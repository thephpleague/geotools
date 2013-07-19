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
use Geocoder\GeocoderInterface;

/**
 * Geotools interface
 *
 * @author Antoine Corcy <contact@sbin.dk>
 */
interface GeotoolsInterface
{
    /**
     * Returns an instance of Distance.
     *
     * @return DistanceInterface
     */
    public function distance();

    /**
     * Returns an instance of Point.
     *
     * @return PointInterface
     */
    public function point();

    /**
     * Returns an instance of Batch.
     *
     * @param GeocoderInterface $geocoder The Geocoder instance to use.
     *
     * @return BatchInterface
     */
    public function batch(GeocoderInterface $geocoder);

    /**
     * Returns an instance of Geohash.
     *
     * @return GeohashInterface
     */
    public function geohash();

    /**
     * Returns an instance of Convert.
     *
     * @param CoordinateInterface $coordinates The coordinates to convert.
     *
     * @return ConvertInterface
     */
    public function convert(CoordinateInterface $coordinates);
}
