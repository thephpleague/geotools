<?php

/*
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools\Polygon;

use League\Geotools\Coordinate\CoordinateInterface;
use League\Geotools\GeometryInterface;

/**
 * @author Gabriel Bull <me@gabrielbull.com>
 */
interface PolygonInterface extends GeometryInterface
{
    /**
     * @param  CoordinateInterface $coordinate
     * @return boolean
     */
    public function pointInPolygon(CoordinateInterface $coordinate);

    /**
     * @param  CoordinateInterface $coordinate
     * @return boolean
     */
    public function pointOnBoundary(CoordinateInterface $coordinate);

    /**
     * @param  CoordinateInterface $coordinate
     * @return boolean
     */
    public function pointOnVertex(CoordinateInterface $coordinate);

    /**
     * Returns the geographic centroid of the polygon's coordinates,
     * calculated by averaging x/y/z unit-sphere vectors.
     *
     * @return CoordinateInterface|null  null when the polygon is empty
     */
    public function getCenter(): ?CoordinateInterface;

    /**
     * Returns the radius of the polygon in meters, defined as the maximum
     * haversine distance from the centroid to any vertex.
     *
     * @return float
     */
    public function getRadius(): float;
}
