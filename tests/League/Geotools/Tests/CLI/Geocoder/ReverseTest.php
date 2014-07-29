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
use League\Geotools\CLI\Geocoder\Reverse;

/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
class ReverseTest extends TestCase
{
    protected $application;
    protected $command;
    protected $commandTester;

    protected function setUp()
    {
        $this->application = new Application();
        $this->application->add(new Reverse());

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
Adapter:       \Geocoder\HttpAdapter\CurlHttpAdapter
Provider:      \Geocoder\Provider\GoogleMapsProvider
Arguments:     us_US
---
Latitude:      40.689758
Longitude:     -74.045138
Bounds
 - South: 40.689758
 - West:  -74.045138
 - North: 40.689758
 - East:  -74.045138
Street Number: 1
Street Name:   Liberty Island
Zipcode:       10004
City:          New York
City District: Manhattan
County:        New York County
County Code:   NEW YORK COUNTY
Region:        New York
Region Code:   NY
Country:       United States
Country Code:  US
Timezone:      

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
{"latitude":40.689758,"longitude":-74.045138,"bounds":{"south":40.689758,"west":-74.045138,"north":40.689758,"east":-74.045138},"streetNumber":"1","streetName":"Liberty Island","zipcode":"10004","city":"New York","cityDistrict":"Manhattan","county":"New York County","countyCode":"NEW YORK COUNTY","region":"New York","regionCode":"NY","country":"United States","countryCode":"US","timezone":null}

EOF;

        $this->assertTrue(is_string($this->commandTester->getDisplay()));
        $this->assertSame($expected, $this->commandTester->getDisplay());
    }
}
