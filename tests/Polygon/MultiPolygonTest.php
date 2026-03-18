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
use League\Geotools\Polygon\MultiPolygon;
/**
 * @author Gabriel Bull <me@gabrielbull.com>
 */
class MultiPolygonTest extends \League\Geotools\Tests\TestCase
{
    /**
     * @var Polygon
     */
    protected $polygon;
    protected function setup(): void
    {
        $this->polygon = new Polygon;
    }
    public function testType()
    {
        $multiPolygon = new MultiPolygon();
        $this->assertEquals('MULTIPOLYGON', $multiPolygon->getGeometryType());
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
        $multiPolygon = new MultiPolygon([$this->polygon]);
        $this->assertTrue($this->polygon->pointOnVertex(new Coordinate($vertexCoordinate)));
        $this->assertTrue($multiPolygon->pointOnVertex(new Coordinate($vertexCoordinate)));
    }
    #[DataProvider('polygonAndVertexCoordinate')]
    public function testPointNotOnVertex($polygonCoordinates)
    {
        $this->polygon->set($polygonCoordinates);
        $multiPolygon = new MultiPolygon([$this->polygon]);
        $this->assertFalse($this->polygon->pointOnVertex(new Coordinate(array(0, 0))));
        $this->assertFalse($multiPolygon->pointOnVertex(new Coordinate(array(0, 0))));
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
        $multiPolygon = new MultiPolygon([$this->polygon]);
        foreach ($pointOnBoundaryCoordinates as $pointOnBoundaryCoordinate) {
            $this->assertTrue($this->polygon->pointOnBoundary(new Coordinate($pointOnBoundaryCoordinate)));
            $this->assertTrue($multiPolygon->pointOnBoundary(new Coordinate($pointOnBoundaryCoordinate)));
        }
    }
    #[DataProvider('polygonAndPointOnBoundaryCoordinate')]
    public function testPointNotOnBoundary(
        $polygonCoordinates,
        $pointOnBoundaryCoordinates,
        $pointNotOnBoundaryCoordinates
    ) {
        $this->polygon->set($polygonCoordinates);
        $multiPolygon = new MultiPolygon([$this->polygon]);
        foreach ($pointNotOnBoundaryCoordinates as $pointNotOnBoundaryCoordinate) {
            $this->assertFalse($this->polygon->pointOnBoundary(new Coordinate($pointNotOnBoundaryCoordinate)));
            $this->assertFalse($multiPolygon->pointOnBoundary(new Coordinate($pointNotOnBoundaryCoordinate)));
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
        $multiPolygon = new MultiPolygon([$this->polygon]);
        foreach ($pointInPolygonCoordinates as $pointInPolygonCoordinate) {
            $this->assertTrue($this->polygon->pointInPolygon(new Coordinate($pointInPolygonCoordinate)));
            $this->assertTrue($multiPolygon->pointInPolygon(new Coordinate($pointInPolygonCoordinate)));
        }
    }
    #[DataProvider('polygonAndPointInPolygonCoordinate')]
    public function testPointNotInPolygon(
        $polygonCoordinates,
        $pointInPolygonCoordinates,
        $pointNotInPolygonCoordinates
    ) {
        $this->polygon->set($polygonCoordinates);
        $multiPolygon = new MultiPolygon([$this->polygon]);
        foreach ($pointNotInPolygonCoordinates as $pointNotInPolygonCoordinate) {
            $this->assertFalse($this->polygon->pointInPolygon(new Coordinate($pointNotInPolygonCoordinate)));
            $this->assertFalse($multiPolygon->pointInPolygon(new Coordinate($pointNotInPolygonCoordinate)));
        }
    }
}
