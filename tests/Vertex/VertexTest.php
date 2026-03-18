<?php

/*
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools\Tests\Vertex;

use League\Geotools\Vertex\Vertex;
use PHPUnit\Framework\Attributes\DataProvider;
use League\Geotools\Coordinate\Coordinate;
use League\Geotools\Coordinate\Ellipsoid;
/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
class VertexTest extends \League\Geotools\Tests\TestCase
{
    protected $vertex;
    protected $from;
    protected $to;
    protected function setup(): void
    {
        $this->vertex = new Vertex;
        $this->from  = $this->getStubCoordinate();
        $this->to    = $this->getStubCoordinate();
        }
    public function testSetFromValueShouldBeACoordinateInterface()
    {
        $this->vertex->setFrom($this->from);
        $from = $this->vertex->getFrom();
        $this->assertTrue(is_object($from));
        $this->assertInstanceOf('League\Geotools\Coordinate\CoordinateInterface', $from);
    }
    public function testSetFromShouldReturnTheSameVertexInstance()
    {
        $vertex = $this->vertex->setFrom($this->from);
        $this->assertTrue(is_object($vertex));
        $this->assertInstanceOf('League\Geotools\Vertex\Vertex', $vertex);
        $this->assertInstanceOf('League\Geotools\Vertex\VertexInterface', $vertex);
        $this->assertSame($this->vertex, $vertex);
    }
    public function testSetToValueShouldBeACoordinateInterface()
    {
        $this->vertex->setTo($this->to);
        $to = $this->vertex->getTo();
        $this->assertTrue(is_object($to));
        $this->assertInstanceOf('League\Geotools\Coordinate\CoordinateInterface', $to);
    }
    public function testSetToShouldReturnTheSameVertexInstance()
    {
        $vertex = $this->vertex->setTo($this->to);
        $this->assertTrue(is_object($vertex));
        $this->assertInstanceOf('League\Geotools\Vertex\Vertex', $vertex);
        $this->assertInstanceOf('League\Geotools\Vertex\VertexInterface', $vertex);
        $this->assertSame($this->vertex, $vertex);
    }
    #[DataProvider('coordinatesAndExpectedDegreeForInitialBearingProvider')]
    public function testInitialBearing($from, $to, $expectedDegree)
    {
        $this->vertex->setFrom($this->getMockCoordinateReturns($from));
        $this->vertex->setTo($this->getMockCoordinateReturns($to));
        $this->assertEquals($expectedDegree, $this->vertex->initialBearing());
    }
    public static function coordinatesAndExpectedDegreeForInitialBearingProvider()
    {
        return array(
            array(
                array(48.8234055, 2.3072664),
                array(43.296482, 5.36978),
                157.85228357999392
            ),
            array(
                array('48.8234055', '2.3072664'),
                array('43.296482', '5.36978'),
                '157.85228357999392'
            ),
            array(
                array(43.296482, 5.36978),
                array(48.8234055, 2.3072664),
                340.06031595031203
            ),
            array(
                array(-43.296482, -5.36978),
                array(-48.8234055, -2.3072664),
                160.0603159503121
            ),
            array(
                array(35, 45),
                array(35, 135),
                60.16243352168624
            ),
        );
    }
    #[DataProvider('coordinatesAndExpectedDegreeForFinalBearingProvider')]
    public function testFinalBearing($from, $to, $expectedDegree)
    {
        $this->vertex->setFrom($this->getMockCoordinateReturns($from));
        $this->vertex->setTo($this->getMockCoordinateReturns($to));
        $this->assertEquals($expectedDegree, $this->vertex->finalBearing());
    }
    public static function coordinatesAndExpectedDegreeForFinalBearingProvider()
    {
        return array(
            array(
                array(48.8234055, 2.3072664),
                array(43.296482, 5.36978),
                160.0603159503121
            ),
            array(
                array('48.8234055', '2.3072664'),
                array('43.296482', '5.36978'),
                '160.0603159503121'
            ),
            array(
                array(43.296482, 5.36978),
                array(48.8234055, 2.3072664),
                337.8522835799939
            ),
            array(
                array(-43.296482, -5.36978),
                array(-48.8234055, -2.3072664),
                157.85228357999392
            ),
            array(
                array(35, 45),
                array(35, 135),
                119.83756647831376
            ),
        );
    }
    #[DataProvider('coordinatesAndExpectedInitialCardinalProvider')]
    public function testInitialCardinal($from, $to, $expectedCardinal)
    {
        $this->vertex->setFrom($this->getMockCoordinateReturns($from));
        $this->vertex->setTo($this->getMockCoordinateReturns($to));
        $this->assertEquals($expectedCardinal, $this->vertex->initialCardinal());
    }
    public static function coordinatesAndExpectedInitialCardinalProvider()
    {
        return array(
            array(
                array(48.8234055, 2.3072664),
                array(43.296482, 5.36978),
                'SSE'
            ),
            array(
                array('28.8234055', '1.3072664'),
                array('43.296482', '5.36978'),
                'NNE'
            ),
            array(
                array(43.296482, 5.36978),
                array(48.8234055, 2.3072664),
                'NNW'
            ),
            array(
                array(-13.296482, -5.36978),
                array(-38.8234055, -4.3072664),
                'S'
            ),
            array(
                array(35, 45),
                array(35, 135),
                'ENE'
            ),
        );
    }
    #[DataProvider('coordinatesAndExpectedFinalCardinalProvider')]
    public function testFinalCardinal($from, $to, $expectedCardinal)
    {
        $this->vertex->setFrom($this->getMockCoordinateReturns($from));
        $this->vertex->setTo($this->getMockCoordinateReturns($to));
        $this->assertEquals($expectedCardinal, $this->vertex->finalCardinal());
    }
    public static function coordinatesAndExpectedFinalCardinalProvider()
    {
        return array(
            array(
                array(48.8234055, 2.3072664),
                array(43.296482, 5.36978),
                'SSE'
            ),
            array(
                array('28.8234055', '1.3072664'),
                array('43.296482', '5.36978'),
                'NNE'
            ),
            array(
                array(43.296482, 5.36978),
                array(48.8234055, 2.3072664),
                'NNW'
            ),
            array(
                array(-13.296482, -5.36978),
                array(-38.8234055, -4.3072664),
                'S'
            ),
            array(
                array(35, 45),
                array(35, 135),
                'ESE'
            ),
        );
    }
    #[DataProvider('fromAndToCoordinatesAndExpectedMiddlePointProvider')]
    public function testMiddle($from, $to, $expectedMiddlePoint)
    {
        $this->vertex->setFrom($this->getMockCoordinateReturns($from));
        $this->vertex->setTo($this->getMockCoordinateReturns($to));
        $middlePoint = $this->vertex->middle();
        $this->assertTrue(is_object($middlePoint));
        $this->assertInstanceOf('League\Geotools\Coordinate\Coordinate', $middlePoint);
        $this->assertInstanceOf('League\Geotools\Coordinate\CoordinateInterface', $middlePoint);
        $this->assertEquals($expectedMiddlePoint->getLatitude(), $middlePoint->getLatitude());
        $this->assertEquals($expectedMiddlePoint->getLongitude(), $middlePoint->getLongitude());
    }
    public static function fromAndToCoordinatesAndExpectedMiddlePointProvider()
    {
        return array(
            array(
                array(48.8234055, 2.3072664),
                array(43.296482, 5.36978),
                new Coordinate([46.0701431258146, 3.9152401085931])
            ),
            array(
                array('28.8234055', '1.3072664'),
                array('43.296482', '5.36978'),
                new Coordinate([36.0769359371328, 3.1506401291113])
            ),
            array(
                array(43.296482, 5.36978),
                array(48.8234055, 2.3072664),
                new Coordinate([46.0701431258146, 3.9152401085931])
            ),
            array(
                array(-13.296482, -5.36978),
                array(-38.8234055, -4.3072664),
                new Coordinate([-26.0609038494776, -4.8973756901009])
            ),
        );
    }
    public function testMiddleShouldHaveTheSameEllipsoid()
    {
        $FOO = Ellipsoid::createFromArray(array(
            'name' => 'foo ellipsoid',
            'a'    => 123.0,
            'invF' => 456.0
        ));
        $this->vertex->setFrom($this->getMockCoordinateReturns(array(1, 2), $FOO));
        $this->vertex->setTo($this->getMockCoordinateReturns(array(3, 4), $FOO));
        $this->assertSame($this->vertex->middle()->getEllipsoid(), $FOO);
    }
    #[DataProvider('fromAndBearingAndDistanceAndExpectedDestinationPoint')]
    public function testDestination($from, $bearing, $distance, $expectedDestinationPoint)
    {
        $WGS84 = Ellipsoid::createFromName(Ellipsoid::WGS84);
        $this->vertex->setFrom($this->getMockCoordinateReturns($from, $WGS84));
        $destinationPoint = $this->vertex->destination($bearing, $distance);
        $this->assertTrue(is_object($destinationPoint));
        $this->assertInstanceOf('League\Geotools\Coordinate\Coordinate', $destinationPoint);
        $this->assertInstanceOf('League\Geotools\Coordinate\CoordinateInterface', $destinationPoint);
        $this->assertEquals($expectedDestinationPoint->getLatitude(), $destinationPoint->getLatitude());
        $this->assertEquals($expectedDestinationPoint->getLongitude(), $destinationPoint->getLongitude());
    }
    public static function fromAndBearingAndDistanceAndExpectedDestinationPoint()
    {
        return array(
            array(
                array(48.8234055, 2.3072664),
                180,
                200000,
                new Coordinate([47.026774931761, 2.3072664])
            ),
            array(
                array('28.8234055', '1.3072664'),
                95,
                500000,
                new Coordinate([28.3366412434111, 6.3923708101944])
            ),
            array(
                array(43.296482, 5.36978),
                37,
                3000,
                new Coordinate([43.3180026306153, 5.3920718391258])
            ),
            array(
                array(-13.296482, -5.36978),
                166,
                5000000,
                new Coordinate([-56.0570895504523, 12.4434647018672])
            ),
        );
    }
    public function testDestinationShouldHaveTheSameEllipsoid()
    {
        $FOO = Ellipsoid::createFromArray(array(
            'name' => 'foo ellipsoid',
            'a'    => 123.0,
            'invF' => 456.0
        ));
        $this->vertex->setFrom($this->getMockCoordinateReturns(array(1, 2), $FOO));
        $this->assertSame($this->vertex->destination(123, 456)->getEllipsoid(), $FOO);
    }
    #[DataProvider('VertexCoordinatesAndExpectedSameLineStatusProvider')]
    public function testSameLine($vertexCoordinatesFirst, $vertexCoordinatesSecond, $sameLineStatus)
    {
        $this->vertex->setFrom($this->getMockCoordinateReturns($vertexCoordinatesFirst['from']));
        $this->vertex->setTo($this->getMockCoordinateReturns($vertexCoordinatesFirst['to']));
        $vertexToComp = new Vertex;
        $vertexToComp->setFrom($this->getMockCoordinateReturns($vertexCoordinatesSecond['from']));
        $vertexToComp->setTo($this->getMockCoordinateReturns($vertexCoordinatesSecond['to']));
        $this->assertTrue(is_object($vertexToComp));
        $this->assertInstanceOf('League\Geotools\Vertex\Vertex', $this->vertex);
        $this->assertInstanceOf('League\Geotools\Vertex\Vertex', $vertexToComp);
        $this->assertEquals($sameLineStatus, $this->vertex->isOnSameLine($vertexToComp));
    }
    public static function VertexCoordinatesAndExpectedSameLineStatusProvider()
    {
        return array(
            array(
                array(
                    'from' => array(2, 5),
                    'to' => array(3, 7)
                ),
                array(
                    'from' => array(14, 29),
                    'to' => array(-35, -69)
                ),
                true
            ),
            array(
                array(
                    'from' => array(48.8234055, 2.3072664),
                    'to' => array(43.296482, 5.36978)
                ),
                array(
                    'from' => array(56.2615, -1.8142427115944),
                    'to' => array(15.55886, 20.739423637488)
                ),
                true
            ),
            array(
                array(
                    'from' => array(1, 4),
                    'to' => array(2, 8)
                ),
                array(
                    'from' => array(1, 4),
                    'to' => array(2, 7)
                ),
                false
            ),
            array(
                array(
                    'from' => array(48.8234055, 2.3072664),
                    'to' => array(43.296482, 5.36978)
                ),
                array(
                    'from' => array(4.26116, 2.3072664),
                    'to' => array(68.5, 8.79635)
                ),
                false
            ),
            array(
                array(
                    'from' => array(48.8234055, 2.3072664),
                    'to' => array(43.296482, 5.36978)
                ),
                array(
                    'from' => array(48.8234055, 2.3072664),
                    'to' => array(null, null)
                ),
                false
            ),
        );
    }
    #[DataProvider('VertexCoordinatesOriginalCoordinatesAndOtherOneProvider')]
    public function testGetOtherCoordinate($vertexCoordinates, $oneCoordinate, $otherCoordinate)
    {
        $this->vertex->setFrom($vertexCoordinates['from']);
        $this->vertex->setTo($vertexCoordinates['to']);
        $this->assertInstanceOf('League\Geotools\Vertex\Vertex', $this->vertex);
        $this->assertEquals($otherCoordinate, $this->vertex->getOtherCoordinate($oneCoordinate));
    }
    public static function VertexCoordinatesOriginalCoordinatesAndOtherOneProvider()
    {
        return array(
            array(
                array(
                    'from' => new Coordinate(array(48.8234055, 2.3072664)),
                    'to' => new Coordinate(array(43.296482, 5.36978))
                ),
                new Coordinate(array(48.8234055, 2.3072664)),
                new Coordinate(array(43.296482, 5.36978))
            ),
            array(
                array(
                    'from' => new Coordinate(array(48.8234055, 2.3072664)),
                    'to' => new Coordinate(array(43.296482, 5.36978))
                ),
                new Coordinate(array(43.296482, 5.36978)),
                new Coordinate(array(48.8234055, 2.3072664))
            ),
            array(
                array(
                    'from' => new Coordinate(array(48.8234055, 2.3072664)),
                    'to' => new Coordinate(array(43.296482, 5.36978))
                ),
                new Coordinate(array(2, 5)),
                null
            ),
        );
    }
    #[DataProvider('VertexCoordinatesAndExpectedDeterminantValueProvider')]
    public function testGetDeterminant($vertexCoordinatesFirst, $vertexCoordinatesSecond, $determinantValue)
    {
        $this->vertex->setFrom($this->getMockCoordinateReturns($vertexCoordinatesFirst['from']));
        $this->vertex->setTo($this->getMockCoordinateReturns($vertexCoordinatesFirst['to']));
        $vertexToComp = new Vertex;
        $vertexToComp->setFrom($this->getMockCoordinateReturns($vertexCoordinatesSecond['from']));
        $vertexToComp->setTo($this->getMockCoordinateReturns($vertexCoordinatesSecond['to']));
        $this->assertInstanceOf('League\Geotools\Vertex\Vertex', $this->vertex);
        $this->assertInstanceOf('League\Geotools\Vertex\Vertex', $vertexToComp);
        $this->assertEquals($determinantValue, $this->vertex->getDeterminant($vertexToComp));
    }
    public static function VertexCoordinatesAndExpectedDeterminantValueProvider()
    {
        return array(
            array(
                array(
                    'from' => array(2, 5),
                    'to' => array(3, 7)
                ),
                array(
                    'from' => array(14, 29),
                    'to' => array(-35, -69)
                ),
                0
            ),
            array(
                array(
                    'from' => array(48.8234055, 2.3072664),
                    'to' => array(15.55886, 20.739423637488)
                ),
                array(
                    'from' => array(56.2615, -1.8142427115944),
                    'to' => array(73.588101,45.703125)
                ),
                '-1900.01027430'
            ),
            array(
                array(
                    'from' => array(1, 4),
                    'to' => array(2, 8)
                ),
                array(
                    'from' => array(1, 4),
                    'to' => array(2, 7)
                ),
                '-1.00000000'
            ),
            array(
                array(
                    'from' => array(48.8234055, 2.3072664),
                    'to' => array(43.296482, 5.36978)
                ),
                array(
                    'from' => array(4.26116, 2.3072664),
                    'to' => array(68.5, 8.79635)
                ),
                '-232.59698978'
            ),
        );
    }
}
