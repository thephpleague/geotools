<?php

/*
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools;

use League\Geotools\Coordinate\CoordinateInterface;

/**
 * Coordinate couple Trait
 *
 * @author Rémi San <remi.san@gmail.com>
 */
trait CoordinateCouple
{
    /**
     * The origin coordinate.
     *
     * @var CoordinateInterface
     */
    protected $from;

    /**
     * The destination coordinate.
     *
     * @var CoordinateInterface
     */
    protected $to;
}
