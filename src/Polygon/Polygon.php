<?php
namespace League\Geotools\Polygon;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use JsonSerializable;
use League\Geotools\Coordinate\Coordinate;
use League\Geotools\Coordinate\CoordinateCollection;
use League\Geotools\AbstractGeotools;
use League\Geotools\Coordinate\CoordinateInterface;

class Polygon extends AbstractGeotools implements PolygonInterface, Countable, IteratorAggregate, ArrayAccess,
    JsonSerializable
{
    /**
     * @var CoordinateCollection
     */
    private $coordinates;

    /**
     * @var CoordinateInterface
     */
    private $maximumCoordinate;

    /**
     * @var CoordinateInterface
     */
    private $minimumCoordinate;

    /**
     * @var bool
     */
    private $hasCoordinate = false;

    public function __construct()
    {
        $this->coordinates = new CoordinateCollection();
        $this->maximumCoordinate = new Coordinate(array(0,0));
        $this->minimumCoordinate = new Coordinate(array(0,0));
    }

    private function recalulateMaximumAndMinimum()
    {
        $this->hasCoordinate = false;
        $this->maximumCoordinate = new Coordinate(array(0,0));
        $this->minimumCoordinate = new Coordinate(array(0,0));
        foreach ($this->coordinates as $coordinate) {
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
            $this->maximumCoordinate->setLatitude($coordinate->getLatitude());
            $this->maximumCoordinate->setLongitude($coordinate->getLongitude());
            $this->minimumCoordinate->setLatitude($coordinate->getLatitude());
            $this->minimumCoordinate->setLongitude($coordinate->getLongitude());
        } else {
            $latitude = $coordinate->getLatitude();
            $longitude = $coordinate->getLongitude();

            if ($latitude < $this->minimumCoordinate->getLatitude()) {
                $this->minimumCoordinate->setLatitude($latitude);
            }

            if ($latitude > $this->maximumCoordinate->getLatitude()) {
                $this->maximumCoordinate->setLatitude($latitude);
            }

            if ($longitude < $this->minimumCoordinate->getLongitude()) {
                $this->minimumCoordinate->setLongitude($longitude);
            }

            if ($longitude > $this->maximumCoordinate->getLongitude()) {
                $this->maximumCoordinate->setLongitude($longitude);
            }
        }
    }

    /**
     * @return CoordinateInterface
     */
    public function getMaximumCoordinate()
    {
        return $this->maximumCoordinate;
    }

    /**
     * @param CoordinateInterface $maximumCoordinate
     * @return $this
     */
    public function setMaximumCoordinate(CoordinateInterface $maximumCoordinate)
    {
        $this->maximumCoordinate = $maximumCoordinate;
        return $this;
    }

    /**
     * @return CoordinateInterface
     */
    public function getMinimumCoordinate()
    {
        return $this->minimumCoordinate;
    }

    /**
     * @param CoordinateInterface $minimumCoordinate
     * @return $this
     */
    public function setMinimumCoordinate(CoordinateInterface $minimumCoordinate)
    {
        $this->minimumCoordinate = $minimumCoordinate;
        return $this;
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
        $this->recalulateMaximumAndMinimum();
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
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->coordinates->jsonSerialize();
    }

    /**
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->coordinates->offsetExists($offset);
    }

    /**
     * @param string $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->coordinates->offsetGet($offset);
    }

    /**
     * @param string $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->coordinates->offsetSet($offset, $value);
        $this->recalulateMaximumAndMinimum();
    }

    /**
     * @param string $offset
     * @return null
     */
    public function offsetUnset($offset)
    {
        $retval = $this->coordinates->offsetUnset($offset);
        $this->recalulateMaximumAndMinimum();
        return $retval;
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->coordinates->count();
    }

    /**
     * @return ArrayIterator
     */
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
    public function set($key, CoordinateInterface $coordinate)
    {
        $this->coordinates->set($key, $coordinate);
        $this->recalulateMaximumAndMinimum();
    }

    /**
     * {@inheritDoc}
     */
    public function add(CoordinateInterface $coordinate)
    {
        $this->compareMaximumAndMinimum($coordinate);
        $this->hasCoordinate = true;
        return $this->coordinates->add($coordinate);
    }

    /**
     * {@inheritDoc}
     */
    public function remove($key)
    {
        $retval = $this->coordinates->remove($key);
        $this->recalulateMaximumAndMinimum();
        return $retval;
    }
}
