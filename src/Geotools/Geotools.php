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
    const VERSION = '0.1.5.dev';


    /**
     * {@inheritDoc}
     */
    public function from(CoordinateInterface $from)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function to(CoordinateInterface $to)
    {
        $this->to = $to;

        return $this;
    }

    /**
     * Returns an instance of Distance.
     *
     * @return DistanceInterface
     */
    public function distance()
    {
        $distance = new Distance();

        return $distance->setFrom($this->from)->setTo($this->to);
    }

    /**
     * Returns an instance of Point.
     *
     * @return PointInterface
     */
    public function point()
    {
        $point = new Point();

        return $point->setFrom($this->from)->setTo($this->to);
    }

    /**
     * Returns an instance of Batch.
     *
     * @param GeocoderInterface $geocoder The Geocoder instance to use.
     *
     * @return BatchInterface
     */
    public function batch(GeocoderInterface $geocoder)
    {
        return new Batch($geocoder);
    }

    /**
     * Returns an instance of Geohash.
     *
     * @return GeohashInterface
     */
    public function geohash()
    {
        return new Geohash();
    }

    /**
     * Returns an instance of Convert
     *
     * @return ConvertInterface
     */
    public function convert(CoordinateInterface $coordinates)
    {
        return new Convert($coordinates);
    }
}
