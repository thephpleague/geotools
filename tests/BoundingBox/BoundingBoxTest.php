<?php
namespace League\Geotools\Tests\BoundingBox;

use League\Geotools\Polygon\Polygon;
use League\Geotools\Tests\TestCase;

class BoundingBoxText extends TestCase
{
    /**
     * @var Polygon
     */
    protected $polygon;

    protected function setUp()
    {
        $this->polygon = new Polygon();
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
                'east' => 3.1448364,
                'south' => 48.4711003,
                'west' => 1.7440796,
            ),
        );
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
            $this->polygon->add($this->getMockCoordinateReturns($coordinate));
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
}
