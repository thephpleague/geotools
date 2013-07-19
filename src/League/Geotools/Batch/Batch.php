<?php

/**
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools\Batch;

use League\Geotools\Coordinate\CoordinateInterface;
use League\Geotools\Exception\InvalidArgumentException;
use League\Geotools\Batch\BatchResult;
use League\Geotools\Cache\CacheInterface;
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
     * @param string $providerName The name of the provider.
     * @param string $query        The query string.
     *
     * @return boolean|BatchGeocoded The BatchGeocoded object from the query or the cache instance.
     */
    public function isCached($providerName, $query)
    {
        return isset($this->cache) ? $this->cache->isCached($providerName, $query) : false;
    }

    /**
     * Cache the BatchGeocoded object.
     *
     * @param BatchGeocoded $geocoded The BatchGeocoded object to cache.
     *
     * @return BatchGeocoded The BatchGeocoded object.
     */
    public function cache(BatchGeocoded $geocoded)
    {
        if (isset($this->cache)) {
            $this->cache->cache($geocoded);
        }

        return $geocoded;
    }

    /**
     * {@inheritDoc}
     */
    public function geocode($values)
    {
        $geocoder = $this->geocoder;
        $cache    = $this;

        foreach ($geocoder->getProviders() as $provider) {
            if (is_array($values) && count($values) > 0) {
                foreach ($values as $value) {
                    $this->tasks[] = function ($callback) use ($geocoder, $provider, $value, $cache) {
                        try {
                            if ($cached = $cache->isCached($provider->getName(), $value)) {
                                $callback($cached);
                            } else {
                                $geocoder->setResultFactory(new BatchResult($provider->getName(), $value));
                                $callback($cache->cache($geocoder->using($provider->getName())->geocode($value)));
                            }
                        } catch (\Exception $e) {
                            $batchGeocoded = new BatchResult($provider->getName(), $value, $e->getMessage());
                            $callback($batchGeocoded->newInstance());
                        }
                    };
                }
            } elseif (is_string($values) && '' !== trim($values)) {
                $this->tasks[] = function ($callback) use ($geocoder, $provider, $values, $cache) {
                    try {
                        if ($cached = $cache->isCached($provider->getName(), $values)) {
                            $callback($cached);
                        } else {
                            $geocoder->setResultFactory(new BatchResult($provider->getName(), $values));
                            $callback($cache->cache($geocoder->using($provider->getName())->geocode($values)));
                        }
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
        $cache    = $this;

        foreach ($geocoder->getProviders() as $provider) {
            if (is_array($coordinates) && count($coordinates) > 0) {
                foreach ($coordinates as $coordinate) {
                    $this->tasks[] = function ($callback) use ($geocoder, $provider, $coordinate, $cache) {
                        $valueCoordinates = sprintf('%s, %s', $coordinate->getLatitude(), $coordinate->getLongitude());
                        try {
                            if ($cached = $cache->isCached($provider->getName(), $valueCoordinates)) {
                                $callback($cached);
                            } else {
                                $geocoder->setResultFactory(new BatchResult($provider->getName(), $valueCoordinates));
                                $callback($cache->cache(
                                    $geocoder->using($provider->getName())->reverse(
                                        $coordinate->getLatitude(),
                                        $coordinate->getLongitude()
                                    )
                                ));
                            }
                        } catch (\Exception $e) {
                            $batchGeocoded = new BatchResult($provider->getName(), $valueCoordinates, $e->getMessage());
                            $callback($batchGeocoded->newInstance());
                        }
                    };
                }
            } elseif ($coordinates instanceOf CoordinateInterface) {
                $this->tasks[] = function ($callback) use ($geocoder, $provider, $coordinates, $cache) {
                    $valueCoordinates = sprintf('%s, %s', $coordinates->getLatitude(), $coordinates->getLongitude());
                    try {
                        if ($cached = $cache->isCached($provider->getName(), $valueCoordinates)) {
                            $callback($cached);
                        } else {
                            $geocoder->setResultFactory(new BatchResult($provider->getName(), $valueCoordinates));
                            $callback($cache->cache(
                                $geocoder->using($provider->getName())->reverse(
                                    $coordinates->getLatitude(),
                                    $coordinates->getLongitude()
                                )
                            ));
                        }
                    } catch (\Exception $e) {
                        $batchGeocoded = new BatchResult($provider->getName(), $valueCoordinates, $e->getMessage());
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
    public function setCache(CacheInterface $cache)
    {
        $this->cache = $cache;

        return $this;
    }
}
