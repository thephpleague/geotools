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

    /**
     * @return integer
     */
    public function getPrecision();

    /**
     * @param  integer $precision
     * @return $this
     */
    public function setPrecision($precision);

    /**
     * Returns the initial bearing from the origin coordinate
     * to the destination coordinate in degrees.
     *
     * @return float The initial bearing in degrees
     */
    public function initialBearing();

    /**
     * Returns the final bearing from the origin coordinate
     * to the destination coordinate in degrees.
     *
     * @return float The final bearing in degrees
     */
    public function finalBearing();

    /**
     * Returns the initial cardinal point / direction from the origin coordinate to
     * the destination coordinate.
     * @see http://en.wikipedia.org/wiki/Cardinal_direction
     *
     * @return string The initial cardinal point / direction
     */
    public function initialCardinal();

    /**
     * Returns the final cardinal point / direction from the origin coordinate to
     * the destination coordinate.
     * @see http://en.wikipedia.org/wiki/Cardinal_direction
     *
     * @return string The final cardinal point / direction
     */
    public function finalCardinal();

    /**
     * Returns the half-way point / coordinate along a great circle
     * path between the origin and the destination coordinates.
     *
     * @return CoordinateInterface
     */
    public function middle();

    /**
     * Returns the destination point with a given bearing in degrees travelling along a
     * (shortest distance) great circle arc and a distance in meters.
     *
     * @param integer $bearing  The bearing of the origin in degrees.
     * @param integer $distance The distance from the origin in meters.
     *
     * @return CoordinateInterface
     */
    public function destination($bearing, $distance);

    /**
     * Returns true if the vertex passed on argument is on the same line as this object
     *
     * @param  Vertex  $vertex The vertex to compare
     * @return boolean
     */
    public function isOnSameLine(Vertex $vertex);

    /**
     * Returns the other coordinate who is not the coordinate passed on argument
     * @param  CoordinateInterface $coordinate
     * @return null|Coordinate
     */
    public function getOtherCoordinate(CoordinateInterface $coordinate);

    /**
     * Returns the determinant value between $this (vertex) and another vertex.
     *
     * @param  Vertex $vertex [description]
     * @return [type]         [description]
     */
    public function getDeterminant(Vertex $vertex);

}
