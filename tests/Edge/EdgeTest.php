<?php

/*
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools\Tests\Edge;

use League\Geotools\Coordinate\Coordinate;
use League\Geotools\Coordinate\Ellipsoid;
use League\Geotools\Edge\Edge;
use League\Geotools\Tests\TestCase;

/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
class EdgeTest extends TestCase
{
    protected $edge;
    protected $from;
    protected $to;

    protected function setUp()
    {
        $this->edge = new Edge();
        $this->from  = $this->getStubCoordinate();
        $this->to    = $this->getStubCoordinate();
    }

    public function testSetFromValueShouldBeACoordinateInterface()
    {
        $this->edge->setFrom($this->from);
        $from = $this->edge->getFrom();

        $this->assertTrue(is_object($from));
        $this->assertInstanceOf('League\Geotools\Coordinate\CoordinateInterface', $from);
    }

    public function testSetFromShouldReturnTheSameEdgeInstance()
    {
        $edge = $this->edge->setFrom($this->from);

        $this->assertTrue(is_object($edge));
        $this->assertInstanceOf('League\Geotools\Edge\Edge', $edge);
        $this->assertInstanceOf('League\Geotools\Edge\EdgeInterface', $edge);
        $this->assertSame($this->edge, $edge);
    }

    public function testSetToValueShouldBeACoordinateInterface()
    {
        $this->edge->setTo($this->to);
        $to = $this->edge->getTo();

        $this->assertTrue(is_object($to));
        $this->assertInstanceOf('League\Geotools\Coordinate\CoordinateInterface', $to);
    }

    public function testSetToShouldReturnTheSameEdgeInstance()
    {
        $edge = $this->edge->setTo($this->to);

        $this->assertTrue(is_object($edge));
        $this->assertInstanceOf('League\Geotools\Edge\Edge', $edge);
        $this->assertInstanceOf('League\Geotools\Edge\EdgeInterface', $edge);
        $this->assertSame($this->edge, $edge);
    }

    /**
     * @dataProvider coordinatesAndExpectedDegreeForInitialBearingProvider
     */
    public function testInitialBearing($from, $to, $expectedDegree)
    {
        $this->edge->setFrom($this->getMockCoordinateReturns($from));
        $this->edge->setTo($this->getMockCoordinateReturns($to));

        $this->assertEquals($expectedDegree, $this->edge->initialBearing());
    }

    public function coordinatesAndExpectedDegreeForInitialBearingProvider()
    {
        return [
            [
                [48.8234055, 2.3072664],
                [43.296482, 5.36978],
                157
            ],
            [
                ['48.8234055', '2.3072664'],
                ['43.296482', '5.36978'],
                '157'
            ],
            [
                [43.296482, 5.36978],
                [48.8234055, 2.3072664],
                340
            ],
            [
                [-43.296482, -5.36978],
                [-48.8234055, -2.3072664],
                160
            ],
            [
                [35, 45],
                [35, 135],
                60
            ],
        ];
    }

    /**
     * @dataProvider coordinatesAndExpectedDegreeForFinalBearingProvider
     */
    public function testFinalBearing($from, $to, $expectedDegree)
    {
        $this->edge->setFrom($this->getMockCoordinateReturns($from));
        $this->edge->setTo($this->getMockCoordinateReturns($to));

        $this->assertEquals($expectedDegree, $this->edge->finalBearing());
    }

    public function coordinatesAndExpectedDegreeForFinalBearingProvider()
    {
        return [
            [
                [48.8234055, 2.3072664],
                [43.296482, 5.36978],
                160
            ],
            [
                ['48.8234055', '2.3072664'],
                ['43.296482', '5.36978'],
                '160'
            ],
            [
                [43.296482, 5.36978],
                [48.8234055, 2.3072664],
                337
            ],
            [
                [-43.296482, -5.36978],
                [-48.8234055, -2.3072664],
                157
            ],
            [
                [35, 45],
                [35, 135],
                119
            ],
        ];
    }

    /**
     * @dataProvider coordinatesAndExpectedInitialCardinalProvider
     */
    public function testInitialCardinal($from, $to, $expectedCardinal)
    {
        $this->edge->setFrom($this->getMockCoordinateReturns($from));
        $this->edge->setTo($this->getMockCoordinateReturns($to));

        $this->assertEquals($expectedCardinal, $this->edge->initialCardinal());
    }

    public function coordinatesAndExpectedInitialCardinalProvider()
    {
        return [
            [
                [48.8234055, 2.3072664],
                [43.296482, 5.36978],
                'SSE'
            ],
            [
                ['28.8234055', '1.3072664'],
                ['43.296482', '5.36978'],
                'N'
            ],
            [
                [43.296482, 5.36978],
                [48.8234055, 2.3072664],
                'NNW'
            ],
            [
                [-13.296482, -5.36978],
                [-38.8234055, -4.3072664],
                'S'
            ],
            [
                [35, 45],
                [35, 135],
                'ENE'
            ],
        ];
    }

    /**
     * @dataProvider coordinatesAndExpectedFinalCardinalProvider
     */
    public function testFinalCardinal($from, $to, $expectedCardinal)
    {
        $this->edge->setFrom($this->getMockCoordinateReturns($from));
        $this->edge->setTo($this->getMockCoordinateReturns($to));

        $this->assertEquals($expectedCardinal, $this->edge->finalCardinal());
    }

    public function coordinatesAndExpectedFinalCardinalProvider()
    {
        return [
            [
                [48.8234055, 2.3072664],
                [43.296482, 5.36978],
                'SSE'
            ],
            [
                ['28.8234055', '1.3072664'],
                ['43.296482', '5.36978'],
                'NNE'
            ],
            [
                [43.296482, 5.36978],
                [48.8234055, 2.3072664],
                'NNW'
            ],
            [
                [-13.296482, -5.36978],
                [-38.8234055, -4.3072664],
                'S'
            ],
            [
                [35, 45],
                [35, 135],
                'ESE'
            ],
        ];
    }

    /**
     * @dataProvider fromAndToCoordinatesAndExpectedMiddlePointProvider
     */
    public function testMiddle($from, $to, $expectedMiddlePoint)
    {
        $this->edge->setFrom($this->getMockCoordinateReturns($from));
        $this->edge->setTo($this->getMockCoordinateReturns($to));
        $middlePoint = $this->edge->middle();

        $this->assertTrue(is_object($middlePoint));
        $this->assertInstanceOf('League\Geotools\Coordinate\Coordinate', $middlePoint);
        $this->assertInstanceOf('League\Geotools\Coordinate\CoordinateInterface', $middlePoint);
        $this->assertEquals($expectedMiddlePoint->getLatitude(), $middlePoint->getLatitude());
        $this->assertEquals($expectedMiddlePoint->getLongitude(), $middlePoint->getLongitude());
    }

    public function fromAndToCoordinatesAndExpectedMiddlePointProvider()
    {
        return [
            [
                [48.8234055, 2.3072664],
                [43.296482, 5.36978],
                $this->getMockCoordinateReturns([46.070143125815, 3.9152401085931])
            ],
            [
                ['28.8234055', '1.3072664'],
                ['43.296482', '5.36978'],
                $this->getMockCoordinateReturns([36.076935937133, 3.1506401291113])
            ],
            [
                [43.296482, 5.36978],
                [48.8234055, 2.3072664],
                $this->getMockCoordinateReturns(['46.070143125815', '3.9152401085931'])
            ],
            [
                [-13.296482, -5.36978],
                [-38.8234055, -4.3072664],
                $this->getMockCoordinateReturns([-26.060903849478, -4.8973756901009])
            ],
        ];
    }

    public function testMiddleShouldHaveTheSameEllipsoid()
    {
        $FOO = Ellipsoid::createFromArray([
            'name' => 'foo ellipsoid',
            'a'    => 123.0,
            'invF' => 456.0
        ]);

        $this->edge->setFrom($this->getMockCoordinateReturns([1, 2], $FOO));
        $this->edge->setTo($this->getMockCoordinateReturns([3, 4], $FOO));

        $this->assertSame($this->edge->middle()->getEllipsoid(), $FOO);
    }

    /**
     * @dataProvider fromAndBearingAndDistanceAndExpectedDestinationPoint
     */
    public function testDestination($from, $bearing, $distance, $expectedDestinationPoint)
    {
        $WGS84 = Ellipsoid::createFromName(Ellipsoid::WGS84);

        $this->edge->setFrom($this->getMockCoordinateReturns($from, $WGS84));
        $destinationPoint = $this->edge->destination($bearing, $distance);

        $this->assertTrue(is_object($destinationPoint));
        $this->assertInstanceOf('League\Geotools\Coordinate\Coordinate', $destinationPoint);
        $this->assertInstanceOf('League\Geotools\Coordinate\CoordinateInterface', $destinationPoint);
        $this->assertEquals($expectedDestinationPoint->getLatitude(), $destinationPoint->getLatitude());
        $this->assertEquals($expectedDestinationPoint->getLongitude(), $destinationPoint->getLongitude());
    }

    public function fromAndBearingAndDistanceAndExpectedDestinationPoint()
    {
        return [
            [
                [48.8234055, 2.3072664],
                180,
                200000,
                $this->getMockCoordinateReturns([47.026774650075, 2.3072664])
            ],
            [
                ['28.8234055', '1.3072664'],
                95,
                500000,
                $this->getMockCoordinateReturns([28.336641152298, 6.3923716035552])
            ],
            [
                [43.296482, 5.36978],
                37,
                3000,
                $this->getMockCoordinateReturns(['43.318002633989', '5.3920718426221'])
            ],
            [
                [-13.296482, -5.36978],
                166,
                5000000,
                $this->getMockCoordinateReturns([-56.057095935971, 12.44347001977])
            ],
        ];
    }

    public function testDestinationShouldHaveTheSameEllipsoid()
    {
        $FOO = Ellipsoid::createFromArray([
            'name' => 'foo ellipsoid',
            'a'    => 123.0,
            'invF' => 456.0
        ]);

        $this->edge->setFrom($this->getMockCoordinateReturns([1, 2], $FOO));

        $this->assertSame($this->edge->destination(123, 456)->getEllipsoid(), $FOO);
    }

    /**
     * @dataProvider EdgeCoordinatesAndExpectedSameLineStatusProvider
     */
    public function testSameLine($edgeCoordinatesFirst, $edgeCoordinatesSecond, $sameLineStatus)
    {
        $this->edge->setFrom($this->getMockCoordinateReturns($edgeCoordinatesFirst['from']));
        $this->edge->setTo($this->getMockCoordinateReturns($edgeCoordinatesFirst['to']));
        $edgeToComp = new \League\Geotools\Edge\Edge;
        $edgeToComp->setFrom($this->getMockCoordinateReturns($edgeCoordinatesSecond['from']));
        $edgeToComp->setTo($this->getMockCoordinateReturns($edgeCoordinatesSecond['to']));

        $this->assertTrue(is_object($edgeToComp));
        $this->assertInstanceOf('League\Geotools\Edge\Edge', $this->edge);
        $this->assertInstanceOf('League\Geotools\Edge\Edge', $edgeToComp);
        $this->assertEquals($sameLineStatus, $this->edge->isOnSameLine($edgeToComp));
    }

    public function EdgeCoordinatesAndExpectedSameLineStatusProvider()
    {
        return [
            [
                [
                    'from' => [2, 5],
                    'to' => [3, 7]
                ],
                [
                    'from' => [14, 29],
                    'to' => [-35, -69]
                ],
                true
            ],
            [
                [
                    'from' => [48.8234055, 2.3072664],
                    'to' => [43.296482, 5.36978]
                ],
                [
                    'from' => [56.2615, -1.8142427115944],
                    'to' => [15.55886, 20.739423637488]
                ],
                true
            ],
            [
                [
                    'from' => [1, 4],
                    'to' => [2, 8]
                ],
                [
                    'from' => [1, 4],
                    'to' => [2, 7]
                ],
                false
            ],
            [
                [
                    'from' => [48.8234055, 2.3072664],
                    'to' => [43.296482, 5.36978]
                ],
                [
                    'from' => [4.26116, 2.3072664],
                    'to' => [68.5, 8.79635]
                ],
                false
            ],
            [
                [
                    'from' => [48.8234055, 2.3072664],
                    'to' => [43.296482, 5.36978]
                ],
                [
                    'from' => [48.8234055, 2.3072664],
                    'to' => [null, null]
                ],
                false
            ],
        ];
    }

    /**
     * @dataProvider EdgeCoordinatesOriginalCoordinatesAndOtherOneProvider
     */
    public function testGetOtherCoordinate($edgeCoordinates, $oneCoordinate, $otherCoordinate)
    {
        $this->edge->setFrom($edgeCoordinates['from']);
        $this->edge->setTo($edgeCoordinates['to']);

        $this->assertInstanceOf('League\Geotools\Edge\Edge', $this->edge);
        $this->assertEquals($otherCoordinate, $this->edge->getOtherCoordinate($oneCoordinate));
    }

    public function EdgeCoordinatesOriginalCoordinatesAndOtherOneProvider()
    {
        return [
            [
                [
                    'from' => new Coordinate([48.8234055, 2.3072664]),
                    'to' => new Coordinate([43.296482, 5.36978])
                ],
                new Coordinate([48.8234055, 2.3072664]),
                new Coordinate([43.296482, 5.36978])
            ],
            [
                [
                    'from' => new Coordinate([48.8234055, 2.3072664]),
                    'to' => new Coordinate([43.296482, 5.36978])
                ],
                new Coordinate([43.296482, 5.36978]),
                new Coordinate([48.8234055, 2.3072664])
            ],
            [
                [
                    'from' => new Coordinate([48.8234055, 2.3072664]),
                    'to' => new Coordinate([43.296482, 5.36978])
                ],
                new Coordinate([2, 5]),
                null
            ],
        ];
    }

}
