<?php
namespace League\Geotools\Tests\Polygon;

use League\Geotools\Coordinate\Coordinate;
use League\Geotools\Polygon\Polygon;
use League\Geotools\Tests\TestCase;

class PolygonTest extends TestCase
{
    /**
     * @var Polygon
     */
    protected $polygon;

    protected function setUp()
    {
        $this->polygon = new Polygon();
    }

    public function polygonAndExpectedMaximumAndMinimumCoordinates()
    {
        return array(
            array(
                'polygonCoordinates' => array(
                    array(48.9675969, 1.7440796),
                    array(48.4711003, 2.5268555),
                    array(48.9279131, 3.1448364),
                    array(49.3895245, 2.6119995)
                ),
                'maximumCoordinate' => array(49.3895245, 3.1448364),
                'minimumCoordinate' => array(48.4711003, 1.7440796),
            ),
        );
    }

    /**
     * @dataProvider polygonAndExpectedMaximumAndMinimumCoordinates
     * @param array $polygonCoordinates
     * @param array $maximumCoordinate
     * @param array $minimumCoordinate
     */
    public function testMaximumAndMinimumCoordinate($polygonCoordinates, $maximumCoordinate, $minimumCoordinate)
    {
        foreach ($polygonCoordinates as $coordinate) {
            $this->polygon->add($this->getMockCoordinateReturns($coordinate));
        }

        $this->assertEquals($maximumCoordinate[0], $this->polygon->getMaximumCoordinate()->getLatitude());
        $this->assertEquals($maximumCoordinate[1], $this->polygon->getMaximumCoordinate()->getLongitude());

        $this->assertEquals($minimumCoordinate[0], $this->polygon->getMinimumCoordinate()->getLatitude());
        $this->assertEquals($minimumCoordinate[1], $this->polygon->getMinimumCoordinate()->getLongitude());
    }

    /**
     * @dataProvider polygonAndExpectedMaximumAndMinimumCoordinates
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
    public function testPointNotOnBoundary($polygonCoordinates, $pointOnBoundaryCoordinates, $pointNotOnBoundaryCoordinates)
    {
        $this->polygon->set($polygonCoordinates);
        foreach ($pointNotOnBoundaryCoordinates as $pointNotOnBoundaryCoordinate) {
            $this->assertFalse($this->polygon->pointOnBoundary(new Coordinate($pointNotOnBoundaryCoordinate)));
        }
    }
}