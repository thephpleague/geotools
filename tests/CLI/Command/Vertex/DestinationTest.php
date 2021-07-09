<?php

/*
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools\Tests\CLI\Command\Vertex;

use League\Geotools\CLI\Application;
use League\Geotools\CLI\Command\Vertex\Destination;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
class DestinationTest extends \League\Geotools\Tests\TestCase
{
    protected $application;
    protected $command;
    protected $commandTester;

    protected function setup(): void
    {
        $this->application = new Application;
        $this->application->add(new Destination);

        $this->command = $this->application->find('vertex:destination');

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
            'command'  => $this->command->getName(),
            'origin'   => 'foo, bar',
            'bearing'  => ' ',
            'distance' => '',
        ));
    }

    public function testExecute()
    {
        $this->commandTester->execute(array(
            'command'  => $this->command->getName(),
            'origin'   => '48.8234055, 2.3072664',
            'bearing'  => 180,
            'distance' => 200000,
        ));

        $this->assertEquals(0, $this->commandTester->getStatusCode());
    }

    public function testExecuteWithEmptyEllipsoidOption()
    {
        $this->expectException(\League\Geotools\Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage('Please provide an ellipsoid name !');
        $this->commandTester->execute(array(
            'command'     => $this->command->getName(),
            'origin'      => '48.8234055, 2.3072664',
            'bearing'     => 180,
            'distance'    => 200000,
            '--ellipsoid' => ' ',
        ));
    }

    public function testExecuteWithoutAvailableEllipsoidOption()
    {
        $this->expectException(\League\Geotools\Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage('foo ellipsoid does not exist in selected reference ellipsoids !');
        $this->commandTester->execute(array(
            'command'     => $this->command->getName(),
            'origin'      => '48.8234055, 2.3072664',
            'bearing'     => 180,
            'distance'    => 200000,
            '--ellipsoid' => 'foo',
        ));
    }

    public function testExecuteWithEllipsoid_GRS_1980()
    {
        $this->commandTester->execute(array(
            'command'     => $this->command->getName(),
            'origin'      => '48.8234055, 2.3072664',
            'bearing'     => 110,
            'distance'    => 2000000,
            '--ellipsoid' => 'GRS_1980',
        ));

        $this->assertEquals(0, $this->commandTester->getStatusCode());
    }

    public function testExecuteWithEllipsoid_AUSTRALIAN_NATIONAL()
    {
        $this->commandTester->execute(array(
            'command'     => $this->command->getName(),
            'origin'      => '48.8234055, 2.3072664',
            'bearing'     => 110,
            'distance'    => 2000000,
            '--ellipsoid' => 'AUSTRALIAN_NATIONAL',
        ));

        $this->assertEquals(0, $this->commandTester->getStatusCode());
    }

    public function testExecuteWithEllipsoid_BESSEL_1841()
    {
        $this->commandTester->execute(array(
            'command'     => $this->command->getName(),
            'origin'      => '48.8234055, 2.3072664',
            'bearing'     => 110,
            'distance'    => 2000000,
            '--ellipsoid' => 'BESSEL_1841',
        ));

        $this->assertEquals(0, $this->commandTester->getStatusCode());
    }
}
