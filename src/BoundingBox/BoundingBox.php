<?php

/**
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools\BoundingBox;

use League\Geotools\Polygon\PolygonInterface;
use League\Geotools\Coordinate\CoordinateInterface;

/**
 * @author Gabriel Bull <me@gabrielbull.com>
 */
class BoundingBox implements BoundingBoxInterface
{
    /**
     * @var PolygonInterface
     */
    private $polygon;

    /**
     * The latitude of the north coordinate
     *
     * @var float|string|integer
     */
    private $north;

    /**
     * The longitude of the east coordinate
     *
     * @var float|string|integer
     */
    private $east;

    /**
     * The latitude of the south coordinate
     *
     * @var float|string|integer
     */
    private $south;

    /**
     * The longitude of the west coordinate
     *
     * @var float|string|integer
     */
    private $west;

    /**
     * @var boolean
     */
    private $hasCoordinate = false;

    /**
     * @var integer
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
        } elseif (null !== $object) {
            throw new \InvalidArgumentException;
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
        $latitude  = $coordinate->getLatitude();
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
     * @param  CoordinateInterface $coordinate
     * @return bool
     */
    public function pointInBoundingBox(CoordinateInterface $coordinate)
    {
        if (
            bccomp($coordinate->getLatitude(), $this->getSouth(), $this->getPrecision()) === -1 ||
            bccomp($coordinate->getLatitude(), $this->getNorth(), $this->getPrecision()) === 1 ||
            bccomp($coordinate->getLongitude(), $this->getEast(), $this->getPrecision()) === 1 ||
            bccomp($coordinate->getLongitude(), $this->getWest(), $this->getPrecision()) === -1
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
     * @param  PolygonInterface $polygon
     * @return $this
     */
    public function setPolygon(PolygonInterface $polygon)
    {
        $this->polygon = $polygon;
        $this->createBoundingBoxForPolygon();

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getNorth()
    {
        return $this->north;
    }

    /**
     * @param  float|string|integer $north
     * @return $this
     */
    public function setNorth($north)
    {
        $this->north = $north;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getEast()
    {
        return $this->east;
    }

    /**
     * @param  float|string|integer $east
     * @return $this
     */
    public function setEast($east)
    {
        $this->east = $east;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getSouth()
    {
        return $this->south;
    }

    /**
     * @param  float|string|integer $south
     * @return $this
     */
    public function setSouth($south)
    {
        $this->south = $south;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getWest()
    {
        return $this->west;
    }

    /**
     * @param  float|string|integer $west
     * @return $this
     */
    public function setWest($west)
    {
        $this->west = $west;

        return $this;
    }

    /**
     * @return integer
     */
    public function getPrecision()
    {
        return $this->precision;
    }

    /**
     * @param  integer $precision
     * @return $this
     */
    public function setPrecision($precision)
    {
        $this->precision = $precision;

        return $this;
    }
}
