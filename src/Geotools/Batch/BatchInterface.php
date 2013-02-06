<?php

/**
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Geotools\Batch;

use Geotools\Coordinate\CoordinateInterface;

/**
* @author Antoine Corcy <contact@sbin.dk>
*/
interface BatchInterface
{
    /**
     * Set an array of closures to geocode.
     * If a provider throws an exception it will return an empty ResultInterface.
     *
     * @param  string $value A value to geocode.
     *
     * @return BatchInterface
     */
    public function geocode($value);

    /**
     * Set an array of closures to reverse geocode.
     * If a provider throws an exception it will return an empty ResultInterface.
     *
     * @param  CoordinateInterface $coordinate A coordinate to reverse.
     *
     * @return BatchInterface
     */
    public function reverse(CoordinateInterface $coordinate);

    /**
     * Returns an array of ResultInterface processed in serie.
     *
     * @throws \Exception
     *
     * @return ResultInterface[]
     */
    public function serie();

    /**
     * Returns an array of ResultInterface processed in parallel.
     *
     * @throws \Exception
     *
     * @return ResultInterface[]
     */
    public function parallel();
}
