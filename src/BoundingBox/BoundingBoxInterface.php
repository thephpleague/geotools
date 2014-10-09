<?php
namespace League\Geotools\BoundingBox;

use League\Geotools\Coordinate\CoordinateInterface;

interface BoundingBoxInterface
{
    /**
     * @return CoordinateInterface
     */
    public function getSouthWestCoordinate();

    /**
     * @return CoordinateInterface
     */
    public function getSouthEastCoordinate();

    /**
     * @return CoordinateInterface
     */
    public function getNorthWestCoordinate();

    /**
     * @return CoordinateInterface
     */
    public function getNorthEastCoordinate();
}
