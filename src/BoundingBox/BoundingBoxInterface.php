<?php
namespace League\Geotools\BoundingBox;

use League\Geotools\Coordinate\CoordinateInterface;

interface BoundingBoxInterface
{
    /**
     * @return float|string|int
     */
    public function getNorth();

    /**
     * @return float|string|int
     */
    public function getEast();

    /**
     * @return float|string|int
     */
    public function getSouth();

    /**
     * @return float|string|int
     */
    public function getWest();
}
