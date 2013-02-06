<?php

/**
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Geotools\Coordinate;

/**
 * Coordinate Interface
 *
 * @author Antoine Corcy <contact@sbin.dk>
 */
interface CoordinateInterface
{
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
}
