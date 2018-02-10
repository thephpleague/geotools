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

use Geocoder\Location;
use Geocoder\Model\Address;
use Geocoder\Model\Coordinates;

/**
 * BatchGeocoded class
 *
 * @author Antoine Corcy <contact@sbin.dk>
 */
class BatchGeocoded
{
    /**
     * The name of the provider.
     *
     * @var string
     */
    protected $providerName;

    /**
     * The query.
     *
     * @var string
     */
    protected $query;

    /**
     * The exception message.
     *
     * @var string
     */
    protected $exception;

    /**
     * The Location object.
     *
     * @var Location
     */
    protected $address;

    /**
     * Get the name of the provider.
     *
     * @return string The name of the provider.
     */
    public function getProviderName()
    {
        return $this->providerName;
    }

    /**
     * Set the name of the provider.
     *
     * @param string $providerName The name of the provider.
     */
    public function setProviderName($providerName)
    {
        $this->providerName = $providerName;
    }

    /**
     * Get the query.
     *
     * @return string The query.
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Set the query.
     *
     * @param string $query The query.
     */
    public function setQuery($query)
    {
        $this->query = $query;
    }

    /**
     * Get the exception message.
     *
     * @return string The exception message.
     */
    public function getExceptionMessage()
    {
        return $this->exception;
    }

    /**
     * Set the exception message.
     *
     * @param string $exception The exception message.
     */
    public function setExceptionMessage($exception)
    {
        $this->exception = $exception;
    }

    /**
     * Get the address
     *
     * @return Location
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set the address
     *
     * @param Location $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * Returns an array of coordinates (latitude, longitude).
     *
     * @return Coordinates|null
     */
    public function getCoordinates()
    {
        if (null === $this->address) {
            return null;
        }

        return $this->address->getCoordinates();
    }

    /**
     * Returns the latitude value.
     *
     * @return double
     */
    public function getLatitude()
    {
        if (null === $coordinates = $this->getCoordinates()) {
            return null;
        }

        return $coordinates->getLatitude();
    }

    /**
     * Returns the longitude value.
     *
     * @return double
     */
    public function getLongitude()
    {
        if (null === $coordinates = $this->getCoordinates()) {
            return null;
        }

        return $coordinates->getLongitude();
    }

    /**
     * Create an instance from an array, used from cache libraries.
     *
     * @param array $data
     */
    public function fromArray(array $data = [])
    {
        if (isset($data['providerName'])) {
            $this->providerName = $data['providerName'];
        }
        if (isset($data['query'])) {
            $this->query = $data['query'];
        }
        if (isset($data['exception'])) {
            $this->exception = $data['exception'];
        }

        //GeoCoder Address::createFromArray expects longitude/latitude keys
        $data['address']['longitude'] = $data['address']['coordinates']['longitude'] ?? null;
        $data['address']['latitude'] = $data['address']['coordinates']['latitude'] ?? null;

        // Shortcut to create the address and set it in this class
        $this->setAddress(Address::createFromArray($data['address']));
    }

	/**
     * Router all other methods call directly to our address object
     *
     * @param $method
     * @param $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        if (null === $this->address) {
            return null;
        }

        return call_user_func_array([$this->address, $method], $args);
    }
}
