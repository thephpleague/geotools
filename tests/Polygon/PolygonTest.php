<?php

/*
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools\Tests\Polygon;

use League\Geotools\Coordinate\Coordinate;
use League\Geotools\Polygon\Polygon;

/**
 * @author Gabriel Bull <me@gabrielbull.com>
 */
class PolygonTest extends \League\Geotools\Tests\TestCase
{
    /**
     * @var Polygon
     */
    protected $polygon;

    protected function setUp()
    {
        $this->polygon = new Polygon;
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCannotCreatePolygonWithString()
    {
        new Polygon('foo');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCannotCreatePolygonWithInteger()
    {
        new Polygon(123);
    }

    public function polygonCoordinates()
    {
        return array(
            array(
                'polygonCoordinates' => array(
                    array(48.9675969, 1.7440796),
                    array(48.4711003, 2.5268555),
                    array(48.9279131, 3.1448364),
                    array(49.3895245, 2.6119995)
                ),
            ),
        );
    }

    /**
     * @dataProvider polygonCoordinates
     * @param array $polygonCoordinates
     */
    public function testContructor($polygonCoordinates)
    {
        new Polygon($polygonCoordinates);
    }

    /**
     * @dataProvider polygonCoordinates
     * @param array $polygonCoordinates
     */
    public function testArraySetterAndGetter($polygonCoordinates)
    {
        $this->polygon->set($polygonCoordinates);

        $this->assertCount(4, $this->polygon);
        foreach ($polygonCoordinates as $key => $value) {
            $this->assertEquals($value[0], $this->polygon->get($key)->getLatitude());
            $this->assertEquals($value[1], $this->polygon->get($key)->getLongitude());
        }
    }

    public function polygonAndVertexCoordinate()
    {
        return array(
            array(
                'polygonCoordinates' => array(
                    array(48.9675969, 1.7440796),
                    array(48.4711003, 2.5268555),
                    array(48.9279131, 3.1448364),
                    array(49.3895245, 2.6119995)
                ),
                'vertexCoordinate' => array(48.4711003, 2.5268555),
            ),
        );
    }

    /**
     * @dataProvider polygonAndVertexCoordinate
     * @param array $polygonCoordinates
     * @param array $vertexCoordinate
     */
    public function testPointOnVertex($polygonCoordinates, $vertexCoordinate)
    {
        $this->polygon->set($polygonCoordinates);
        $this->assertTrue($this->polygon->pointOnVertex(new Coordinate($vertexCoordinate)));
    }

    /**
     * @dataProvider polygonAndVertexCoordinate
     * @param array $polygonCoordinates
     */
    public function testPointNotOnVertex($polygonCoordinates)
    {
        $this->polygon->set($polygonCoordinates);
        $this->assertFalse($this->polygon->pointOnVertex(new Coordinate(array(0, 0))));
    }

    public function polygonAndPointOnBoundaryCoordinate()
    {
        return array(
            array(
                'polygonCoordinates' => array(
                    array(48.9675969, 1.7440796),
                    array(48.4711003, 2.5268555),
                    array(48.9279131, 3.1448364),
                    array(49.3895245, 2.6119995)
                ),
                'pointOnBoundaryCoordinates' => array(
                    array(48.7193486, 2.13546755),
                    array(48.6995067, 2.83584595),
                    array(49.1587188, 2.87841795),
                    array(49.1785607, 2.17803955),
                ),
                'pointNotOnBoundaryCoordinates' => array(
                    array(43.7193486, 2.13546755),
                    array(45.6995067, 2.83584595),
                    array(47.1587188, 2.87841795),
                    array(20.1785607, 2.17803955),
                ),
            ),
        );
    }

    /**
     * @dataProvider polygonAndPointOnBoundaryCoordinate
     * @param array $polygonCoordinates
     * @param array $pointOnBoundaryCoordinates
     */
    public function testPointOnBoundary($polygonCoordinates, $pointOnBoundaryCoordinates)
    {
        $this->polygon->set($polygonCoordinates);
        foreach ($pointOnBoundaryCoordinates as $pointOnBoundaryCoordinate) {
            $this->assertTrue($this->polygon->pointOnBoundary(new Coordinate($pointOnBoundaryCoordinate)));
        }
    }

    /**
     * @dataProvider polygonAndPointOnBoundaryCoordinate
     * @param array $polygonCoordinates
     * @param array $pointOnBoundaryCoordinates
     * @param array $pointNotOnBoundaryCoordinates
     */
    public function testPointNotOnBoundary(
        $polygonCoordinates,
        $pointOnBoundaryCoordinates,
        $pointNotOnBoundaryCoordinates
    ) {
        $this->polygon->set($polygonCoordinates);
        foreach ($pointNotOnBoundaryCoordinates as $pointNotOnBoundaryCoordinate) {
            $this->assertFalse($this->polygon->pointOnBoundary(new Coordinate($pointNotOnBoundaryCoordinate)));
        }
    }

    public function polygonAndPointInPolygonCoordinate()
    {
        return array(
            array(
                'polygonCoordinates' => array(
                    array(48.9675969, 1.7440796),
                    array(48.4711003, 2.5268555),
                    array(48.9279131, 3.1448364),
                    array(49.3895245, 2.6119995)
                ),
                'pointInPolygonCoordinates' => array(
                    array(49.1785607, 2.4444580),
                    array(49.1785607, 2.0000000),
                    array(49.1785607, 1.7440796),
                    array(48.9279131, 2.4444580),
                ),
                'pointNotInPolygonCoordinates' => array(
                    array(49.1785607, 5),
                    array(50, 2.4444580),
                )
            ),
        );
    }

    /**
     * @dataProvider polygonAndPointInPolygonCoordinate
     * @param array $polygonCoordinates
     * @param array $pointInPolygonCoordinates
     */
    public function testPointInPolygon($polygonCoordinates, $pointInPolygonCoordinates)
    {
        $this->polygon->set($polygonCoordinates);
        foreach ($pointInPolygonCoordinates as $pointInPolygonCoordinate) {
            $this->assertTrue($this->polygon->pointInPolygon(new Coordinate($pointInPolygonCoordinate)));
        }
    }

    /**
     * @dataProvider polygonAndPointInPolygonCoordinate
     * @param array $polygonCoordinates
     * @param array $pointInPolygonCoordinates
     * @param array $pointNotInPolygonCoordinates
     */
    public function testPointNotInPolygon(
        $polygonCoordinates,
        $pointInPolygonCoordinates,
        $pointNotInPolygonCoordinates
    ) {
        $this->polygon->set($polygonCoordinates);
        foreach ($pointNotInPolygonCoordinates as $pointNotInPolygonCoordinate) {
            $this->assertFalse($this->polygon->pointInPolygon(new Coordinate($pointNotInPolygonCoordinate)));
        }
    }
}
