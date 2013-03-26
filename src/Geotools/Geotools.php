<?php

/**
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Geotools;

use Geotools\Coordinate\CoordinateInterface;
use Geotools\Coordinate\Coordinate;
use Geotools\Distance\Distance;
use Geotools\Point\Point;
use Geotools\Batch\Batch;
use Geotools\Geohash\Geohash;
use Geotools\Convert\Convert;
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
    const VERSION = '0.2.2';


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
