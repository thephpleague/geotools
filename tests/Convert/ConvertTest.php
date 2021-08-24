<?php

/*
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools\Tests\Convert;

use League\Geotools\Convert\Convert;
use League\Geotools\Coordinate\Coordinate;

/**
* @author Antoine Corcy <contact@sbin.dk>
*/
class ConvertTest extends \League\Geotools\Tests\TestCase
{
    /**
     * @doesNotPerformAssertions
     */
    public function testConstructorShouldAcceptCoordinateInterface()
    {
        new TestableConvert($this->getStubCoordinate());
    }

    public function testConstructorShouldSetCoordinateInterface()
    {
        $convert = new TestableConvert($this->getStubCoordinate());
        $coordinates = $convert->getCoordinates();

        $this->assertTrue(is_object($coordinates));
        $this->assertInstanceOf('League\Geotools\Coordinate\CoordinateInterface', $coordinates);
    }

    /**
     * @dataProvider coordinatesToDMSProvider
     */
    public function testToDegreesMinutesSeconds($coordinates, $format, $expectedResult)
    {
        $convert = new TestableConvert(new Coordinate($coordinates));
        $converted = $convert->toDegreesMinutesSeconds($format);

        $this->assertTrue(is_string($converted));
        $this->assertSame($expectedResult, $converted);
    }

    /**
     * @dataProvider coordinatesToDMSProvider
     */
    public function testToDMS($coordinates, $format, $expectedResult)
    {
        $convert = new TestableConvert(new Coordinate($coordinates));
        $converted = $convert->toDMS($format);

        $this->assertTrue(is_string($converted));
        $this->assertSame($expectedResult, $converted);
    }

    public function coordinatesToDMSProvider()
    {
        return array(
            array(
                '40.446195, -79.948862',
                '%D°%M′%S″%L %d°%m′%s″%l',
                '40°26′46″N 79°56′56″W'
            ),
            array(
                '40.446195N 79.948862W',
                '%D %M %S%L %d %m %s%l',
                '40 26 46N 79 56 56W'
            ),
            array(
                '40° 26.7717, -79° 56.93172',
                '%P%Dd%M\'%S" %p%dd%m\'%s"',
                '40d26\'46" -79d56\'56"'
            ),
            array(
                '40d 26′ 47″ N 079d 58′ 36″ W',
                '%P%D°%M′%S″ %p%d°%m′%s″',
                '40°26′47″ -79°58′36″'
            ),
            array(
                '48.8234055, 2.3072664',
                '%D°%M′%S″%L, %d°%m′%s″%l',
                '48°49′24″N, 2°18′26″E'
            ),
            array(
                '48.8234055, 2.3072664',
                '<p><span>%D°%M′%S″%L</span>, %d°%m′%s″%l</p>',
                '<p><span>48°49′24″N</span>, 2°18′26″E</p>'
            ),
        );
    }

    /**
     * @dataProvider coordinatesToDMProvider
     */
    public function testToDecimalMinutes($coordinates, $format, $expectedResult)
    {
        $convert = new TestableConvert(new Coordinate($coordinates));
        $converted = $convert->toDecimalMinutes($format);

        $this->assertTrue(is_string($converted));
        $this->assertSame($expectedResult, $converted);
    }

    /**
     * @dataProvider coordinatesToDMProvider
     */
    public function testToDM($coordinates, $format, $expectedResult)
    {
        $convert = new TestableConvert(new Coordinate($coordinates));
        $converted = $convert->toDM($format);

        $this->assertTrue(is_string($converted));
        $this->assertSame($expectedResult, $converted);
    }

    public function coordinatesToDMProvider()
    {
        return array(
            array(
                '40.446195, -79.948862',
                '%D°%N″%L %d°%n%l',
                '40°26.7717″N 79°56.93172W'
            ),
            array(
                '40.446388888889, -79.976666666667',
                '%P%D° %N%L %p%d° %n%l',
                '40° 26.78333N -79° 58.6W'
            ),
            array(
                '40.446195S 79.948862E',
                '%P%D %N%L, %p%d %n%l',
                '-40 26.7717S, 79 56.93172E'
            ),
            array(
                '40° 26.7717, -79° 56.93172',
                '%L%Dd %N″,%l%dd %n″',
                'N40d 26.7717″,W79d 56.93172″'
            ),
            array(
                '40d 26′ 47″ N 079d 58′ 36″ W',
                '%P%D %N%L, %p%d %n%l',
                '40 26.78333N, -79 58.6W'
            ),
            array(
                '48°49′24″N, 2°18′26″E',
                '%P%D %N%L, %p%d %n%l',
                '48 49.4N, 2 18.43333E'
            ),
            array(
                '48°49′24″N, 2°18′26″E',
                '<p><strong>%P%D %N%L</strong>, %p%d %n%l</p>',
                '<p><strong>48 49.4N</strong>, 2 18.43333E</p>'
            ),
        );
    }


    /**
     * @dataProvider coordinatesToDegreeDecimalMinutesProvider
     */
    public function testToDegreeDecimalMinutes($coordinates, $expectedResult)
    {
        $convert = new TestableConvert(new Coordinate($coordinates));
        $converted = $convert->toDegreeDecimalMinutes();

        $this->assertTrue(is_string($converted));
        $this->assertSame($expectedResult, $converted);
    }

    public function coordinatesToDegreeDecimalMinutesProvider()
    {
        return array(
            array(
                '48 2',
                'N 48° 0.000 E 2° 0.000'
            ),
            array(
                'N 48 49.2 E 2 8.26',
                'N 48° 49.200 E 2° 8.260'
            ),
            array(
                'N 48 49.27444 E 2 8.255555',
                'N 48° 49.274 E 2° 8.256'
            ),
            array(
                'N 48° 49.2 E 2° 8.26',
                'N 48° 49.200 E 2° 8.260'
            ),
        );
    }

    /**
     * @dataProvider coordinatesToUTMProvider
     */
    public function testToUniversalTransverseMercator($coordinates, $expectedResult)
    {
        $convert = new TestableConvert(new Coordinate($coordinates));
        $converted = $convert->toUniversalTransverseMercator();

        $this->assertTrue(is_string($converted));
        $this->assertSame($expectedResult, $converted);
    }

    /**
     * @dataProvider coordinatesToUTMProvider
     */
    public function testToUTM($coordinates, $expectedResult)
    {
        $convert = new TestableConvert(new Coordinate($coordinates));
        $converted = $convert->toUTM();

        $this->assertTrue(is_string($converted));
        $this->assertSame($expectedResult, $converted);
    }

    public function coordinatesToUTMProvider()
    {
        return array(
            array(
                '-40.446195 -79.948862',
                '17G 589138 5522187'
            ),
            array(
                '40.446195 79.948862',
                '44T 410861 4477812'
            ),
            array(
                '48°49′24″N, 2°18′26″E',
                '31U 449149 5408047'
            ),
            array(
                '40d 26′ 47″ N 079d 58′ 36″ W',
                '17T 586780 4477806'
            ),
            array(
                '80, 180',
                '61X 441867 8883083'
            ),
            array(
                '84, 184',
                '1X 511669 9328193'
            ),
            array(
                '84, -176',
                '1X 511669 9328193'
            ),
            array(
                '0, 0',
                '31N 166021 0'
            ),
            // Special zone for South Norway. Ex: Bergen.
            array(
                '60.3912628, 5.3220544',
                '32V 297351 6700643'
            ),
            array(
                '60.3912628, -5.3220544',
                '30V 372031 6697240'
            ),
            // Special zone for Svalbard. 4 cases.
            array(
                '72.0, 0',
                '31X 396566 7991507'
            ),
            array(
                '72.0, 9.0',
                '33X 293363 7999232'
            ),
            array(
                '72.0, 21.0',
                '35X 293363 7999232'
            ),
            array(
                '72.0, 41.999999',
                '37X 603433 7991507'
            ),
        );
    }
}

class TestableConvert extends Convert
{
    public function getCoordinates()
    {
        return $this->coordinates;
    }
}
