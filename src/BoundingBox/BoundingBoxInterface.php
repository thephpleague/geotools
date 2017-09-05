<?php

/*
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools\BoundingBox;

use League\Geotools\Polygon\PolygonInterface;

/**
 * @author Gabriel Bull <me@gabrielbull.com>
 */
interface BoundingBoxInterface
{
    /**
     * @return float|string|integer
     */
    public function getNorth();

    /**
     * @return float|string|integer
     */
    public function getEast();

    /**
     * @return float|string|integer
     */
    public function getSouth();

    /**
     * @return float|string|integer
     */
    public function getWest();

    /**
     * @return PolygonInterface
     */
    public function getAsPolygon();

    /**
     * @param  BoundingBoxInterface $boundingBox
     * @return BoundingBoxInterface
     */
    public function merge(BoundingBoxInterface $boundingBox);
}
