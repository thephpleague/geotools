<?php

/*
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools\Tests\CLI\Command\Geocoder;

use League\Geotools\CLI\Command\Geocoder\Geocode;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
class GeocodeTest extends \League\Geotools\Tests\TestCase
{
    protected $application;
    protected $command;
    protected $commandTester;

    protected function setUp()
    {
        $this->application = new Application;
        $this->application->add(new Geocode);

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
        $this->assertRegExp('/33\.035, -96\.814/', $this->commandTester->getDisplay());
    }

    public function testExecuteIPv6AgainstFreeGeoIpProviderWithDefaultAdapter()
    {
        $this->commandTester->execute(array(
            'command'    => $this->command->getName(),
            'value'      => '::ffff:74.200.247.59',
            '--provider' => 'free_geo_ip',
        ));

        $this->assertTrue(is_string($this->commandTester->getDisplay()));
        $this->assertRegExp('/33\.035, -96\.814/', $this->commandTester->getDisplay());
    }

    public function testExecuteStreetAddressWithDefaultDumper()
    {
        $this->commandTester->execute(array(
            'command'  => $this->command->getName(),
            'value'    => 'Copenhagen, Denmark',
            '--dumper' => ' foo ',
        ));

        $this->assertTrue(is_string($this->commandTester->getDisplay()));
        $this->assertSame('<value>POINT(12.568337 55.676097)</value>', trim($this->commandTester->getDisplay()));
    }

    public function testExecuteStreetAddressWithKmlDumper()
    {
        $this->commandTester->execute(array(
            'command'  => $this->command->getName(),
            'value'    => 'Copenhagen, Denmark',
            '--dumper' => 'KML',
        ));

        $expected = <<<KML
<value><?xml version="1.0" encoding="UTF-8"?>
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
</kml></value>
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
<label>Adapter</label>:       <value>\Geocoder\HttpAdapter\SocketHttpAdapter</value>
<label>Provider</label>:      <value>\Geocoder\Provider\GoogleMapsProvider</value>
<label>Arguments</label>:     <value>da_DK</value>
---
<label>Latitude</label>:      <value>55.6760968</value>
<label>Longitude</label>:     <value>12.5683371</value>
<label>Bounds</label>
 - <label>South</label>: <value>55.615441</value>
 - <label>West</label>:  <value>12.4533824</value>
 - <label>North</label>: <value>55.7270937</value>
 - <label>East</label>:  <value>12.7342654</value>
<label>Street Number</label>: <value></value>
<label>Street Name</label>:   <value></value>
<label>Zipcode</label>:       <value></value>
<label>City</label>:          <value>København</value>
<label>City District</label>: <value></value>
<label>County</label>:        <value>København</value>
<label>County Code</label>:   <value>KØBENHAVN</value>
<label>Region</label>:        <value>Hovedstaden</value>
<label>Region Code</label>:   <value>HOVEDSTADEN</value>
<label>Country</label>:       <value>Danmark</value>
<label>Country Code</label>:  <value>DK</value>
<label>Timezone</label>:      <value></value>

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
<value>{"latitude":55.6760968,"longitude":12.5683371,"bounds":{"south":55.615441,"west":12.4533824,"north":55.7270937,"east":12.7342654},"streetNumber":null,"streetName":null,"zipcode":null,"city":"Copenhagen","cityDistrict":null,"county":"K\u00f8benhavn","countyCode":"K\u00d8BENHAVN","region":"Capital Region Of Denmark","regionCode":"CAPITAL REGION OF DENMARK","country":"Denmark","countryCode":"DK","timezone":null}</value>

EOF;

        $this->assertTrue(is_string($this->commandTester->getDisplay()));
        $this->assertSame($expected, $this->commandTester->getDisplay());
    }
}
