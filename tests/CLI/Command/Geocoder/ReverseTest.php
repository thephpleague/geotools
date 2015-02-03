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

use League\Geotools\CLI\Application;
use League\Geotools\CLI\Command\Geocoder\Reverse;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
class ReverseTest extends \League\Geotools\Tests\TestCase
{
    protected $application;
    protected $command;
    protected $commandTester;

    protected function setUp()
    {
        $this->application = new Application;
        $this->application->add(new Reverse);

        $this->command = $this->application->find('geocoder:reverse');

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

    /**
     * @expectedException League\Geotools\Exception\InvalidArgumentException
     * @expectedExceptionMessage It should be a valid and acceptable ways to write geographic coordinates !
     */
    public function testExecuteInvalidArguments()
    {
        $this->commandTester->execute(array(
            'command'    => $this->command->getName(),
            'coordinate' => 'foo, bar',
        ));
    }

    public function testExecuteReverseWithDefaultProviderAndAdapterAndFormatter()
    {
        $this->commandTester->execute(array(
            'command'    => $this->command->getName(),
            'coordinate' => '48.8631507, 2.388911',
        ));

        $this->assertTrue(is_string($this->commandTester->getDisplay()));
        $this->assertRegExp('/Avenue Gambetta 1, 75020 Paris/', $this->commandTester->getDisplay());
    }

    public function testExecuteReverseWithDefaultProviderAndSocketAdapterAndArguments()
    {
        $this->commandTester->execute(array(
            'command'    => $this->command->getName(),
            'coordinate' => '48.8631507, 2.388911',
            '--adapter'  => 'socket',
            '--args'     => array(
                'da_DK',    // locale
                'Denmark',  // region
                'true',     // useSsl
            ),
        ));

        $this->assertTrue(is_string($this->commandTester->getDisplay()));
        $this->assertRegExp('/Avenue Gambetta 1, 75020 Paris/', $this->commandTester->getDisplay());
    }

    public function testExecuteReverseAgainstOpenStreetMapsProviderWithDefaultAdapterAndDefaultFormatter()
    {
        $this->commandTester->execute(array(
            'command'    => $this->command->getName(),
            'coordinate' => '48.8631507, 2.388911',
            '--provider' => 'openstreetmaps',
        ));

        $this->assertTrue(is_string($this->commandTester->getDisplay()));
        $this->assertRegExp('/Avenue Gambetta 8, 75020 Paris/', $this->commandTester->getDisplay());
    }

    public function testExecuteReverseAgainstOpenStreetMapsProviderWithDefaultAdapterAndFormatter()
    {
        $this->commandTester->execute(array(
            'command'    => $this->command->getName(),
            'coordinate' => '48.8631507, 2.388911',
            '--provider' => 'openstreetmaps',
            '--format'   => '%L, %R, %C',
        ));

        $this->assertTrue(is_string($this->commandTester->getDisplay()));
        $this->assertRegExp('/Paris, Île-De-France/', $this->commandTester->getDisplay());
    }

    public function testExecuteRawOptionAndLocalArgument()
    {
        $this->commandTester->execute(array(
            'command'    => $this->command->getName(),
            'coordinate' => '40.689167, -74.044444',
            '--raw'      => true,
            '--args'     => 'us_US',
        ));

        $expected = <<<EOF
<label>Adapter</label>:       <value>\Geocoder\HttpAdapter\CurlHttpAdapter</value>
<label>Provider</label>:      <value>\Geocoder\Provider\GoogleMapsProvider</value>
<label>Arguments</label>:     <value>us_US</value>
---
<label>Latitude</label>:      <value>40.689758</value>
<label>Longitude</label>:     <value>-74.045138</value>
<label>Bounds</label>
 - <label>South</label>: <value>40.689758</value>
 - <label>West</label>:  <value>-74.045138</value>
 - <label>North</label>: <value>40.689758</value>
 - <label>East</label>:  <value>-74.045138</value>
<label>Street Number</label>: <value>1</value>
<label>Street Name</label>:   <value>Liberty Island</value>
<label>Zipcode</label>:       <value>10004</value>
<label>City</label>:          <value>New York</value>
<label>City District</label>: <value>Manhattan</value>
<label>County</label>:        <value>New York County</value>
<label>County Code</label>:   <value>NEW YORK COUNTY</value>
<label>Region</label>:        <value>New York</value>
<label>Region Code</label>:   <value>NY</value>
<label>Country</label>:       <value>United States</value>
<label>Country Code</label>:  <value>US</value>
<label>Timezone</label>:      <value></value>

EOF;

        $this->assertTrue(is_string($this->commandTester->getDisplay()));
        $this->assertSame($expected, $this->commandTester->getDisplay());
    }

    public function testExecuteJsonOption()
    {
        $this->commandTester->execute(array(
            'command'    => $this->command->getName(),
            'coordinate' => '40.689167, -74.044444',
            '--json'     => true,
        ));

        $expected = <<<EOF
<value>{"latitude":40.689758,"longitude":-74.045138,"bounds":{"south":40.689758,"west":-74.045138,"north":40.689758,"east":-74.045138},"streetNumber":"1","streetName":"Liberty Island","zipcode":"10004","city":"New York","cityDistrict":"Manhattan","county":"New York County","countyCode":"NEW YORK COUNTY","region":"New York","regionCode":"NY","country":"United States","countryCode":"US","timezone":null}</value>

EOF;

        $this->assertTrue(is_string($this->commandTester->getDisplay()));
        $this->assertSame($expected, $this->commandTester->getDisplay());
    }
}
