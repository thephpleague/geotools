<?php

/*
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools\Batch;

use Geocoder\Geocoder;
use Geocoder\ProviderAggregator;
use League\Geotools\Coordinate\CoordinateInterface;
use League\Geotools\Exception\InvalidArgumentException;
use Psr\Cache\CacheItemPoolInterface;
use React\EventLoop\Factory as EventLoopFactory;
use React\Promise\Deferred;

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
     * @var ProviderAggregator
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
     * @var CacheItemPoolInterface
     */
    protected $cache;

    /**
     * Set the Geocoder instance to use.
     *
     * @param ProviderAggregator $geocoder The Geocoder instance to use.
     */
    public function __construct(ProviderAggregator $geocoder)
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
        if (null === $this->cache) {
            return false;
        }

        $item = $this->cache->getItem($this->getCacheKey($providerName, $query));

        if ($item->isHit()) {
            return $item->get();
        }

        return false;
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
            $key = $this->getCacheKey($geocoded->getProviderName(), $geocoded->getQuery());
            $item = $this->cache->getItem($key);
            $item->set($geocoded);
            $this->cache->save($item);
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
                    $this->tasks[] = function () use ($geocoder, $provider, $value, $cache) {
                        $deferred = new Deferred;

                        try {
                            if ($cached = $cache->isCached($provider->getName(), $value)) {
                                $deferred->resolve($cached);
                            } else {
                                $batchResult = new BatchResult($provider->getName(), $value);
                                $address = $geocoder->using($provider->getName())->geocode($value)->first();
                                $deferred->resolve($cache->cache($batchResult->createFromAddress($address)));
                            }
                        } catch (\Exception $e) {
                            $batchGeocoded = new BatchResult($provider->getName(), $value, $e->getMessage());
                            $deferred->resolve($batchGeocoded->newInstance());
                        }

                        return $deferred->promise();
                    };
                }
            } elseif (is_string($values) && '' !== trim($values)) {
                $this->tasks[] = function () use ($geocoder, $provider, $values, $cache) {
                    $deferred = new Deferred;

                    try {
                        if ($cached = $cache->isCached($provider->getName(), $values)) {
                            $deferred->resolve($cached);
                        } else {
                            $batchResult = new BatchResult($provider->getName(), $values);
                            $address = $geocoder->using($provider->getName())->geocode($values)->first();
                            $deferred->resolve($cache->cache($batchResult->createFromAddress($address)));
                        }
                    } catch (\Exception $e) {
                        $batchGeocoded = new BatchResult($provider->getName(), $values, $e->getMessage());
                        $deferred->resolve($batchGeocoded->newInstance());
                    }

                    return $deferred->promise();
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
                    $this->tasks[] = function () use ($geocoder, $provider, $coordinate, $cache) {
                        $deferred = new Deferred();

                        $valueCoordinates = sprintf('%s, %s', $coordinate->getLatitude(), $coordinate->getLongitude());
                        try {
                            if ($cached = $cache->isCached($provider->getName(), $valueCoordinates)) {
                                $deferred->resolve($cached);
                            } else {
                                $batchResult = new BatchResult($provider->getName(), $valueCoordinates);
                                $address = $geocoder->using($provider->getName())->reverse(
                                        $coordinate->getLatitude(),
                                        $coordinate->getLongitude()
                                    )->first();

                                $deferred->resolve($cache->cache($batchResult->createFromAddress($address)));
                            }
                        } catch (\Exception $e) {
                            $batchGeocoded = new BatchResult($provider->getName(), $valueCoordinates, $e->getMessage());
                            $deferred->resolve($batchGeocoded->newInstance());
                        }

                        return $deferred->promise();
                    };
                }
            } elseif ($coordinates instanceOf CoordinateInterface) {
                $this->tasks[] = function () use ($geocoder, $provider, $coordinates, $cache) {
                    $deferred = new Deferred();

                    $valueCoordinates = sprintf('%s, %s', $coordinates->getLatitude(), $coordinates->getLongitude());
                    try {
                        if ($cached = $cache->isCached($provider->getName(), $valueCoordinates)) {
                            $deferred->resolve($cached);
                        } else {
                            $batchResult = new BatchResult($provider->getName(), $valueCoordinates);
                            $address = $geocoder->using($provider->getName())->reverse(
                                    $coordinates->getLatitude(),
                                    $coordinates->getLongitude()
                                )->first();
                            $deferred->resolve($cache->cache($batchResult->createFromAddress($address)));
                        }
                    } catch (\Exception $e) {
                        $batchGeocoded = new BatchResult($provider->getName(), $valueCoordinates, $e->getMessage());
                        $deferred->resolve($batchGeocoded->newInstance());
                    }

                    return $deferred->promise();
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

        foreach ($this->tasks as $task) {
            $task()->then(function($result) use (&$computedInSerie) {
                $computedInSerie[] = $result;
            }, function ($emptyResult) use (&$computedInSerie) {
                $computedInSerie[] = $emptyResult;
            });
        }

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
        $loop = EventLoopFactory::create();
        $computedInParallel = array();

        foreach ($this->tasks as $task) {
            $loop->futureTick(function () use ($task, &$computedInParallel) {
                $task()->then(function($result) use (&$computedInParallel) {
                    $computedInParallel[] = $result;
                }, function ($emptyResult) use (&$computedInParallel) {
                    $computedInParallel[] = $emptyResult;
                });
            });
        }

        $loop->run();

        return $computedInParallel;
    }

    /**
     * {@inheritDoc}
     */
    public function setCache(CacheItemPoolInterface $cache)
    {
        $this->cache = $cache;

        return $this;
    }

    private function getCacheKey(string $providerName, string $query): string
    {
        return sha1($providerName.'-'.$query);
    }
}
