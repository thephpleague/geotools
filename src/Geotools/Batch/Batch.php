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
use Geocoder\GeocoderInterface;
use Geocoder\Result\Geocoded;
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
     * Set the Geocoder instance to use.
     *
     * @param GeocoderInterface $geocoder The Geocoder instance to use.
     */
    public function __construct(GeocoderInterface $geocoder)
    {
        $this->geocoder = $geocoder;
    }

    /**
     * {@inheritDoc}
     */
    public function geocode($values)
    {
        $geocoder = $this->geocoder;

        foreach ($this->geocoder->getProviders() as $provider) {
            if (is_array($values) && 0 !== count($values)) {
                foreach ($values as $value) {
                    $this->tasks[] = function ($callback) use ($geocoder, $provider, $value) {
                        try {
                            $callback($geocoder->using($provider->getName())->geocode(
                                $value
                            ));
                        } catch (\Exception $e) {
                            $callback(new Geocoded());
                        }
                    };
                }
            } elseif (is_string($values) && '' !== trim($values)) {
                $this->tasks[] = function ($callback) use ($geocoder, $provider, $values) {
                    try {
                        $callback($geocoder->using($provider->getName())->geocode(
                            $values
                        ));
                    } catch (\Exception $e) {
                        $callback(new Geocoded());
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

        foreach ($this->geocoder->getProviders() as $provider) {
            if (is_array($coordinates) && 0 !== count($coordinates)) {
                foreach ($coordinates as $coordinate) {
                    $this->tasks[] = function ($callback) use ($geocoder, $provider, $coordinate) {
                        try {
                            $callback($geocoder->using($provider->getName())->reverse(
                                $coordinate->getLatitude(),
                                $coordinate->getLongitude()
                            ));
                        } catch (\Exception $e) {
                            $callback(new Geocoded());
                        }
                    };
                }
            } elseif ($coordinates instanceOf CoordinateInterface) {
                $this->tasks[] = function ($callback) use ($geocoder, $provider, $coordinates) {
                    try {
                        $callback($geocoder->using($provider->getName())->reverse(
                            $coordinates->getLatitude(),
                            $coordinates->getLongitude()
                        ));
                    } catch (\Exception $e) {
                        $callback(new Geocoded());
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
        $computedInParallel = array();

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
}
