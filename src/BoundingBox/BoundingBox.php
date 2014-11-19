<?php
namespace League\Geotools\BoundingBox;

use InvalidArgumentException;
use League\Geotools\Polygon\PolygonInterface;
use League\Geotools\Coordinate\CoordinateInterface;

class BoundingBox implements BoundingBoxInterface
{
    /**
     * @var PolygonInterface
     */
    private $polygon;

    /**
     * The latitude of the north coordinate
     *
     * @var float|string|int
     */
    private $north;

    /**
     * The longitude of the east coordinate
     *
     * @var float|string|int
     */
    private $east;

    /**
     * The latitude of the south coordinate
     *
     * @var float|string|int
     */
    private $south;

    /**
     * The longitude of the west coordinate
     *
     * @var float|string|int
     */
    private $west;

    /**
     * @var bool
     */
    private $hasCoordinate = false;

    /**
     * @var int
     */
    private $precision = 8;

    /**
     * @param PolygonInterface|CoordinateInterface $object
     */
    public function __construct($object = null)
    {
        if ($object instanceof PolygonInterface) {
            $this->setPolygon($object);
        } elseif ($object instanceof CoordinateInterface) {
            $this->addCoordinate($object);
        } else {
            throw new InvalidArgumentException;
        }
    }

    private function createBoundingBoxForPolygon()
    {
        $this->hasCoordinate = false;
        $this->west = $this->east = $this->north = $this->south = null;
        foreach ($this->polygon->getCoordinates() as $coordinate) {
            $this->addCoordinate($coordinate);
        }
    }

    /**
     * @param CoordinateInterface $coordinate
     */
    private function addCoordinate(CoordinateInterface $coordinate)
    {
        $latitude = $coordinate->getLatitude();
        $longitude = $coordinate->getLongitude();

        if (!$this->hasCoordinate) {
            $this->setNorth($latitude);
            $this->setSouth($latitude);
            $this->setEast($longitude);
            $this->setWest($longitude);
        } else {
            if (bccomp($latitude, $this->getSouth(), $this->getPrecision()) === -1) {
                $this->setSouth($latitude);
            }
            if (bccomp($latitude, $this->getNorth(), $this->getPrecision()) === 1) {
                $this->setNorth($latitude);
            }
            if (bccomp($longitude, $this->getEast(), $this->getPrecision()) === 1) {
                $this->setEast($longitude);
            }
            if (bccomp($longitude, $this->getWest(), $this->getPrecision()) === -1) {
                $this->setWest($longitude);
            }
        }
        $this->hasCoordinate = true;
    }

    /**
     * @param CoordinateInterface $coordinate
     * @return bool
     */
    public function pointInBoundingBox(CoordinateInterface $coordinate)
    {
        if (
            bccomp($coordinate->getLatitude(), $this->getSouth(), $this->getPrecision()) === -1 ||
            bccomp($coordinate->getLatitude(), $this->getNorth(), $this->getPrecision()) === 1 ||
            bccomp($coordinate->getLongitude(), $this->getEast(), $this->getPrecision()) === -1 ||
            bccomp($coordinate->getLongitude(), $this->getWest(), $this->getPrecision()) === 1
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
     * @return float|string|int
     */
    public function getNorth()
    {
        return $this->north;
    }

    /**
     * @param float|string|int $north
     * @return $this
     */
    public function setNorth($north)
    {
        $this->north = $north;
        return $this;
    }

    /**
     * @return float|string|int
     */
    public function getEast()
    {
        return $this->east;
    }

    /**
     * @param float|string|int $east
     * @return $this
     */
    public function setEast($east)
    {
        $this->east = $east;
        return $this;
    }

    /**
     * @return float|string|int
     */
    public function getSouth()
    {
        return $this->south;
    }

    /**
     * @param float|string|int $south
     * @return $this
     */
    public function setSouth($south)
    {
        $this->south = $south;
        return $this;
    }

    /**
     * @return float|string|int
     */
    public function getWest()
    {
        return $this->west;
    }

    /**
     * @param float|string|int $west
     * @return $this
     */
    public function setWest($west)
    {
        $this->west = $west;
        return $this;
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
