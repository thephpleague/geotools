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
                'northWestCoordinate' => array(49.3895245, 3.1448364),
                'southEastCoordinate' => array(48.4711003, 1.7440796),
            ),
        );
    }

    /**
     * @dataProvider polygonAndExpectedNorthWestAndSouthEastCoordinates
     * @param array $polygonCoordinates
     * @param array $northWestCoordinate
     * @param array $southEastCoordinate
     */
    public function testPolygonBoundingBox($polygonCoordinates, $northWestCoordinate, $southEastCoordinate)
    {
        foreach ($polygonCoordinates as $coordinate) {
            $this->polygon->add($this->getMockCoordinateReturns($coordinate));
        }

        $this->assertEquals(
            $northWestCoordinate[0],
            $this->polygon->getBoundingBox()->getNorthWestCoordinate()->getLatitude()
        );
        $this->assertEquals(
            $northWestCoordinate[1],
            $this->polygon->getBoundingBox()->getNorthWestCoordinate()->getLongitude()
        );

        $this->assertEquals(
            $southEastCoordinate[0],
            $this->polygon->getBoundingBox()->getSouthEastCoordinate()->getLatitude()
        );
        $this->assertEquals(
            $southEastCoordinate[1],
            $this->polygon->getBoundingBox()->getSouthEastCoordinate()->getLongitude()
        );
    }
}
