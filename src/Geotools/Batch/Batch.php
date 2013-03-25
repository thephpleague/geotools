<?php

/**
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Geotools\Batch;

use Geotools\Coordinate\CoordinateInterface;
use Geotools\Exception\InvalidArgumentException;
use Geotools\Batch\BatchResult;
use Geotools\Cache\CacheInterface;
use Geocoder\GeocoderInterface;
use React\Async\Util as Async;

/**
 * Batch class
 *
 * @author Antoine Corcy <contact@sbin.dk>
 */
class Batch implements BatchInterface
{
    /**
     * The Geocoder instance to use.
     *
     * @var GeocoderInterface
     */
    protected $geocoder;

    /**
     * An array of closures.
     *
     * @var array
     */
    protected $tasks;

    /**
     * The cache instance to use.
     *
     * @var CacheInterface
     */
    protected $cache;


    /**
     * Set the Geocoder instance to use.
     *
     * @param GeocoderInterface $geocoder The Geocoder instance to use.
     */
    public function __construct(GeocoderInterface $geocoder)
    {
        $this->geocoder = $geocoder;
    }

    /**
     * Check against the cache instance if any.
     *
     * @param BatchGeocoded $value The BatchGeocoded object to check against the cache instance.
     *
     * @return BatchGeocoded The BatchGeocoded object from the query or the cache instance.
     */
    private function cache(BatchGeocoded $value)
    {
        return isset($this->cache) ? $this->cache->check($value) : $value;
    }

    /**
     * {@inheritDoc}
     */
    public function geocode($values)
    {
        $geocoder = $this->geocoder;

        foreach ($geocoder->getProviders() as $provider) {
            if (is_array($values) && count($values) > 0) {
                foreach ($values as $value) {
                    $this->tasks[] = function ($callback) use ($geocoder, $provider, $value) {
                        try {
                            $geocoder->setResultFactory(new BatchResult($provider->getName(), $value));
                            $callback($this->cache($geocoder->using($provider->getName())->geocode($value)));
                        } catch (\Exception $e) {
                            $batchGeocoded = new BatchResult($provider->getName(), $value, $e->getMessage());
                            $callback($batchGeocoded->newInstance());
                        }
                    };
                }
            } elseif (is_string($values) && '' !== trim($values)) {
                $this->tasks[] = function ($callback) use ($geocoder, $provider, $values) {
                    try {
                        $geocoder->setResultFactory(new BatchResult($provider->getName(), $values));
                        $callback($this->cache($geocoder->using($provider->getName())->geocode($values)));
                    } catch (\Exception $e) {
                        $batchGeocoded = new BatchResult($provider->getName(), $values, $e->getMessage());
                        $callback($batchGeocoded->newInstance());
                    }
                };
            } else {
                throw new InvalidArgumentException(
                    'The argument should be a string or an array of strings to geocode.'
                );
            }
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function reverse($coordinates)
    {
        $geocoder = $this->geocoder;

        foreach ($geocoder->getProviders() as $provider) {
            if (is_array($coordinates) && count($coordinates) > 0) {
                foreach ($coordinates as $coordinate) {
                    $this->tasks[] = function ($callback) use ($geocoder, $provider, $coordinate) {
                        try {
                            $geocoder->setResultFactory(new BatchResult(
                                $provider->getName(),
                                sprintf('%s, %s', $coordinate->getLatitude(), $coordinate->getLongitude())
                            ));
                            $callback($this->cache(
                                $geocoder->using($provider->getName())->reverse(
                                    $coordinate->getLatitude(),
                                    $coordinate->getLongitude()
                                )
                            ));
                        } catch (\Exception $e) {
                            $batchGeocoded = new BatchResult(
                                $provider->getName(),
                                sprintf('%s, %s', $coordinate->getLatitude(), $coordinate->getLongitude()),
                                $e->getMessage()
                            );
                            $callback($batchGeocoded->newInstance());
                        }
                    };
                }
            } elseif ($coordinates instanceOf CoordinateInterface) {
                $this->tasks[] = function ($callback) use ($geocoder, $provider, $coordinates) {
                    try {
                        $geocoder->setResultFactory(new BatchResult(
                            $provider->getName(),
                            sprintf('%s, %s', $coordinates->getLatitude(), $coordinates->getLongitude())
                        ));
                        $callback($this->cache(
                            $geocoder->using($provider->getName())->reverse(
                                $coordinates->getLatitude(),
                                $coordinates->getLongitude()
                            )
                        ));
                    } catch (\Exception $e) {
                        $batchGeocoded = new BatchResult(
                            $provider->getName(),
                            sprintf('%s, %s', $coordinates->getLatitude(), $coordinates->getLongitude()),
                            $e->getMessage()
                        );
                        $callback($batchGeocoded->newInstance());
                    }
                };
            } else {
                throw new InvalidArgumentException(
                    'The argument should be a Coordinate instance or an array of Coordinate instances to reverse.'
                );
            }
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * $this cannot be used in anonymous function in PHP 5.3.x
     * @see http://php.net/manual/en/functions.anonymous.php
     */
    public function serie()
    {
        $computedInSerie = array();

        Async::series(
            $this->tasks,
            function (array $providerResults) use (&$computedInSerie) {
                foreach ($providerResults as $providerResult) {
                    $computedInSerie[] = $providerResult;
                }
            },
            function (\Exception $e) {
                throw $e;
            }
        );

        return $computedInSerie;
    }

    /**
     * {@inheritDoc}
     *
     * $this cannot be used in anonymous function in PHP 5.3.x
     * @see http://php.net/manual/en/functions.anonymous.php
     */
    public function parallel()
    {
        $computedInParallel = array();

        Async::parallel(
            $this->tasks,
            function (array $providerResults) use (&$computedInParallel) {
                foreach ($providerResults as $providerResult) {
                    $computedInParallel[] = $providerResult;
                }
            },
            function (\Exception $e) {
                throw $e;
            }
        );

        return $computedInParallel;
    }

    /**
     * {@inheritDoc}
     */
    public function setCache(CacheInterface $cache) {
        $this->cache = $cache;

        return $this;
    }
}
