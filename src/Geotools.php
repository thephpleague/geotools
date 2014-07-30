<?php

/**
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools;

use League\Geotools\Coordinate\CoordinateInterface;
use League\Geotools\Coordinate\Coordinate;
use League\Geotools\Distance\Distance;
use League\Geotools\Point\Point;
use League\Geotools\Batch\Batch;
use League\Geotools\Geohash\Geohash;
use League\Geotools\Convert\Convert;
use Geocoder\GeocoderInterface;

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
    const VERSION = '0.4.0';


    /**
     * {@inheritDoc}
     */
    public function distance()
    {
        return new Distance();
    }

    /**
     * {@inheritDoc}
     */
    public function point()
    {
        return new Point();
    }

    /**
     * {@inheritDoc}
     */
    public function batch(GeocoderInterface $geocoder)
    {
        return new Batch($geocoder);
    }

    /**
     * {@inheritDoc}
     */
    public function geohash()
    {
        return new Geohash();
    }

    /**
     * {@inheritDoc}
     */
    public function convert(CoordinateInterface $coordinates)
    {
        return new Convert($coordinates);
    }
}
