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

use Geocoder\Model\Address;
use Geocoder\Model\AddressFactory;

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
     * The address object.
     *
     * @var Address
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
     * @return Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set the address
     *
     * @param Address $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * Returns an array of coordinates (latitude, longitude).
     *
     * @return Coordinates
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
        if (null === $this->address) {
            return null;
        }

        return $this->address->getLatitude();
    }

    /**
     * Returns the longitude value.
     *
     * @return double
     */
    public function getLongitude()
    {
        if (null === $this->address) {
            return null;
        }

        return $this->address->getLongitude();
    }

    /**
     * Create an instance from an array, used from cache libraries.
     *
     * @param array $data
     */
    public function fromArray(array $data = array())
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

        // Shortcut to create the address and set it in this class
        $addressFactory = new AddressFactory();
        $this->setAddress($addressFactory->createFromArray([$data])->first());
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

        return call_user_func_array(array($this->address, $method), $args);
    }

}
