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
use Geotools\CLI\Geocoder\Geocode;

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
        $this->assertRegExp('/55.6760968, 12.5683371/', $this->commandTester->getDisplay());
    }

    public function testExecuteStreetAddressWithDefaultProviderAndAdapterAndArguments()
    {
        $this->commandTester->execute(array(
            'command' => $this->command->getName(),
            'value'   => 'Copenhagen, Denmark',
            '--args'  => array(
                'da_DK',    // locale
                'Denmark',  // region
                'true'      // useSsl
            ),
        ));

        $this->assertTrue(is_string($this->commandTester->getDisplay()));
        $this->assertRegExp('/55.6760968, 12.5683371/', $this->commandTester->getDisplay());
    }

    public function testExecuteIPv4AgainsFreeGeoIpProviderWithSocketAdapter()
    {
        $this->commandTester->execute(array(
            'command'    => $this->command->getName(),
            'value'      => '74.200.247.59',
            '--provider' => 'free_geo_ip',
            '--adapter'  => 'socket',
        ));

        $this->assertTrue(is_string($this->commandTester->getDisplay()));
        $this->assertRegExp('/37.7484, -122.4156/', $this->commandTester->getDisplay());
    }

    public function testExecuteIPv6AgainsFreeGeoIpProviderWithDefaultAdapter()
    {
        $this->commandTester->execute(array(
            'command'    => $this->command->getName(),
            'value'      => '::ffff:74.200.247.59',
            '--provider' => 'free_geo_ip',
        ));

        $this->assertTrue(is_string($this->commandTester->getDisplay()));
        $this->assertRegExp('/37.7484, -122.4156/', $this->commandTester->getDisplay());
    }
}
