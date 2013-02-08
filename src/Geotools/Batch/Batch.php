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
     * An array of ResultInterface.
     *
     * @var array
     */
    protected $providerResults;


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
        foreach ($this->geocoder->getProviders() as $provider) {
            $this->tasks[$provider->getName()] = function ($callback) use ($provider, $value) {
                try {
                    $callback($this->geocoder->using($provider->getName())->geocode(
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
        foreach ($this->geocoder->getProviders() as $provider) {
            $this->tasks[$provider->getName()] = function ($callback) use ($provider, $coordinate) {
                try {
                    $callback($this->geocoder->using($provider->getName())->reverse(
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
     * @todo Make a patch to React/Async to return the provider name form the callback
     */
    public function serie()
    {
        Async::series(
            $this->tasks,
            function (array $providerResults) {
                foreach ($providerResults as $providerName => $providerResult) {
                    $this->providerResults[$providerName] = $providerResult;
                }
            },
            function (\Exception $e) {
                throw $e;
            }
        );

        return $this->providerResults;
    }

    /**
     * {@inheritDoc}
     */
    public function parallel()
    {
        Async::parallel(
            $this->tasks,
            function (array $providerResults) {
                foreach ($providerResults as $providerName => $providerResult) {
                    $this->providerResults[$providerName] = $providerResult;
                }
            },
            function (\Exception $e) {
                throw $e;
            }
        );

        return $this->providerResults;
    }
}
