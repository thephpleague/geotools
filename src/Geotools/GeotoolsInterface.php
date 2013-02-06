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

/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
interface GeotoolsInterface
{
    /**
     * Set the origin coordinate.
     *
     * @param  CoordinateInterface $from The origin coordinate.
     *
     * @return GeotoolsInterface
     */
    public function from(CoordinateInterface $from);

    /**
     * Set the destination coordinate.
     *
     * @param  CoordinateInterface $to The destination coordinate.
     *
     * @return GeotoolsInterface
     */
    public function to(CoordinateInterface $to);
}
