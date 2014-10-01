<?php
namespace League\Geotools\Polygon;

use League\Geotools\Coordinate\CoordinateCollection;
use League\Geotools\Coordinate\CoordinateInterface;

interface PolygonInterface
{
    /**
     * @return CoordinateCollection
     */
    public function getCoordinates();

    /**
     * @param CoordinateCollection $coordinates
     * @return $this
     */
    public function setCoordinates(CoordinateCollection $coordinates);

    /**
     * @param string $key
     * @return null|CoordinateInterface
     */
    public function get($key);

    /**
     * @param string $key
     * @param CoordinateInterface $coordinate
     */
    public function set($key, CoordinateInterface $coordinate);

    /**
     * @param CoordinateInterface $coordinate
     * @return bool
     */
    public function add(CoordinateInterface $coordinate);

    /**
     * @param string $key
     * @return null|CoordinateInterface
     */
    public function remove($key);
}
