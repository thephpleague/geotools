<?php

/*
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools;

use Geocoder\Geocoder;
use League\Geotools\Batch\Batch;
use League\Geotools\Convert\Convert;
use League\Geotools\Coordinate\CoordinateInterface;
use League\Geotools\Distance\Distance;
use League\Geotools\Edge\Edge;
use League\Geotools\Geohash\Geohash;

/**
 * Geotools class
 *
 * @author Antoine Corcy <contact@sbin.dk>
 */
class Geotools extends AbstractGeotools implements GeotoolsInterface
{
    /**
     * Version.
     * @see http://semver.org/
     */
    const VERSION = '0.7.1-dev';


    /**
     * {@inheritDoc}
     */
    public function distance()
    {
        return new Distance;
    }

    /**
     * {@inheritDoc}
     */
    public function edge()
    {
        return new Edge;
    }

    /**
     * {@inheritDoc}
     */
    public function batch(Geocoder $geocoder)
    {
        return new Batch($geocoder);
    }

    /**
     * {@inheritDoc}
     */
    public function geohash()
    {
        return new Geohash;
    }

    /**
     * {@inheritDoc}
     */
    public function convert(CoordinateInterface $coordinates)
    {
        return new Convert($coordinates);
    }
}
