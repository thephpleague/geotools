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
    public function geocode($value)
    {
        $geocoder = $this->geocoder;

        foreach ($this->geocoder->getProviders() as $provider) {
            $this->tasks[$provider->getName()] = function ($callback) use ($geocoder, $provider, $value) {
                try {
                    $callback($geocoder->using($provider->getName())->geocode(
                        $value
                    ));
                } catch (\Exception $e) {
                    $callback(new Geocoded());
                }
            };
        }

        return $this;
    }


    /**
     * {@inheritDoc}
     */
    public function reverse(CoordinateInterface $coordinate)
    {
        $geocoder = $this->geocoder;

        foreach ($this->geocoder->getProviders() as $provider) {
            $this->tasks[$provider->getName()] = function ($callback) use ($geocoder, $provider, $coordinate) {
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

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * $this cannot be used in anonymous function in PHP 5.3.x
     * @see http://php.net/manual/en/functions.anonymous.php
     *
     * @todo Make a patch to React/Async to return the provider name form the callback
     */
    public function serie()
    {
        $computedInParallel = array();

        Async::series(
            $this->tasks,
            function (array $providerResults) use (&$computedInSerie) {
                foreach ($providerResults as $providerName => $providerResult) {
                    $computedInSerie[$providerName] = $providerResult;
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
                foreach ($providerResults as $providerName => $providerResult) {
                    $computedInParallel[$providerName] = $providerResult;
                }
            },
            function (\Exception $e) {
                throw $e;
            }
        );

        return $computedInParallel;
    }
}
