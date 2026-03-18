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
    use PolygonCenterTrait;
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
     * Running sum of the x-components of coordinates on the unit sphere.
     *
     * @var float
     */
    private $sumX = 0.0;

    /**
     * Running sum of the y-components of coordinates on the unit sphere.
     *
     * @var float
     */
    private $sumY = 0.0;

    /**
     * Running sum of the z-components of coordinates on the unit sphere.
     *
     * @var float
     */
    private $sumZ = 0.0;

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
            $this->recalculateSums();
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
        $this->recalculateSums();

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
        $existing = $this->coordinates->get($offset);
        if ($existing !== null) {
            $this->subtractFromSums($existing);
        }
        $this->coordinates->offsetSet($offset, $value);
        $this->addToSums($value);
        $this->boundingBox->setPolygon($this);
    }

    /**
     * {@inheritDoc}
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        $existing = $this->coordinates->get($offset);
        if ($existing !== null) {
            $this->subtractFromSums($existing);
        }
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
    public function set($key, ?CoordinateInterface $coordinate = null)
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
        $this->recalculateSums();
    }

    /**
     * {@inheritDoc}
     */
    public function add(CoordinateInterface $coordinate)
    {
        $retval = $this->coordinates->add($coordinate);

        $this->hasCoordinate = true;
        $this->boundingBox->setPolygon($this);
        $this->addToSums($coordinate);

        return $retval;
    }

    /**
     * {@inheritDoc}
     */
    public function remove($key)
    {
        $coordinate = $this->coordinates->get($key);
        $retval = $this->coordinates->remove($key);

        if (!count($this->coordinates)) {
            $this->hasCoordinate = false;
            $this->sumX = $this->sumY = $this->sumZ = 0.0;
        } elseif ($coordinate !== null) {
            $this->subtractFromSums($coordinate);
        }
        $this->boundingBox->setPolygon($this);

        return $retval;
    }

    /**
     * Returns the geographic centroid of the polygon's coordinates.
     *
     * The centroid is computed by averaging the unit-sphere (x, y, z) vectors
     * of all vertices and projecting the result back to a (latitude, longitude)
     * coordinate.  Running sums of x/y/z are maintained incrementally so that
     * repeated calls are O(1) after the initial population.
     *
     * @return CoordinateInterface|null  null when the polygon is empty
     */
    public function getCenter(): ?CoordinateInterface
    {
        $count = $this->count();
        if ($count === 0) {
            return null;
        }

        $x = $this->sumX / $count;
        $y = $this->sumY / $count;
        $z = $this->sumZ / $count;

        $lng = rad2deg(atan2($y, $x));
        $hyp = sqrt($x * $x + $y * $y);
        $lat = rad2deg(atan2($z, $hyp));

        return new Coordinate([$lat, $lng], $this->getEllipsoid());
    }

    /**
     * Returns the radius of the polygon in meters, defined as the maximum
     * haversine distance from the centroid to any vertex.
     *
     * @return float
     */
    public function getRadius(): float
    {
        $center = $this->getCenter();
        if ($center === null) {
            return 0.0;
        }

        $radius = 0.0;
        $R = $center->getEllipsoid()->getArithmeticMeanRadius();
        $latC = deg2rad($center->getLatitude());
        $lngC = deg2rad($center->getLongitude());

        foreach ($this->coordinates as $coordinate) {
            $d = $this->haversineDistance(
                $R,
                $latC,
                $lngC,
                deg2rad($coordinate->getLatitude()),
                deg2rad($coordinate->getLongitude())
            );

            if ($d > $radius) {
                $radius = $d;
            }
        }

        return $radius;
    }

    /**
     * Adds a coordinate's unit-sphere contribution to the running sums.
     *
     * @param CoordinateInterface $coordinate
     */
    private function addToSums(CoordinateInterface $coordinate): void
    {
        [$x, $y, $z] = $this->coordinateToXYZ($coordinate);
        $this->sumX += $x;
        $this->sumY += $y;
        $this->sumZ += $z;
    }

    /**
     * Subtracts a coordinate's unit-sphere contribution from the running sums.
     *
     * @param CoordinateInterface $coordinate
     */
    private function subtractFromSums(CoordinateInterface $coordinate): void
    {
        [$x, $y, $z] = $this->coordinateToXYZ($coordinate);
        $this->sumX -= $x;
        $this->sumY -= $y;
        $this->sumZ -= $z;
    }

    /**
     * Recalculates the running sums from scratch based on current coordinates.
     */
    private function recalculateSums(): void
    {
        $this->sumX = $this->sumY = $this->sumZ = 0.0;
        foreach ($this->coordinates as $coordinate) {
            $this->addToSums($coordinate);
        }
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
