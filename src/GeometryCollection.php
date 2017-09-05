<?php

/*
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools;

use League\Geotools\BoundingBox\BoundingBox;
use League\Geotools\BoundingBox\BoundingBoxInterface;
use League\Geotools\Coordinate\Coordinate;
use League\Geotools\Coordinate\CoordinateCollection;
use League\Geotools\Coordinate\Ellipsoid;
use League\Geotools\Exception\InvalidArgumentException;

/**
 * @author Rémi San <remi.san@gmail.com>
 */
abstract class GeometryCollection extends ArrayCollection implements GeometryInterface
{
    /**
     * @var Ellipsoid
     */
    private $ellipsoid;

    /**
     * @var integer
     */
    private $precision;

    /**
     * CoordinateCollection constructor.
     *
     * @param GeometryInterface[] $geometries
     * @param Ellipsoid             $ellipsoid
     */
    public function __construct(array $geometries = array(), Ellipsoid $ellipsoid = null)
    {
        $this->precision = -1;

        $this->ellipsoid = $ellipsoid ? : null;

        $this->checkGeometriesArray($geometries);

        parent::__construct($geometries);
    }

    /**
     * @return string
     */
    abstract public function getGeometryType();

    /**
     * @return integer
     */
    public function getPrecision()
    {
        return $this->precision;
    }

    /**
     * @return Coordinate
     */
    public function getCoordinate()
    {
        if ($this->isEmpty()) {
            return null;
        }

        return $this->offsetGet(0)->getCoordinate();
    }

    /**
     * @return CoordinateCollection
     */
    public function getCoordinates()
    {
        $coordinates = new CoordinateCollection(array(), $this->ellipsoid);

        /** @var GeometryInterface $element */
        foreach ($this->elements as $element) {
            $coordinates = $coordinates->merge($element->getCoordinates());
        }

        return $coordinates;
    }

    /**
     * @return boolean
     */
    public function isEmpty()
    {
        return count($this->elements) === 0 ;
    }

    /**
     * @return BoundingBoxInterface
     */
    public function getBoundingBox()
    {
        $boundingBox = new BoundingBox();

        /** @var GeometryInterface $element */
        foreach ($this->elements as $element) {
            $boundingBox = $boundingBox->merge($element->getBoundingBox());
        }

        return $boundingBox;
    }

    /**
     * @param string            $key
     *
     * @param GeometryInterface $value
     */
    public function set($key, $value)
    {
        $this->checkEllipsoid($value);
        $this->elements[$key] = $value;
    }

    /**
     * @param  GeometryInterface $value
     *
     * @return bool
     */
    public function add($value)
    {
        $this->checkEllipsoid($value);
        $this->elements[] = $value;

        return true;
    }

    /**
     * @return Ellipsoid
     */
    public function getEllipsoid()
    {
        return $this->ellipsoid;
    }

    /**
     * @param array GeometryInterface[] $geometries
     */
    private function checkGeometriesArray(array $geometries)
    {
        foreach ($geometries as $geometry) {
            if (!$geometry instanceof GeometryInterface) {
                throw new InvalidArgumentException("You didn't provide a geometry!");
            }
            $this->checkEllipsoid($geometry);
        }
    }

    /**
     * @param GeometryInterface $geometry
     *
     * @throws InvalidArgumentException
     */
    private function checkEllipsoid(GeometryInterface $geometry)
    {
        if (bccomp($geometry->getPrecision(), $this->precision) === 1) {
            $this->precision = $geometry->getPrecision();
        }

        if ($this->ellipsoid === null) {
            $this->ellipsoid = $geometry->getEllipsoid();
        } elseif ($geometry->isEmpty() || $geometry->getEllipsoid() != $this->ellipsoid) {
            throw new InvalidArgumentException("Geometry is invalid");
        }
    }

    /**
     * @param  ArrayCollection $collection
     *
     * @return ArrayCollection
     */
    public function merge(ArrayCollection $collection)
    {
        if (!$collection instanceof GeometryCollection || $collection->getGeometryType() !== $this->getGeometryType()) {
            throw new InvalidArgumentException("Collections types don't match, you can't merge.");
        }

        return parent::merge($collection);
    }
}
