<?php

/**
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools\Tests\Coordinate;

use League\Geotools\Tests\TestCase;
use League\Geotools\Coordinate\Ellipsoid;

/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
class EllipsoidTest extends TestCase
{
    /**
     * @expectedException League\Geotools\Exception\InvalidArgumentException
     * @expectedExceptionMessage The inverse flattening cannot be negative or equal to zero !
     * @dataProvider constructorArgumentsWhichThrowException
     */
    public function testConstructWithInverseFlatteningEqualsToZero($invF)
    {
        new Ellipsoid('foo', 'bar', $invF);
    }

    public function constructorArgumentsWhichThrowException()
    {
        return array(
            array(-123),
            array(-0.1),
            array(0),
            array(0.0),
            array('foo'),
            array(' '),
            array(array()),
            array(null),
        );
    }

    /**
     * @dataProvider constructorArgumentsProvider
     */
    public function testConstructor($name, $a, $invF, $expected)
    {
        $ellipsoid = new Ellipsoid($name, $a, $invF);

        $this->assertSame($expected[0], $ellipsoid->getName());
        $this->assertSame($expected[1], $ellipsoid->getA());
        $this->assertSame($expected[2], $ellipsoid->getB());
        $this->assertSame($expected[3], $ellipsoid->getInvF());
        $this->assertSame($expected[4], $ellipsoid->getArithmeticMeanRadius());
    }

    public function constructorArgumentsProvider()
    {
        return array(
            array('name', 'a', 1, array('name', 0.0, 0.0, 1.0, 0.0)),
            array('foo', 'bar', 123, array('foo', 0.0, 0.0, 123.0, 0.0)),
            array(123, 456, 789, array(123, 456.0, 455.42205323194, 789.0, 455.80735107731)),
        );
    }

    /**
     * @expectedException League\Geotools\Exception\InvalidArgumentException
     * @expectedExceptionMessage foo ellipsoid does not exist in selected reference ellipsoids !
     */
    public function testCreateFromNameUnavailableEllipsoidThrowsException()
    {
        Ellipsoid::createFromName('foo');
    }

    /**
     * @expectedException League\Geotools\Exception\InvalidArgumentException
     * @expectedExceptionMessage Please provide an ellipsoid name !
     */
    public function testCreateFromNameEmptyNameThrowsException()
    {
        Ellipsoid::createFromName(' ');
    }

    public function testCreateFromName()
    {
        $ellipsoid = Ellipsoid::createFromName(Ellipsoid::WGS84);

        $this->assertTrue(is_object($ellipsoid));
        $this->assertInstanceOf('League\Geotools\Coordinate\Ellipsoid', $ellipsoid);
        $this->assertSame('WGS 84', $ellipsoid->getName());
        $this->assertSame(6378136.0, $ellipsoid->getA());
        $this->assertEquals(6356751.317598, $ellipsoid->getB(), '', 0.0001);
        $this->assertSame(298.257223563, $ellipsoid->getInvF());
        $this->assertEquals(6371007.7725327, $ellipsoid->getArithmeticMeanRadius(), '', 0.0001);
    }

    /**
     * @expectedException League\Geotools\Exception\InvalidArgumentException
     * @expectedExceptionMessage Ellipsoid arrays should contain `name`, `a` and `invF` keys !
     * @dataProvider createFromArrayProvider
     */
    public function testCreateFromArrayThrowsException($newEllipsoid)
    {
        Ellipsoid::createFromArray($newEllipsoid);
    }

    public function createFromArrayProvider()
    {
        return array(
            array(
                array()
            ),
            array(
                array(' ')
            ),
            array(
                array('foo')
            ),
            array(
                array(
                    'foo' => 'foo',
                    'bar' => 'bar',
                    'baz' => 'baz'
                )
            ),
            array(
                array(
                    'name' => 'name',
                    'a'    => 'a',
                    'foo'  => 'foo'
                )
            ),
        );
    }

    public function testCreateFromArray()
    {
        $newEllipsoid = array(
            'name' => 'foo ellipsoid',
            'a'    => 6378136.0,
            'invF' => 298.257223563,
        );

        $ellipsoid = Ellipsoid::createFromArray($newEllipsoid);

        $this->assertTrue(is_object($ellipsoid));
        $this->assertInstanceOf('League\Geotools\Coordinate\Ellipsoid', $ellipsoid);
        $this->assertSame('foo ellipsoid', $ellipsoid->getName());
        $this->assertSame(6378136.0, $ellipsoid->getA());
        $this->assertEquals(6356751.317598, $ellipsoid->getB(), '', 0.0001);
        $this->assertSame(298.257223563, $ellipsoid->getInvF());
        $this->assertEquals(6371007.7725327, $ellipsoid->getArithmeticMeanRadius(), '', 0.0001);
    }


    /**
     * @expectedException League\Geotools\Exception\NotMatchingEllipsoidException
     * @expectedExceptionMessage The ellipsoids for both coordinates must match !
     */
    public function testCoordinatesWithDifferentEllipsoids()
    {
        $WGS84       = Ellipsoid::createFromName(Ellipsoid::WGS84);
        $ANOTHER_ONE = Ellipsoid::createFromArray(array(
            'name' => 'foo ellipsoid',
            'a'    => 123.0,
            'invF' => 456.0
        ));

        $a = $this->getMockCoordinateReturns(array(1, 2), $WGS84);
        $b = $this->getMockCoordinateReturns(array(3, 4), $ANOTHER_ONE);

        Ellipsoid::checkCoordinatesEllipsoid($a, $b);
    }
}
