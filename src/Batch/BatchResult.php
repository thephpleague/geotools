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

/**
 * BatchResult class
 *
 * @author Antoine Corcy <contact@sbin.dk>
 */
class BatchResult
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
     * Construct a Geocoded object with the provider name, its query and exception if any.
     *
     * @param string $providerName The name of the provider.
     * @param string $query        The query.
     * @param string $exception    The exception message if any.
     */
    public function __construct($providerName, $query, $exception = '')
    {
        $this->providerName = $providerName;
        $this->query        = $query;
        $this->exception    = $exception;
    }

    /**
     * {@inheritDoc}
     */
    public function createFromAddress(Location $address)
    {
        $result = $this->newInstance();
        $result->setAddress($address);

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function newInstance()
    {
        $batchGeocoded = new BatchGeocoded;

        $batchGeocoded->setProviderName($this->providerName);
        $batchGeocoded->setQuery($this->query);
        $batchGeocoded->setExceptionMessage($this->exception);

        return $batchGeocoded;
    }
}
