<?php

/*
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools\Vertex;

use League\Geotools\Coordinate\CoordinateInterface;

/**
 * Vertex interface
 *
 * @author Antoine Corcy <contact@sbin.dk>
 */
interface VertexInterface
{
    /**
     * Set the origin coordinate.
     *
     * @param CoordinateInterface $from The origin coordinate.
     *
     * @return VertexInterface
     */
    public function setFrom(CoordinateInterface $from);

    /**
     * Get the origin coordinate.
     *
     * @return CoordinateInterface
     */
    public function getFrom();

    /**
     * Set the destination coordinate.
     *
     * @param CoordinateInterface $to The destination coordinate.
     *
     * @return VertexInterface
     */
    public function setTo(CoordinateInterface $to);

    /**
     * Get the destination coordinate.
     *
     * @return CoordinateInterface
     */
    public function getTo();

    /**
     * Get the gradient (slope) of the vertex.
     *
     * @return integer
     */
    public function getGradient();

    /**
     * Get the ordinate (longitude) of the point where vertex intersects with the ordinate-axis (Prime-Meridian) of the coordinate system.
     *
     * @return integer
     */
    public function getOrdinateIntercept();
}
