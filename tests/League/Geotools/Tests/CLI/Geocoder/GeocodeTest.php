<?php

/**
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools\Tests\CLI\Geocoder;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use League\Geotools\Tests\TestCase;
use League\Geotools\CLI\Geocoder\Geocode;

/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
class GeocodeTest extends TestCase
{
    protected $application;
    protected $command;
    protected $commandTester;

    protected function setUp()
    {
        $this->application = new Application();
        $this->application->add(new Geocode());

        $this->command = $this->application->find('geocoder:geocode');

        $this->commandTester = new CommandTester($this->command);
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Not enough arguments.
     */
    public function testExecuteWithoutArguments()
    {
        $this->commandTester->execute(array(
            'command' => $this->command->getName(),
        ));
    }

    public function testExecuteStreetAddressWithDefaultProviderAndAdapter()
    {
        $this->commandTester->execute(array(
            'command' => $this->command->getName(),
            'value'   => 'Copenhagen, Denmark',
        ));

        $this->assertTrue(is_string($this->commandTester->getDisplay()));
        $this->assertRegExp('/55\.6760968, 12\.5683371/', $this->commandTester->getDisplay());
    }

    public function testExecuteStreetAddressWithDefaultProviderAndAdapterAndArguments()
    {
        $this->commandTester->execute(array(
            'command' => $this->command->getName(),
            'value'   => 'Copenhagen, Denmark',
            '--args'  => array(
                'da_DK',    // locale
                'Denmark',  // region
                'true',     // useSsl
            ),
        ));

        $this->assertTrue(is_string($this->commandTester->getDisplay()));
        $this->assertRegExp('/55\.6760968, 12\.5683371/', $this->commandTester->getDisplay());
    }

    public function testExecuteIPv4AgainstFreeGeoIpProviderWithBuzzAdapter()
    {
        $this->commandTester->execute(array(
            'command'    => $this->command->getName(),
            'value'      => '74.200.247.59',
            '--provider' => 'free_geo_ip',
            '--adapter'  => 'buzz',
        ));

        $this->assertTrue(is_string($this->commandTester->getDisplay()));
        $this->assertRegExp('/33\.0347, -96\.8134/', $this->commandTester->getDisplay());
    }

    public function testExecuteIPv6AgainstFreeGeoIpProviderWithDefaultAdapter()
    {
        $this->commandTester->execute(array(
            'command'    => $this->command->getName(),
            'value'      => '::ffff:74.200.247.59',
            '--provider' => 'free_geo_ip',
        ));

        $this->assertTrue(is_string($this->commandTester->getDisplay()));
        $this->assertRegExp('/33\.0347, -96\.8134/', $this->commandTester->getDisplay());
    }

    public function testExecuteStreetAddressWithDefaultDumper()
    {
        $this->commandTester->execute(array(
            'command'  => $this->command->getName(),
            'value'    => 'Copenhagen, Denmark',
            '--dumper' => ' foo ',
        ));

        $this->assertTrue(is_string($this->commandTester->getDisplay()));
        $this->assertSame('POINT(12.568337 55.676097)', trim($this->commandTester->getDisplay()));
    }

    public function testExecuteStreetAddressWithKmlDumper()
    {
        $this->commandTester->execute(array(
            'command'  => $this->command->getName(),
            'value'    => 'Copenhagen, Denmark',
            '--dumper' => 'KML',
        ));

        $expected = <<<KML
<?xml version="1.0" encoding="UTF-8"?>
<kml xmlns="http://www.opengis.net/kml/2.2">
    <Document>
        <Placemark>
            <name><![CDATA[Copenhagen, København, Capital Region Of Denmark, Denmark]]></name>
            <description><![CDATA[Copenhagen, København, Capital Region Of Denmark, Denmark]]></description>
            <Point>
                <coordinates>12.5683371,55.6760968,0</coordinates>
            </Point>
        </Placemark>
    </Document>
</kml>
KML;

        $this->assertTrue(is_string($this->commandTester->getDisplay()));
        $this->assertSame($expected, trim($this->commandTester->getDisplay()));
    }

    public function testExecuteRawOptionAndLocalArgumentAndSocketAdapter()
    {
        $this->commandTester->execute(array(
            'command'   => $this->command->getName(),
            'value'     => 'Copenhagen, Denmark',
            '--raw'     => true,
            '--args'    => 'da_DK',
            '--adapter' => 'socket',
        ));

        $expected = <<<EOF
Adapter:       \Geocoder\HttpAdapter\SocketHttpAdapter
Provider:      \Geocoder\Provider\GoogleMapsProvider
Arguments:     da_DK
---
Latitude:      55.6760968
Longitude:     12.5683371
Bounds
 - South: 55.615441
 - West:  12.4533824
 - North: 55.7270937
 - East:  12.7342654
Street Number: 
Street Name:   
Zipcode:       
City:          København
City District: 
County:        København
County Code:   KØBENHAVN
Region:        Hovedstaden
Region Code:   HOVEDSTADEN
Country:       Danmark
Country Code:  DK
Timezone:      

EOF;

        $this->assertTrue(is_string($this->commandTester->getDisplay()));
        $this->assertSame($expected, $this->commandTester->getDisplay());
    }

    public function testExecuteJsonOption()
    {
        $this->commandTester->execute(array(
            'command' => $this->command->getName(),
            'value'   => 'Copenhagen, Denmark',
            '--json'  => true,
        ));

        $expected = <<<EOF
{"latitude":55.6760968,"longitude":12.5683371,"bounds":{"south":55.615441,"west":12.4533824,"north":55.7270937,"east":12.7342654},"streetNumber":null,"streetName":null,"zipcode":null,"city":"Copenhagen","cityDistrict":null,"county":"K\u00f8benhavn","countyCode":"K\u00d8BENHAVN","region":"Capital Region Of Denmark","regionCode":"CAPITAL REGION OF DENMARK","country":"Denmark","countryCode":"DK","timezone":null}

EOF;

        $this->assertTrue(is_string($this->commandTester->getDisplay()));
        $this->assertSame($expected, $this->commandTester->getDisplay());
    }
}
