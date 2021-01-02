<?php

/*
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools\Tests\BoundingBox;

use League\Geotools\BoundingBox\BoundingBox;
use League\Geotools\Coordinate\Coordinate;
use League\Geotools\Coordinate\Ellipsoid;
use League\Geotools\Exception\InvalidArgumentException;
use League\Geotools\Polygon\Polygon;

/**
 * @author Gabriel Bull <me@gabrielbull.com>
 */
class BoundingBoxTest extends \League\Geotools\Tests\TestCase
{
    /**
     * @var Polygon
     */
    protected $polygon;

    protected function setup(): void
    {
        $this->polygon = new Polygon;
    }

    public function polygonAndExpectedNorthWestAndSouthEastCoordinates()
    {
        return array(
            array(
                'polygonCoordinates' => array(
                    array(48.9675969, 1.7440796),
                    array(48.4711003, 2.5268555),
                    array(48.9279131, 3.1448364),
                    array(49.3895245, 2.6119995)
                ),
                'north' => 49.3895245,
                'east'  => 3.1448364,
                'south' => 48.4711003,
                'west'  => 1.7440796,
            ),
        );
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testConstructWithPolygon()
    {
        new BoundingBox(new Polygon);
    }


    /**
     * @doesNotPerformAssertions
     */
    public function testConstructWithCoordinate()
    {
        new BoundingBox(new Coordinate(array(0, 0)));
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testConstructWithNull()
    {
        new BoundingBox;
    }

    public function testConstructWithInvalidArgument()
    {
        $this->expectException(\InvalidArgumentException::class);
        new BoundingBox('string');
    }

    /**
     * @dataProvider polygonAndExpectedNorthWestAndSouthEastCoordinates
     * @param array $polygonCoordinates
     * @param string $north
     * @param string $east
     * @param string $south
     * @param string $west
     */
    public function testPolygonBoundingBox($polygonCoordinates, $north, $east, $south, $west)
    {
        foreach ($polygonCoordinates as $coordinate) {
            $this->polygon->add(
                $this->getMockCoordinateReturns($coordinate, Ellipsoid::createFromName(Ellipsoid::WGS84))
            );
        }

        $this->assertEquals(
            $north,
            $this->polygon->getBoundingBox()->getNorth()
        );
        $this->assertEquals(
            $east,
            $this->polygon->getBoundingBox()->getEast()
        );
        $this->assertEquals(
            $south,
            $this->polygon->getBoundingBox()->getSouth()
        );
        $this->assertEquals(
            $west,
            $this->polygon->getBoundingBox()->getWest()
        );
    }

    /**
     * @test
     */
    public function itShouldThrowAnExceptionWhenEllipsoidsDontMatch()
    {
        $bb = new BoundingBox(
            new Polygon([new Coordinate([-1, -2], Ellipsoid::createFromName(Ellipsoid::AUSTRALIAN_NATIONAL))])
        );
        $polygon = new Polygon([new Coordinate([-1, -2], Ellipsoid::createFromName(Ellipsoid::WGS84))]);

        $this->expectException('\InvalidArgumentException');
        $bb->setPolygon($polygon);
    }

    /**
     * @test
     */
    public function itShouldReturnThePolygonRectangleOfTheBoundingBox()
    {
        $bb = new BoundingBox();

        $this->assertNull($bb->getAsPolygon());

        $bb = new BoundingBox(
            new Polygon([
                new Coordinate([-1, -2], Ellipsoid::createFromName(Ellipsoid::WGS84)),
                new Coordinate([1, 2], Ellipsoid::createFromName(Ellipsoid::WGS84))
            ])
        );

        $polygon = $bb->getAsPolygon();

        $expected = new Polygon([
            new Coordinate([1, -2], Ellipsoid::createFromName(Ellipsoid::WGS84)),
            new Coordinate([1, 2], Ellipsoid::createFromName(Ellipsoid::WGS84)),
            new Coordinate([-1, 2], Ellipsoid::createFromName(Ellipsoid::WGS84)),
            new Coordinate([-1, -2], Ellipsoid::createFromName(Ellipsoid::WGS84)),
            new Coordinate([1, -2], Ellipsoid::createFromName(Ellipsoid::WGS84))
        ]);

        $this->assertEquals($expected, $polygon);
    }

    /**
     * @test
     */
    public function itShouldMergeBoundingBoxes()
    {
        $bb = new BoundingBox(
            new Polygon([
                new Coordinate([-1, -2], Ellipsoid::createFromName(Ellipsoid::WGS84)),
                new Coordinate([1, 2], Ellipsoid::createFromName(Ellipsoid::WGS84))
            ])
        );

        $bb2 = new BoundingBox(
            new Polygon([
                new Coordinate([0, -3], Ellipsoid::createFromName(Ellipsoid::WGS84)),
                new Coordinate([2, 0], Ellipsoid::createFromName(Ellipsoid::WGS84))
            ])
        );

        $merged = $bb->merge($bb2);

        $this->assertEquals(2, $merged->getNorth());
        $this->assertEquals(-1, $merged->getSouth());
        $this->assertEquals(2, $merged->getEast());
        $this->assertEquals(-3, $merged->getWest());
    }
}
