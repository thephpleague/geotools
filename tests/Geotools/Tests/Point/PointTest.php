<?php

/**
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Geotools\Tests\Point;

use Geotools\Tests\TestCase;
use Geotools\Point\Point;
use Geotools\Coordinate\Coordinate;
use Geotools\Coordinate\Ellipsoid;

/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
class PointTest extends TestCase
{
    protected $point;
    protected $from;
    protected $to;

    protected function setUp()
    {
        $this->point = new Point();
        $this->from  = $this->getStubCoordinate();
        $this->to    = $this->getStubCoordinate();
        }

    public function testSetFromValueShouldBeACoordinateInterface()
    {
        $this->point->setFrom($this->from);
        $from = $this->point->getFrom();

        $this->assertTrue(is_object($from));
        $this->assertInstanceOf('Geotools\Coordinate\CoordinateInterface', $from);
    }

    public function testSetFromShouldReturnTheSamePointInstance()
    {
        $point = $this->point->setFrom($this->from);

        $this->assertTrue(is_object($point));
        $this->assertInstanceOf('Geotools\Point\Point', $point);
        $this->assertInstanceOf('Geotools\Point\PointInterface', $point);
        $this->assertSame($this->point, $point);
    }

    public function testSetToValueShouldBeACoordinateInterface()
    {
        $this->point->setTo($this->to);
        $to = $this->point->getTo();

        $this->assertTrue(is_object($to));
        $this->assertInstanceOf('Geotools\Coordinate\CoordinateInterface', $to);
    }

    public function testSetToShouldReturnTheSamePointInstance()
    {
        $point = $this->point->setTo($this->to);

        $this->assertTrue(is_object($point));
        $this->assertInstanceOf('Geotools\Point\Point', $point);
        $this->assertInstanceOf('Geotools\Point\PointInterface', $point);
        $this->assertSame($this->point, $point);
    }

    /**
     * @dataProvider coordinatesAndExpectedDegreeForInitialBearingProvider
     */
    public function testInitialBearing($from, $to, $expectedDegree)
    {
        $this->point->setFrom($this->getMockCoordinateReturns($from));
        $this->point->setTo($this->getMockCoordinateReturns($to));

        $this->assertEquals($expectedDegree[0], $this->point->initialBearing());
    }

    public function coordinatesAndExpectedDegreeForInitialBearingProvider()
    {
        return array(
            array(
                array(48.8234055, 2.3072664),
                array(43.296482, 5.36978),
                array(157)
            ),
            array(
                array('48.8234055', '2.3072664'),
                array('43.296482', '5.36978'),
                array('157')
            ),
            array(
                array(43.296482, 5.36978),
                array(48.8234055, 2.3072664),
                array(340)
            ),
            array(
                array(-43.296482, -5.36978),
                array(-48.8234055, -2.3072664),
                array(160)
            ),
            array(
                array(35, 45),
                array(35, 135),
                array(60)
            ),
        );
    }

    /**
     * @dataProvider coordinatesAndExpectedDegreeForFinalBearingProvider
     */
    public function testFinalBearing($from, $to, $expectedDegree)
    {
        $this->point->setFrom($this->getMockCoordinateReturns($from));
        $this->point->setTo($this->getMockCoordinateReturns($to));

        $this->assertEquals($expectedDegree[0], $this->point->finalBearing());
    }

    public function coordinatesAndExpectedDegreeForFinalBearingProvider()
    {
        return array(
            array(
                array(48.8234055, 2.3072664),
                array(43.296482, 5.36978),
                array(160)
            ),
            array(
                array('48.8234055', '2.3072664'),
                array('43.296482', '5.36978'),
                array('160')
            ),
            array(
                array(43.296482, 5.36978),
                array(48.8234055, 2.3072664),
                array(337)
            ),
            array(
                array(-43.296482, -5.36978),
                array(-48.8234055, -2.3072664),
                array(157)
            ),
            array(
                array(35, 45),
                array(35, 135),
                array(119)
            ),
        );
    }

    /**
     * @dataProvider coordinatesAndExpectedInitialCardinalProvider
     */
    public function testInitialCardinal($from, $to, $expectedCardinal)
    {
        $this->point->setFrom($this->getMockCoordinateReturns($from));
        $this->point->setTo($this->getMockCoordinateReturns($to));

        $this->assertEquals($expectedCardinal[0], $this->point->initialCardinal());
    }

    public function coordinatesAndExpectedInitialCardinalProvider()
    {
        return array(
            array(
                array(48.8234055, 2.3072664),
                array(43.296482, 5.36978),
                array('SSE')
            ),
            array(
                array('28.8234055', '1.3072664'),
                array('43.296482', '5.36978'),
                array('N')
            ),
            array(
                array(43.296482, 5.36978),
                array(48.8234055, 2.3072664),
                array('NNW')
            ),
            array(
                array(-13.296482, -5.36978),
                array(-38.8234055, -4.3072664),
                array('S')
            ),
            array(
                array(35, 45),
                array(35, 135),
                array('ENE')
            ),
        );
    }

    /**
     * @dataProvider coordinatesAndExpectedFinalCardinalProvider
     */
    public function testFinalCardinal($from, $to, $expectedCardinal)
    {
        $this->point->setFrom($this->getMockCoordinateReturns($from));
        $this->point->setTo($this->getMockCoordinateReturns($to));

        $this->assertEquals($expectedCardinal[0], $this->point->finalCardinal());
    }

    public function coordinatesAndExpectedFinalCardinalProvider()
    {
        return array(
            array(
                array(48.8234055, 2.3072664),
                array(43.296482, 5.36978),
                array('SSE')
            ),
            array(
                array('28.8234055', '1.3072664'),
                array('43.296482', '5.36978'),
                array('NNE')
            ),
            array(
                array(43.296482, 5.36978),
                array(48.8234055, 2.3072664),
                array('NNW')
            ),
            array(
                array(-13.296482, -5.36978),
                array(-38.8234055, -4.3072664),
                array('S')
            ),
            array(
                array(35, 45),
                array(35, 135),
                array('ESE')
            ),
        );
    }

    /**
     * @dataProvider fromAndToCoordinatesAndExpectedMiddlePointProvider
     */
    public function testMiddle($from, $to, $expectedMiddlePoint)
    {
        $this->point->setFrom($this->getMockCoordinateReturns($from));
        $this->point->setTo($this->getMockCoordinateReturns($to));
        $middlePoint = $this->point->middle();

        $this->assertTrue(is_object($middlePoint));
        $this->assertInstanceOf('Geotools\Coordinate\Coordinate', $middlePoint);
        $this->assertInstanceOf('Geotools\Coordinate\CoordinateInterface', $middlePoint);
        $this->assertEquals($expectedMiddlePoint[0]->getLatitude(), $middlePoint->getLatitude());
        $this->assertEquals($expectedMiddlePoint[0]->getLongitude(), $middlePoint->getLongitude());
    }

    public function fromAndToCoordinatesAndExpectedMiddlePointProvider()
    {
        return array(
            array(
                array(48.8234055, 2.3072664),
                array(43.296482, 5.36978),
                array($this->getMockCoordinateReturns(array(46.070143125815, 3.9152401085931)))
            ),
            array(
                array('28.8234055', '1.3072664'),
                array('43.296482', '5.36978'),
                array($this->getMockCoordinateReturns(array(36.076935937133, 3.1506401291113)))
            ),
            array(
                array(43.296482, 5.36978),
                array(48.8234055, 2.3072664),
                array($this->getMockCoordinateReturns(array('46.070143125815', '3.9152401085931')))
            ),
            array(
                array(-13.296482, -5.36978),
                array(-38.8234055, -4.3072664),
                array($this->getMockCoordinateReturns(array(-26.060903849478, -4.8973756901009)))
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

        $this->point->setFrom($this->getMockCoordinateReturns(array(1, 2), $FOO));
        $this->point->setTo($this->getMockCoordinateReturns(array(3, 4), $FOO));

        $this->assertSame($this->point->middle()->getEllipsoid(), $FOO);
    }

    /**
     * @dataProvider fromAndBearingAndDistanceAndExpectedDestinationPoint
     */
    public function testDestination($from, $bearing, $distance, $expectedDestinationPoint)
    {
        $WGS84 = Ellipsoid::createFromName(Ellipsoid::WGS84);

        $this->point->setFrom($this->getMockCoordinateReturns($from, $WGS84));
        $destionationPoint = $this->point->destination($bearing[0], $distance[0]);

        $this->assertTrue(is_object($destionationPoint));
        $this->assertInstanceOf('Geotools\Coordinate\Coordinate', $destionationPoint);
        $this->assertInstanceOf('Geotools\Coordinate\CoordinateInterface', $destionationPoint);
        $this->assertEquals($expectedDestinationPoint[0]->getLatitude(), $destionationPoint->getLatitude());
        $this->assertEquals($expectedDestinationPoint[0]->getLongitude(), $destionationPoint->getLongitude());
    }

    public function fromAndBearingAndDistanceAndExpectedDestinationPoint()
    {
        return array(
            array(
                array(48.8234055, 2.3072664),
                array(180),
                array(200000),
                array($this->getMockCoordinateReturns(array(47.026774650075, 2.3072664)))
            ),
            array(
                array('28.8234055', '1.3072664'),
                array(95),
                array(500000),
                array($this->getMockCoordinateReturns(array(28.336641152298, 6.3923716035552)))
            ),
            array(
                array(43.296482, 5.36978),
                array(37),
                array(3000),
                array($this->getMockCoordinateReturns(array('43.318002633989', '5.3920718426221')))
            ),
            array(
                array(-13.296482, -5.36978),
                array(166),
                array(5000000),
                array($this->getMockCoordinateReturns(array(-56.057095935971, 12.44347001977)))
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

        $this->point->setFrom($this->getMockCoordinateReturns(array(1, 2), $FOO));

        $this->assertSame($this->point->destination(123, 456)->getEllipsoid(), $FOO);
    }
}
