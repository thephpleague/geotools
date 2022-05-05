<?php

/*
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools\Polygon;

use League\Geotools\BoundingBox\BoundingBox;
use League\Geotools\BoundingBox\BoundingBoxInterface;
use League\Geotools\Coordinate\Coordinate;
use League\Geotools\Coordinate\CoordinateCollection;
use League\Geotools\Coordinate\CoordinateInterface;
use League\Geotools\Coordinate\Ellipsoid;

/**
 * @author Gabriel Bull <me@gabrielbull.com>
 */
class Polygon implements PolygonInterface, \Countable, \IteratorAggregate, \ArrayAccess, \JsonSerializable
{
    const TYPE = 'POLYGON';

    /**
     * @var CoordinateCollection
     */
    private $coordinates;

    /**
     * @var BoundingBoxInterface
     */
    private $boundingBox;

    /**
     * @var boolean
     */
    private $hasCoordinate = false;

    /**
     * @var integer
     */
    private $precision = 8;

    /**
     * @param null|array|CoordinateCollection $coordinates
     */
    public function __construct($coordinates = null)
    {
        if (is_array($coordinates) || null === $coordinates) {
            $this->coordinates = new CoordinateCollection;
        } elseif ($coordinates instanceof CoordinateCollection) {
            $this->coordinates = $coordinates;
            $this->hasCoordinate = $coordinates->count() > 0;
        } else {
            throw new \InvalidArgumentException;
        }

        $this->boundingBox = new BoundingBox($this);

        if (is_array($coordinates)) {
            $this->set($coordinates);
        }
    }

    /**
     * @return string
     */
    public function getGeometryType()
    {
        return self::TYPE;
    }

    /**
     * @return Ellipsoid
     */
    public function getEllipsoid()
    {
        return $this->coordinates->getEllipsoid();
    }

    /**
     * @return Coordinate
     */
    public function getCoordinate()
    {
        return $this->coordinates->offsetGet(0);
    }

    /**
     * @return boolean
     */
    public function isEmpty()
    {
        return !$this->hasCoordinate;
    }


    /**
     * @param  CoordinateInterface $coordinate
     * @return boolean
     */
    public function pointInPolygon(CoordinateInterface $coordinate)
    {
        if (!$this->hasCoordinate) {
            return false;
        }

        if (!$this->boundingBox->pointInBoundingBox($coordinate)) {
            return false;
        }

        if ($this->pointOnVertex($coordinate)) {
            return true;
        }

        if ($this->pointOnBoundary($coordinate)) {
            return true;
        }

        $total = $this->count();
        $intersections = 0;
        for ($i = 1; $i < $total; $i++) {
            $currentVertex = $this->get($i - 1);
            $nextVertex = $this->get($i);

            if (bccomp(
                $coordinate->getLatitude(),
                min($currentVertex->getLatitude(), $nextVertex->getLatitude()),
                $this->getPrecision()
            ) === 1 &&
                bccomp(
                    $coordinate->getLatitude(),
                    max($currentVertex->getLatitude(), $nextVertex->getLatitude()),
                    $this->getPrecision()
                ) <= 0 &&
                bccomp(
                    $coordinate->getLongitude(),
                    max($currentVertex->getLongitude(), $nextVertex->getLongitude()),
                    $this->getPrecision()
                ) <= 0 &&
                bccomp(
                    $currentVertex->getLatitude(),
                    $nextVertex->getLatitude(),
                    $this->getPrecision()
                ) !== 0
            ) {
                $xinters =
                    ($coordinate->getLatitude() - $currentVertex->getLatitude()) *
                    ($nextVertex->getLongitude() - $currentVertex->getLongitude()) /
                    ($nextVertex->getLatitude() - $currentVertex->getLatitude()) +
                    $currentVertex->getLongitude();

                if (bccomp(
                    $currentVertex->getLongitude(),
                    $nextVertex->getLongitude(),
                    $this->getPrecision()
                ) === 0 ||
                    bccomp(
                        $coordinate->getLongitude(),
                        $xinters,
                        $this->getPrecision()
                    ) <= 0
                ) {
                    $intersections++;
                }
            }
        }

        if ($intersections % 2 != 0) {
            return true;
        }

        return false;
    }

    /**
     * @param  CoordinateInterface $coordinate
     * @return boolean
     */
    public function pointOnBoundary(CoordinateInterface $coordinate)
    {
        $total = $this->count();
        for ($i = 1; $i <= $total; $i++) {
            $currentVertex = $this->get($i - 1);
            $nextVertex = $this->get($i);

            if (null === $nextVertex) {
                $nextVertex = $this->get(0);
            }

            // Check if coordinate is on a horizontal boundary
            if (bccomp(
                $currentVertex->getLatitude(),
                $nextVertex->getLatitude(),
                $this->getPrecision()
            ) === 0 &&
                bccomp(
                    $currentVertex->getLatitude(),
                    $coordinate->getLatitude(),
                    $this->getPrecision()
                ) === 0 &&
                bccomp(
                    $coordinate->getLongitude(),
                    min($currentVertex->getLongitude(), $nextVertex->getLongitude()),
                    $this->getPrecision()
                ) === 1 &&
                bccomp(
                    $coordinate->getLongitude(),
                    max($currentVertex->getLongitude(), $nextVertex->getLongitude()),
                    $this->getPrecision()
                ) === -1
            ) {
                return true;
            }

            // Check if coordinate is on a boundary
            if (bccomp(
                $coordinate->getLatitude(),
                min($currentVertex->getLatitude(), $nextVertex->getLatitude()),
                $this->getPrecision()
            ) === 1 &&
                bccomp(
                    $coordinate->getLatitude(),
                    max($currentVertex->getLatitude(), $nextVertex->getLatitude()),
                    $this->getPrecision()
                ) <= 0 &&
                bccomp(
                    $coordinate->getLongitude(),
                    max($currentVertex->getLongitude(), $nextVertex->getLongitude()),
                    $this->getPrecision()
                ) <= 0 &&
                bccomp(
                    $currentVertex->getLatitude(),
                    $nextVertex->getLatitude(),
                    $this->getPrecision()
                ) !== 0
            ) {
                $xinters =
                    ($coordinate->getLatitude() - $currentVertex->getLatitude()) *
                    ($nextVertex->getLongitude() - $currentVertex->getLongitude()) /
                    ($nextVertex->getLatitude() - $currentVertex->getLatitude()) +
                    $currentVertex->getLongitude();

                if (bccomp($xinters, $coordinate->getLongitude(), $this->getPrecision()) === 0) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param  CoordinateInterface $coordinate
     * @return boolean
     */
    public function pointOnVertex(CoordinateInterface $coordinate)
    {
        foreach ($this->coordinates as $vertexCoordinate) {
            if (bccomp(
                $vertexCoordinate->getLatitude(),
                $coordinate->getLatitude(),
                $this->getPrecision()
            ) === 0 &&
                bccomp(
                    $vertexCoordinate->getLongitude(),
                    $coordinate->getLongitude(),
                    $this->getPrecision()
                ) === 0
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function getCoordinates()
    {
        return $this->coordinates;
    }

    /**
     * {@inheritDoc}
     */
    public function setCoordinates(CoordinateCollection $coordinates)
    {
        $this->coordinates = $coordinates;
        $this->boundingBox->setPolygon($this);

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->coordinates->toArray();
    }

    /**
     * {@inheritDoc}
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->coordinates->jsonSerialize();
    }

    /**
     * {@inheritDoc}
     */
    #[\ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return $this->coordinates->offsetExists($offset);
    }

    /**
     * {@inheritDoc}
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->coordinates->offsetGet($offset);
    }

    /**
     * {@inheritDoc}
     */
    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        $this->coordinates->offsetSet($offset, $value);
        $this->boundingBox->setPolygon($this);
    }

    /**
     * {@inheritDoc}
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        $retval = $this->coordinates->offsetUnset($offset);
        $this->boundingBox->setPolygon($this);
        return $retval;
    }

    /**
     * {@inheritDoc}
     */
    #[\ReturnTypeWillChange]
    public function count()
    {
        return $this->coordinates->count();
    }

    /**
     * {@inheritDoc}
     */
    #[\ReturnTypeWillChange]
    public function getIterator()
    {
        return $this->coordinates->getIterator();
    }

    /**
     * {@inheritDoc}
     */
    public function get($key)
    {
        return $this->coordinates->get($key);
    }

    /**
     * {@inheritDoc}
     */
    public function set($key, CoordinateInterface $coordinate = null)
    {
        if (is_array($key)) {
            $values = $key;
        } elseif (null !== $coordinate) {
            $values = array($key => $coordinate);
        } else {
            throw new \InvalidArgumentException;
        }

        foreach ($values as $key => $value) {
            if (!$value instanceof CoordinateInterface) {
                $value = new Coordinate($value);
            }
            $this->coordinates->set($key, $value);
        }

        $this->hasCoordinate = true;
        $this->boundingBox->setPolygon($this);
    }

    /**
     * {@inheritDoc}
     */
    public function add(CoordinateInterface $coordinate)
    {
        $retval = $this->coordinates->add($coordinate);

        $this->hasCoordinate = true;
        $this->boundingBox->setPolygon($this);

        return $retval;
    }

    /**
     * {@inheritDoc}
     */
    public function remove($key)
    {
        $retval = $this->coordinates->remove($key);

        if (!count($this->coordinates)) {
            $this->hasCoordinate = false;
        }
        $this->boundingBox->setPolygon($this);

        return $retval;
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
        $this->boundingBox->setPrecision($precision);
        $this->precision = $precision;

        return $this;
    }

    /**
     * @return BoundingBoxInterface
     */
    public function getBoundingBox()
    {
        return $this->boundingBox;
    }

    /**
     * @param  BoundingBoxInterface $boundingBox
     * @return $this
     */
    public function setBoundingBox(BoundingBoxInterface $boundingBox)
    {
        $this->boundingBox = $boundingBox;

        return $this;
    }
}
