<?php

/**
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Geotools\Tests\CLI\Geocoder;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Geotools\Tests\TestCase;
use Geotools\CLI\Geocoder\Reverse;

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
     * @expectedException Geotools\Exception\InvalidArgumentException
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
        $this->assertRegExp('/Avenue Gambetta 10, 75020 Paris/', $this->commandTester->getDisplay());
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
        $this->assertRegExp('/Avenue Gambetta 10, 75020 Paris/', $this->commandTester->getDisplay());
    }

    public function testExecuteReverseAgainstOpenStreetMapsProviderWithDefaultAdapterAndDefaultFormatter()
    {
        $this->commandTester->execute(array(
            'command'    => $this->command->getName(),
            'coordinate' => '48.8631507, 2.388911',
            '--provider' => 'openstreetmaps',
        ));

        $this->assertTrue(is_string($this->commandTester->getDisplay()));
        $this->assertRegExp('/Avenue Gambetta 8, 75011 Paris/', $this->commandTester->getDisplay());
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
        $this->assertRegExp('/Paris, Île-De-France, France Métropolitaine/', $this->commandTester->getDisplay());
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
Latitude:      40.6891988
Longitude:     -74.0445167
Bounds
 - South: 40.6887596
 - West:  -74.0451881
 - North: 40.6896417
 - East:  -74.0439787
Street Number: 
Street Name:   
Zipcode:       11231
City:          New York
City District: Brooklyn
County:        New York
County Code:   NEW YORK
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
{"latitude":40.6891988,"longitude":-74.0445167,"bounds":{"south":40.6887596,"west":-74.0451881,"north":40.6896417,"east":-74.0439787},"streetNumber":null,"streetName":null,"zipcode":"11231","city":"New York","cityDistrict":"Brooklyn","county":"New York","countyCode":"NEW YORK","region":"New York","regionCode":"NY","country":"United States","countryCode":"US","timezone":null}

EOF;

        $this->assertTrue(is_string($this->commandTester->getDisplay()));
        $this->assertSame($expected, $this->commandTester->getDisplay());
    }
}
