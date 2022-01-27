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

use League\Geotools\CLI\GeotoolsApplication;
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

    protected function setup(): void
    {
        $this->application = new GeotoolsApplication();
        $this->application->add(new Reverse);

        $this->command = $this->application->find('geocoder:reverse');

        $this->commandTester = new CommandTester($this->command);
    }

    public function testExecuteWithoutArguments()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Not enough arguments');
        $this->commandTester->execute(array(
            'command' => $this->command->getName(),
        ));
    }

    public function testExecuteInvalidArguments()
    {
        $this->expectException(\League\Geotools\Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage('It should be a valid and acceptable ways to write geographic coordinates !');
        $this->commandTester->execute(array(
            'command'    => $this->command->getName(),
            'coordinate' => 'foo, bar',
        ));
    }

    public function testExecuteReverseWithDefaultProviderAndAdapterAndFormatter()
    {
        $this->markTestIncomplete();

        $this->commandTester->execute(array(
            'command'    => $this->command->getName(),
            'coordinate' => '48.8631507, 2.388911',
        ));

        $this->assertTrue(is_string($this->commandTester->getDisplay()));
        $this->assertMatchesRegularExpression('/Avenue Gambetta 1, 75020 Paris/', $this->commandTester->getDisplay());
    }

    public function testExecuteReverseWithDefaultProviderAndSocketAdapterAndArguments()
    {
        $this->markTestIncomplete();

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
        $this->assertMatchesRegularExpression('/Avenue Gambetta 1, 75020 Paris/', $this->commandTester->getDisplay());
    }

    public function testExecuteReverseAgainstOpenStreetMapsProviderWithDefaultAdapterAndDefaultFormatter()
    {
        $this->markTestIncomplete();

        $this->commandTester->execute(array(
            'command'    => $this->command->getName(),
            'coordinate' => '48.8631507, 2.388911',
            '--provider' => 'openstreetmaps',
        ));

        $this->assertTrue(is_string($this->commandTester->getDisplay()));
        $this->assertMatchesRegularExpression('/Avenue Gambetta 8, 75020 Paris/', $this->commandTester->getDisplay());
    }

    public function testExecuteReverseAgainstOpenStreetMapsProviderWithDefaultAdapterAndFormatter()
    {
        $this->markTestIncomplete();

        $this->commandTester->execute(array(
            'command'    => $this->command->getName(),
            'coordinate' => '48.8631507, 2.388911',
            '--provider' => 'openstreetmaps',
            '--format'   => '%L, %A1, %C',
        ));

        $this->assertTrue(is_string($this->commandTester->getDisplay()));
        $this->assertMatchesRegularExpression('/Paris, Île-de-France/', $this->commandTester->getDisplay());
    }

    public function testExecuteRawOptionAndLocalArgument()
    {
        $this->markTestIncomplete();

        $this->commandTester->execute(array(
            'command'    => $this->command->getName(),
            'coordinate' => '40.689167, -74.044444',
            '--raw'      => true,
            '--args'     => 'us_US',
        ));

        $expected = <<<EOF
<label>Adapter</label>:       <value>\Ivory\HttpAdapter\CurlHttpAdapter</value>
<label>Provider</label>:      <value>\Geocoder\Provider\GoogleMaps</value>
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
<label>Admin Levels</label>
 - <label>NY</label>: <value>New York</value>
 - <label>New York County</label>: <value>New York County</value>
<label>Country</label>:       <value>United States</value>
<label>Country Code</label>:  <value>US</value>
<label>Timezone</label>:      <value></value>

EOF;

        $this->assertTrue(is_string($this->commandTester->getDisplay()));
        $this->assertSame($expected, $this->commandTester->getDisplay());
    }

    public function testExecuteJsonOption()
    {
        $this->markTestIncomplete();

        $this->commandTester->execute(array(
            'command'    => $this->command->getName(),
            'coordinate' => '40.689167, -74.044444',
            '--json'     => true,
        ));

        $expected = <<<EOF
<value>{"latitude":40.689758,"longitude":-74.045138,"bounds":{"south":40.689758,"west":-74.045138,"north":40.689758,"east":-74.045138},"streetNumber":"1","streetName":"Liberty Island","postalCode":"10004","locality":"New York","subLocality":"Manhattan","adminLevels":{"1":{"name":"New York","code":"NY"},"2":{"name":"New York County","code":"New York County"}},"country":"United States","countryCode":"US","timezone":null}</value>

EOF;

        $this->assertTrue(is_string($this->commandTester->getDisplay()));
        $this->assertSame($expected, $this->commandTester->getDisplay());
    }
}
