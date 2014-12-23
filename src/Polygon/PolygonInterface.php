<?php

/**
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools\Polygon;

use League\Geotools\Coordinate\CoordinateCollection;
use League\Geotools\Coordinate\CoordinateInterface;

/**
 * @author Gabriel Bull <me@gabrielbull.com>
 */
interface PolygonInterface
{
    /**
     * @return CoordinateCollection
     */
    public function getCoordinates();

    /**
     * @param  CoordinateCollection $coordinates
     * @return $this
     */
    public function setCoordinates(CoordinateCollection $coordinates);

    /**
     * @param  string                   $key
     * @return null|CoordinateInterface
     */
    public function get($key);

    /**
     * @param string              $key
     * @param CoordinateInterface $coordinate
     */
    public function set($key, CoordinateInterface $coordinate);

    /**
     * @param  CoordinateInterface $coordinate
     * @return boolean
     */
    public function add(CoordinateInterface $coordinate);

    /**
     * @param  string                   $key
     * @return null|CoordinateInterface
     */
    public function remove($key);
}
