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

use League\Geotools\Polygon\Polygon;
use PHPUnit\Framework\Attributes\DataProvider;
use League\Geotools\Coordinate\Coordinate;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
/**
 * @author Gabriel Bull <me@gabrielbull.com>
 */
class PolygonTest extends \League\Geotools\Tests\TestCase
{
    /**
     * @var Polygon
     */
    protected $polygon;
    protected function setup(): void
    {
        $this->polygon = new Polygon;
    }
    public function testCannotCreatePolygonWithString()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Polygon('foo');
    }
    public function testCannotCreatePolygonWithInteger()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Polygon(123);
    }
    public static function polygonCoordinates()
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
    #[DataProvider('polygonCoordinates')]
    #[DoesNotPerformAssertions]
    public function testContructor($polygonCoordinates)
    {
        new Polygon($polygonCoordinates);
    }
    #[DataProvider('polygonCoordinates')]
    public function testArraySetterAndGetter($polygonCoordinates)
    {
        $this->polygon->set($polygonCoordinates);
        $this->assertCount(4, $this->polygon);
        foreach ($polygonCoordinates as $key => $value) {
            $this->assertEquals($value[0], $this->polygon->get($key)->getLatitude());
            $this->assertEquals($value[1], $this->polygon->get($key)->getLongitude());
        }
    }
    public static function polygonAndVertexCoordinate()
    {
        return array(
            array(
                array(
                    array(48.9675969, 1.7440796),
                    array(48.4711003, 2.5268555),
                    array(48.9279131, 3.1448364),
                    array(49.3895245, 2.6119995)
                ),
                array(48.4711003, 2.5268555),
            ),
        );
    }
    #[DataProvider('polygonAndVertexCoordinate')]
    public function testPointOnVertex($polygonCoordinates, $vertexCoordinate)
    {
        $this->polygon->set($polygonCoordinates);
        $this->assertTrue($this->polygon->pointOnVertex(new Coordinate($vertexCoordinate)));
    }
    #[DataProvider('polygonAndVertexCoordinate')]
    public function testPointNotOnVertex($polygonCoordinates)
    {
        $this->polygon->set($polygonCoordinates);
        $this->assertFalse($this->polygon->pointOnVertex(new Coordinate(array(0, 0))));
    }
    public static function polygonAndPointOnBoundaryCoordinate()
    {
        return array(
            array(
                array(
                    array(48.9675969, 1.7440796),
                    array(48.4711003, 2.5268555),
                    array(48.9279131, 3.1448364),
                    array(49.3895245, 2.6119995)
                ),
                array(
                    array(48.7193486, 2.13546755),
                    array(48.6995067, 2.83584595),
                    array(49.1587188, 2.87841795),
                    array(49.1785607, 2.17803955),
                ),
                array(
                    array(43.7193486, 2.13546755),
                    array(45.6995067, 2.83584595),
                    array(47.1587188, 2.87841795),
                    array(20.1785607, 2.17803955),
                ),
            ),
        );
    }
    #[DataProvider('polygonAndPointOnBoundaryCoordinate')]
    public function testPointOnBoundary($polygonCoordinates, $pointOnBoundaryCoordinates)
    {
        $this->polygon->set($polygonCoordinates);
        foreach ($pointOnBoundaryCoordinates as $pointOnBoundaryCoordinate) {
            $this->assertTrue($this->polygon->pointOnBoundary(new Coordinate($pointOnBoundaryCoordinate)));
        }
    }
    #[DataProvider('polygonAndPointOnBoundaryCoordinate')]
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
    public static function polygonAndPointInPolygonCoordinate()
    {
        return array(
            array(
                array(
                    array(48.9675969, 1.7440796),
                    array(48.4711003, 2.5268555),
                    array(48.9279131, 3.1448364),
                    array(49.3895245, 2.6119995)
                ),
                array(
                    array(49.1785607, 2.4444580),
                    array(49.1785607, 2.0000000),
                    array(49.1785607, 1.7440796),
                    array(48.9279131, 2.4444580),
                ),
                array(
                    array(49.1785607, 5),
                    array(50, 2.4444580),
                )
            ),
        );
    }
    #[DataProvider('polygonAndPointInPolygonCoordinate')]
    public function testPointInPolygon($polygonCoordinates, $pointInPolygonCoordinates)
    {
        $this->polygon->set($polygonCoordinates);
        foreach ($pointInPolygonCoordinates as $pointInPolygonCoordinate) {
            $this->assertTrue($this->polygon->pointInPolygon(new Coordinate($pointInPolygonCoordinate)));
        }
    }
    #[DataProvider('polygonAndPointInPolygonCoordinate')]
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
