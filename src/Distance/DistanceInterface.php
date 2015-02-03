<?php

/*
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools\Distance;

use League\Geotools\Coordinate\CoordinateInterface;

/**
 * Distance interface
 *
 * @author Antoine Corcy <contact@sbin.dk>
 */
interface DistanceInterface
{
    /**
     * Set the origin coordinate
     *
     * @param CoordinateInterface $from The origin coordinate
     *
     * @return DistanceInterface
     */
    public function setFrom(CoordinateInterface $from);

    /**
     * Get the origin coordinate
     *
     * @return CoordinateInterface
     */
    public function getFrom();

    /**
     * Set the destination coordinate
     *
     * @param CoordinateInterface $to The destination coordinate
     *
     * @return DistanceInterface
     */
    public function setTo(CoordinateInterface $to);

    /**
     * Get the destination coordinate
     *
     * @return CoordinateInterface
     */
    public function getTo();

    /**
     * Set the user unit
     *
     * @param string $unit Set the unit
     *
     * @return DistanceInterface
     */
    public function in($unit);
}
