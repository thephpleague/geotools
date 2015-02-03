<?php

/*
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools\Coordinate;

/**
 * Coordinate Interface
 *
 * @author Antoine Corcy <contact@sbin.dk>
 */
interface CoordinateInterface
{
    /**
     * Normalizes a latitude to the (-90, 90) range.
     * Latitudes below -90.0 or above 90.0 degrees are capped, not wrapped.
     *
     * @param double $latitude The latitude to normalize
     *
     * @return double
     */
    public function normalizeLatitude($latitude);

    /**
     * Normalizes a longitude to the (-180, 180) range.
     * Longitudes below -180.0 or abode 180.0 degrees are wrapped.
     *
     * @param double $longitude The longitude to normalize
     *
     * @return double
     */
    public function normalizeLongitude($longitude);

    /**
     * Set the latitude.
     *
     * @param double $latitude
     */
    public function setLatitude($latitude);

    /**
     * Get the latitude.
     *
     * @return double
     */
    public function getLatitude();

    /**
     * Set the longitude.
     *
     * @param double $longitude
     */
    public function setLongitude($longitude);

    /**
     * Get the longitude.
     *
     * @return double
     */
    public function getLongitude();

    /**
     * Get the Ellipsoid.
     *
     * @return Ellipsoid
     */
    public function getEllipsoid();
}
