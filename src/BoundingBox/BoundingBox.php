<?php
namespace League\Geotools\BoundingBox;

use InvalidArgumentException;
use League\Geotools\Coordinate\Coordinate;
use League\Geotools\Polygon\PolygonInterface;
use League\Geotools\Coordinate\CoordinateInterface;

class BoundingBox implements BoundingBoxInterface
{
    /**
     * @var PolygonInterface
     */
    private $polygon;

    /**
     * @var CoordinateInterface
     */
    private $nortWestCorner;

    /**
     * @var CoordinateInterface
     */
    private $southEastCorner;

    /**
     * @var bool
     */
    private $hasCoordinate = false;

    /**
     * @var int
     */
    private $precision = 8;

    /**
     * @param PolygonInterface$object
     */
    public function __construct($object = null)
    {
        $this->nortWestCorner = new Coordinate(array(0, 0));
        $this->southEastCorner = new Coordinate(array(0, 0));
        if ($object instanceof PolygonInterface) {
            $this->setPolygon($object);
        } else {
            throw new InvalidArgumentException;
        }
    }

    private function createBoundingBoxForPolygon()
    {
        $this->hasCoordinate = false;
        $this->nortWestCorner = new Coordinate(array(0,0));
        $this->southEastCorner = new Coordinate(array(0,0));
        foreach ($this->polygon->getCoordinates() as $coordinate) {
            $this->compareMaximumAndMinimum($coordinate);
            $this->hasCoordinate = true;
        }
    }

    /**
     * @param CoordinateInterface $coordinate
     */
    private function compareMaximumAndMinimum(CoordinateInterface $coordinate)
    {
        if (!$this->hasCoordinate) {
            $this->nortWestCorner->setLatitude($coordinate->getLatitude());
            $this->nortWestCorner->setLongitude($coordinate->getLongitude());
            $this->southEastCorner->setLatitude($coordinate->getLatitude());
            $this->southEastCorner->setLongitude($coordinate->getLongitude());
        } else {
            $latitude = $coordinate->getLatitude();
            $longitude = $coordinate->getLongitude();

            if ($latitude < $this->southEastCorner->getLatitude()) {
                $this->southEastCorner->setLatitude($latitude);
            }

            if ($latitude > $this->nortWestCorner->getLatitude()) {
                $this->nortWestCorner->setLatitude($latitude);
            }

            if ($longitude < $this->southEastCorner->getLongitude()) {
                $this->southEastCorner->setLongitude($longitude);
            }

            if ($longitude > $this->nortWestCorner->getLongitude()) {
                $this->nortWestCorner->setLongitude($longitude);
            }
        }
    }

    /**
     * @param CoordinateInterface $coordinate
     * @return bool
     */
    public function pointInBoundingBox(CoordinateInterface $coordinate)
    {
        if (
            bccomp(
                $coordinate->getLatitude(),
                $this->southEastCorner->getLatitude(),
                $this->getPrecision()
            ) === -1 ||
            bccomp(
                $coordinate->getLatitude(),
                $this->nortWestCorner->getLatitude(),
                $this->getPrecision()
            ) === 1 ||
            bccomp(
                $coordinate->getLongitude(),
                $this->southEastCorner->getLongitude(),
                $this->getPrecision()
            ) === -1 ||
            bccomp(
                $coordinate->getLongitude(),
                $this->nortWestCorner->getLongitude(),
                $this->getPrecision()
            ) === 1
        ) {
            return false;
        }

        return true;
    }

    /**
     * @return PolygonInterface
     */
    public function getPolygon()
    {
        return $this->polygon;
    }

    /**
     * @param PolygonInterface $polygon
     * @return $this
     */
    public function setPolygon(PolygonInterface $polygon)
    {
        $this->polygon = $polygon;
        $this->createBoundingBoxForPolygon();
        return $this;
    }

    /**
     * @return CoordinateInterface
     */
    public function getSouthWestCoordinate()
    {
        return new Coordinate(array(
            $this->southEastCorner->getLatitude(),
            $this->nortWestCorner->getLongitude()
        ));
    }

    /**
     * @return CoordinateInterface
     */
    public function getSouthEastCoordinate()
    {
        return $this->southEastCorner;
    }

    /**
     * @return CoordinateInterface
     */
    public function getNorthWestCoordinate()
    {
        return $this->nortWestCorner;
    }

    /**
     * @return CoordinateInterface
     */
    public function getNorthEastCoordinate()
    {
        return new Coordinate(array(
            $this->nortWestCorner->getLatitude(),
            $this->southEastCorner->getLongitude()
        ));
    }

    /**
     * @return int
     */
    public function getPrecision()
    {
        return $this->precision;
    }

    /**
     * @param int $precision
     * @return $this
     */
    public function setPrecision($precision)
    {
        $this->precision = $precision;
        return $this;
    }
}
