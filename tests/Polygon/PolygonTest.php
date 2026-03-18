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
     * @doesNotPerformAssertions
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

    public function testGetCenterReturnsNullOnEmptyPolygon()
    {
        $this->assertNull($this->polygon->getCenter());
    }

    public function testGetRadiusReturnsZeroOnEmptyPolygon()
    {
        $this->assertSame(0.0, $this->polygon->getRadius());
    }

    public function testGetCenterSingleCoordinate()
    {
        $this->polygon->add(new Coordinate([48.8566, 2.3522]));
        $center = $this->polygon->getCenter();

        $this->assertNotNull($center);
        $this->assertEqualsWithDelta(48.8566, (float) $center->getLatitude(), 0.0001);
        $this->assertEqualsWithDelta(2.3522, (float) $center->getLongitude(), 0.0001);
    }

    public function testGetCenterSymmetricSquare()
    {
        // A square centred at (0, 0) — centre must be (0, 0).
        $this->polygon->set([
            [1.0, -1.0],
            [1.0,  1.0],
            [-1.0,  1.0],
            [-1.0, -1.0],
        ]);

        $center = $this->polygon->getCenter();
        $this->assertNotNull($center);
        $this->assertEqualsWithDelta(0.0, (float) $center->getLatitude(), 0.0001);
        $this->assertEqualsWithDelta(0.0, (float) $center->getLongitude(), 0.0001);
    }

    public function testGetCenterWithKnownPolygon()
    {
        // The centroid of these four points should be approximately at their
        // geographic mean (verified independently with spherical averaging).
        $this->polygon->set([
            [48.9675969, 1.7440796],
            [48.4711003, 2.5268555],
            [48.9279131, 3.1448364],
            [49.3895245, 2.6119995],
        ]);

        $center = $this->polygon->getCenter();
        $this->assertNotNull($center);
        // The centroid latitude/longitude must fall inside the bounding box.
        $this->assertGreaterThan(48.4, (float) $center->getLatitude());
        $this->assertLessThan(49.4, (float) $center->getLatitude());
        $this->assertGreaterThan(1.7, (float) $center->getLongitude());
        $this->assertLessThan(3.2, (float) $center->getLongitude());
    }

    public function testGetRadiusGreaterThanZeroForNonTrivialPolygon()
    {
        $this->polygon->set([
            [48.9675969, 1.7440796],
            [48.4711003, 2.5268555],
            [48.9279131, 3.1448364],
            [49.3895245, 2.6119995],
        ]);

        $radius = $this->polygon->getRadius();
        $this->assertGreaterThan(0.0, $radius);
        // Rough sanity check: radius should be in tens-of-km range for this polygon.
        $this->assertGreaterThan(50000.0, $radius);   // > 50 km
        $this->assertLessThan(200000.0, $radius);     // < 200 km
    }

    public function testGetCenterUpdatesIncrementallyOnAdd()
    {
        $coord1 = new Coordinate([10.0, 20.0]);
        $coord2 = new Coordinate([10.0, 22.0]);

        $this->polygon->add($coord1);
        $center1 = $this->polygon->getCenter();
        $this->assertNotNull($center1);
        $this->assertEqualsWithDelta(10.0, (float) $center1->getLatitude(), 0.0001);
        $this->assertEqualsWithDelta(20.0, (float) $center1->getLongitude(), 0.0001);

        $this->polygon->add($coord2);
        $center2 = $this->polygon->getCenter();
        $this->assertNotNull($center2);
        // After adding the second point, the centroid longitude should be ~21.
        $this->assertEqualsWithDelta(10.0, (float) $center2->getLatitude(), 0.01);
        $this->assertEqualsWithDelta(21.0, (float) $center2->getLongitude(), 0.01);
    }

    public function testGetCenterUpdatesAfterRemove()
    {
        $this->polygon->set([
            [10.0, 20.0],
            [10.0, 22.0],
            [10.0, 24.0],
        ]);

        // Remove the last coordinate; centre should shift towards [10, 21].
        $this->polygon->remove(2);
        $center = $this->polygon->getCenter();
        $this->assertNotNull($center);
        $this->assertEqualsWithDelta(10.0, (float) $center->getLatitude(), 0.01);
        $this->assertEqualsWithDelta(21.0, (float) $center->getLongitude(), 0.01);
    }
}
