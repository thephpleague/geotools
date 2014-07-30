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

use Geocoder\Result\Geocoded;

/**
 * BatchGeocoded class
 *
 * @author Antoine Corcy <contact@sbin.dk>
 */
class BatchGeocoded extends Geocoded
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
     * {@inheritDoc}
     */
    public function fromArray(array $data = array())
    {
        parent::fromArray($data);

        if (isset($data['providerName'])) {
            $this->providerName = $this->formatString($data['providerName']);
        }

        if (isset($data['query'])) {
            $this->query = $this->formatString($data['query']);
        }

        if (isset($data['exception'])) {
            $this->exception = $this->formatString($data['exception']);
        }
    }
}
