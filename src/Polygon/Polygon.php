<?php
namespace League\Geotools\Point;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use JsonSerializable;
use League\Geotools\Coordinate\CoordinateCollection;
use League\Geotools\AbstractGeotools;
use League\Geotools\Coordinate\CoordinateInterface;
use League\Geotools\Polygon\PolygonInterface;

class Polygon extends AbstractGeotools implements PolygonInterface, Countable, IteratorAggregate, ArrayAccess,
    JsonSerializable
{
    /**
     * @var CoordinateCollection
     */
    private $coordinates;

    public function __construct()
    {
        $this->coordinates = new CoordinateCollection();
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
    }

    /**
     * @param string $offset
     * @return null
     */
    public function offsetUnset($offset)
    {
        return $this->coordinates->offsetUnset($offset);
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
    }

    /**
     * {@inheritDoc}
     */
    public function add(CoordinateInterface $coordinate)
    {
        return $this->coordinates->add($coordinate);
    }

    /**
     * {@inheritDoc}
     */
    public function remove($key)
    {
        return $this->coordinates->remove($key);
    }
}
