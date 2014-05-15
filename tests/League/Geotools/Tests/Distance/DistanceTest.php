<?php

/**
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools\Tests\Distance;

use League\Geotools\Tests\TestCase;
use League\Geotools\Distance\Distance;
use League\Geotools\Coordinate\Ellipsoid;

/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
class DistanceTest extends TestCase
{
    protected $distance;
    protected $from;
    protected $to;
    protected $coordA;
    protected $coordB;

    protected function setUp()
    {
        $this->distance = new TestableDistance();
        $this->from     = $this->getStubCoordinate();
        $this->to       = $this->getStubCoordinate();
        $this->coordA   = array(48.8234055, 2.3072664);
        $this->coordB   = array(43.296482, 5.36978);
    }

    public function testSetFromValueShouldBeACoordinateInterface()
    {
        $this->distance->setFrom($this->from);
        $from = $this->distance->getFrom();

        $this->assertTrue(is_object($from));
        $this->assertInstanceOf('League\Geotools\Coordinate\CoordinateInterface', $from);
    }

    public function testSetFromShouldReturnTheSameDistanceInstance()
    {
        $distance = $this->distance->setFrom($this->from);

        $this->assertTrue(is_object($distance));
        $this->assertInstanceOf('League\Geotools\Distance\Distance', $distance);
        $this->assertInstanceOf('League\Geotools\Distance\DistanceInterface', $distance);
        $this->assertSame($this->distance, $distance);
    }

    public function testSetToValueShouldBeACoordinateInterface()
    {
        $this->distance->setTo($this->to);
        $to = $this->distance->getTo();

        $this->assertTrue(is_object($to));
        $this->assertInstanceOf('League\Geotools\Coordinate\CoordinateInterface', $to);
    }

    public function testSetToShouldReturnTheSameDistanceInstance()
    {
        $distance = $this->distance->setTo($this->to);

        $this->assertTrue(is_object($distance));
        $this->assertInstanceOf('League\Geotools\Distance\Distance', $distance);
        $this->assertInstanceOf('League\Geotools\Distance\DistanceInterface', $distance);
        $this->assertSame($this->distance, $distance);
    }

    public function testIn()
    {
        $distance = $this->distance->in('foo');

        $this->assertSame('foo', $distance->getIn());
    }

    public function testInShouldReturnTheSameDistanceInstance()
    {
        $distance = $this->distance->in('foo');

        $this->assertTrue(is_object($distance));
        $this->assertInstanceOf('League\Geotools\Distance\Distance', $distance);
        $this->assertInstanceOf('League\Geotools\Distance\DistanceInterface', $distance);
        $this->assertSame($this->distance, $distance);
    }

    /**
     * @dataProvider ellipsoidInstanceAndExpectedResultProvider
     */
    public function testFlatDistance($ellipsoid, $result)
    {
        $this->distance->setFrom($this->getMockCoordinateReturns($this->coordA, $ellipsoid));
        $this->distance->setTo($this->getMockCoordinateReturns($this->coordB, $ellipsoid));
        $this->assertEquals($result['flat']['m'], $this->distance->flat(), '', 0.00001);

        $this->distance->setFrom($this->getMockCoordinateReturns($this->coordA, $ellipsoid));
        $this->distance->setTo($this->getMockCoordinateReturns($this->coordB, $ellipsoid));
        $this->assertEquals($result['flat']['km'], $this->distance->in('km')->flat(), '', 0.00001);

        $this->distance->setFrom($this->getMockCoordinateReturns($this->coordA, $ellipsoid));
        $this->distance->setTo($this->getMockCoordinateReturns($this->coordB, $ellipsoid));
        $this->assertEquals($result['flat']['mi'], $this->distance->in('mi')->flat(), '', 0.00001);

        $this->distance->setFrom($this->getMockCoordinateReturns($this->coordA, $ellipsoid));
        $this->distance->setTo($this->getMockCoordinateReturns($this->coordB, $ellipsoid));
        $this->assertEquals($result['flat']['ft'], $this->distance->in('ft')->flat(), '', 0.00001);
    }

    /**
     * @dataProvider ellipsoidInstanceAndExpectedResultProvider
     */
    public function testGreatCircleDistance($ellipsoid, $result)
    {
        $this->distance->setFrom($this->getMockCoordinateReturns($this->coordA, $ellipsoid));
        $this->distance->setTo($this->getMockCoordinateReturns($this->coordB, $ellipsoid));
        $this->assertEquals($result['greatCircle']['m'], $this->distance->greatCircle(), '', 0.00001);

        $this->distance->setFrom($this->getMockCoordinateReturns($this->coordA, $ellipsoid));
        $this->distance->setTo($this->getMockCoordinateReturns($this->coordB, $ellipsoid));
        $this->assertEquals($result['greatCircle']['km'], $this->distance->in('km')->greatCircle(), '', 0.00001);

        $this->distance->setFrom($this->getMockCoordinateReturns($this->coordA, $ellipsoid));
        $this->distance->setTo($this->getMockCoordinateReturns($this->coordB, $ellipsoid));
        $this->assertEquals($result['greatCircle']['mi'], $this->distance->in('mi')->greatCircle(), '', 0.00001);

        $this->distance->setFrom($this->getMockCoordinateReturns($this->coordA, $ellipsoid));
        $this->distance->setTo($this->getMockCoordinateReturns($this->coordB, $ellipsoid));
        $this->assertEquals($result['greatCircle']['ft'], $this->distance->in('ft')->greatCircle(), '', 0.00001);
    }

    /**
     * @dataProvider ellipsoidInstanceAndExpectedResultProvider
     */
    public function testHaversineDistance($ellipsoid, $result)
    {
        $this->distance->setFrom($this->getMockCoordinateReturns($this->coordA, $ellipsoid));
        $this->distance->setTo($this->getMockCoordinateReturns($this->coordB, $ellipsoid));
        $this->assertEquals($result['haversine']['m'], $this->distance->haversine(), '', 0.00001);

        $this->distance->setFrom($this->getMockCoordinateReturns($this->coordA, $ellipsoid));
        $this->distance->setTo($this->getMockCoordinateReturns($this->coordB, $ellipsoid));
        $this->assertEquals($result['haversine']['km'], $this->distance->in('km')->haversine(), '', 0.00001);

        $this->distance->setFrom($this->getMockCoordinateReturns($this->coordA, $ellipsoid));
        $this->distance->setTo($this->getMockCoordinateReturns($this->coordB, $ellipsoid));
        $this->assertEquals($result['haversine']['mi'], $this->distance->in('mi')->haversine(), '', 0.00001);

        $this->distance->setFrom($this->getMockCoordinateReturns($this->coordA, $ellipsoid));
        $this->distance->setTo($this->getMockCoordinateReturns($this->coordB, $ellipsoid));
        $this->assertEquals($result['haversine']['ft'], $this->distance->in('ft')->haversine(), '', 0.00001);
    }

    /**
     * @dataProvider ellipsoidInstanceAndExpectedResultProvider
     */
    public function testVincentyDistance($ellipsoid, $result)
    {
        $this->distance->setFrom($this->getMockCoordinateReturns($this->coordA, $ellipsoid));
        $this->distance->setTo($this->getMockCoordinateReturns($this->coordB, $ellipsoid));
        $this->assertEquals($result['vincenty']['m'], $this->distance->vincenty(), '', 0.00001);

        $this->distance->setFrom($this->getMockCoordinateReturns($this->coordA, $ellipsoid));
        $this->distance->setTo($this->getMockCoordinateReturns($this->coordB, $ellipsoid));
        $this->assertEquals($result['vincenty']['km'], $this->distance->in('km')->vincenty(), '', 0.00001);

        $this->distance->setFrom($this->getMockCoordinateReturns($this->coordA, $ellipsoid));
        $this->distance->setTo($this->getMockCoordinateReturns($this->coordB, $ellipsoid));
        $this->assertEquals($result['vincenty']['mi'], $this->distance->in('mi')->vincenty(), '', 0.00001);

        $this->distance->setFrom($this->getMockCoordinateReturns($this->coordA, $ellipsoid));
        $this->distance->setTo($this->getMockCoordinateReturns($this->coordB, $ellipsoid));
        $this->assertEquals($result['vincenty']['ft'], $this->distance->in('ft')->vincenty(), '', 0.00001);
    }

    public function ellipsoidInstanceAndExpectedResultProvider()
    {
        return array(
            array(
                Ellipsoid::createFromName(Ellipsoid::WGS84),
                array(
                    'flat' => array(
                        'm'  => 659166.50038742,
                        'km' => 659.16650524477,
                        'mi' => 409.58707724686,
                        'ft' => 2162619.7519272,
                    ),
                    'greatCircle' => array(
                        'm'  => 659021.90812846,
                        'km' => 659.02190812846,
                        'mi' => 409.49722876431,
                        'ft' => 2162145.3678755,
                    ),
                    'haversine' => array(
                        'm'  => 659021.90812846,
                        'km' => 659.02190812846,
                        'mi' => 409.49722876431,
                        'ft' => 2162145.3678755,
                    ),
                    'vincenty' => array(
                        'm'  => 658307.48497307,
                        'km' => 658.30748497307,
                        'mi' => 409.05330679648,
                        'ft' => 2159801.4598854,
                    ),
                ),
            ),
            array(
                Ellipsoid::createFromName(Ellipsoid::GRS_1980),
                array(
                    'flat' => array(
                        'm'  => 659166.60373525,
                        'km' => 659.16660373525,
                        'mi' => 409.587138446,
                        'ft' => 2162620.0909949,
                    ),
                    'greatCircle' => array(
                        'm'  => 659022.01145362,
                        'km' => 659.02201145362,
                        'mi' => 409.49729296758,
                        'ft' => 2162145.7068688,
                    ),
                    'haversine' => array(
                        'm'  => 659022.01145362,
                        'km' => 659.02201145362,
                        'mi' => 409.49729296758,
                        'ft' => 2162145.7068688,
                    ),
                    'vincenty' => array(
                        'm'  => 658307.58818269,
                        'km' => 658.30758818269,
                        'mi' => 409.05337092796,
                        'ft' => 2159801.7984996,
                    ),
                ),
            ),
            array(
                Ellipsoid::createFromName(Ellipsoid::CLARKE_1880),
                array(
                    'flat' => array(
                        'm'  => 659178.19367738,
                        'km' => 659.17819367738,
                        'mi' => 409.59434010217,
                        'ft' => 2162658.1157394,
                    ),
                    'greatCircle' => array(
                        'm'  => 659033.59885343,
                        'km' => 659.03359885343,
                        'mi' => 409.50449304402,
                        'ft' => 2162183.7232724,
                    ),
                    'haversine' => array(
                        'm'  => 659033.59885343,
                        'km' => 659.03359885343,
                        'mi' => 409.50449304402,
                        'ft' => 2162183.7232724,
                    ),
                    'vincenty' => array(
                        'm'  => 658307.4119689,
                        'km' => 658.3074119689,
                        'mi' => 409.05326143379,
                        'ft' => 2159801.2203704,
                    ),
                ),
            ),
            array(
                Ellipsoid::createFromName(Ellipsoid::HOUGH),
                array(
                    'flat' => array(
                        'm'  => 659180.34899633,
                        'km' => 659.18034899633,
                        'mi' => 409.59567935527,
                        'ft' => 2162665.1869958,
                    ),
                    'greatCircle' => array(
                        'm'  => 659035.7536996,
                        'km' => 659.0357536996,
                        'mi' => 409.50583200335,
                        'ft' => 2162190.7929777,
                    ),
                    'haversine' => array(
                        'm'  => 659035.7536996,
                        'km' => 659.0357536996,
                        'mi' => 409.50583200335,
                        'ft' => 2162190.7929777,
                    ),
                    'vincenty' => array(
                        'm'  => 658318.26962941,
                        'km' => 658.31826962941,
                        'mi' => 409.06000807124,
                        'ft' => 2159836.8426162,
                    ),
                ),
            ),
        );
    }

    public function testVincentyDistanceCoIncidentPoint()
    {
        $ellipsoid = Ellipsoid::createFromName(Ellipsoid::WGS84);
        $this->distance->setFrom($this->getMockCoordinateReturns($this->coordA, $ellipsoid));
        $this->distance->setTo($this->getMockCoordinateReturns($this->coordA, $ellipsoid));

        $this->assertSame(0.0, $this->distance->vincenty());
    }

    public function testFlatDistanceWithSameCoordinate()
    {
        $ellipsoid = Ellipsoid::createFromName(Ellipsoid::WGS84);
        $this->distance->setFrom($this->getMockCoordinateReturns($this->coordA, $ellipsoid));
        $this->distance->setTo($this->getMockCoordinateReturns($this->coordA, $ellipsoid));

        $this->assertSame(0.0, $this->distance->flat());
    }

    public function testGreatCircleDistanceWithSameCoordinate()
    {
        $ellipsoid = Ellipsoid::createFromName(Ellipsoid::WGS84);
        $this->distance->setFrom($this->getMockCoordinateReturns($this->coordA, $ellipsoid));
        $this->distance->setTo($this->getMockCoordinateReturns($this->coordA, $ellipsoid));

        $this->assertSame(0.0, $this->distance->greatCircle());
    }

    public function testHaversineDistanceWithSameCoordinate()
    {
        $ellipsoid = Ellipsoid::createFromName(Ellipsoid::WGS84);
        $this->distance->setFrom($this->getMockCoordinateReturns($this->coordA, $ellipsoid));
        $this->distance->setTo($this->getMockCoordinateReturns($this->coordA, $ellipsoid));

        $this->assertSame(0.0, $this->distance->haversine());
    }
}

class TestableDistance extends Distance
{
    public function getIn()
    {
        return $this->unit;
    }
}
