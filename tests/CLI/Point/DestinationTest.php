<?php

/**
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools\Tests\CLI\Point;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use League\Geotools\Tests\TestCase;
use League\Geotools\CLI\Point\Destination;

/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
class DestinationTest extends TestCase
{
    protected $application;
    protected $command;
    protected $commandTester;

    protected function setUp()
    {
        $this->application = new Application();
        $this->application->add(new Destination());

        $this->command = $this->application->find('point:destination');

        $this->commandTester = new CommandTester($this->command);
    }

    /**
     * @expectedException \RuntimeException
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

        $this->assertTrue(is_string($this->commandTester->getDisplay()));
        $this->assertRegExp('/47\.026774650075, 2\.3072664/', $this->commandTester->getDisplay());
    }

    /**
     * @expectedException League\Geotools\Exception\InvalidArgumentException
     * @expectedExceptionMessage Please provide an ellipsoid name !
     */
    public function testExecuteWithEmptyEllipsoidOption()
    {
        $this->commandTester->execute(array(
            'command'     => $this->command->getName(),
            'origin'      => '48.8234055, 2.3072664',
            'bearing'     => 180,
            'distance'    => 200000,
            '--ellipsoid' => ' ',
        ));
    }

    /**
     * @expectedException League\Geotools\Exception\InvalidArgumentException
     * @expectedExceptionMessage foo ellipsoid does not exist in selected reference ellipsoids !
     */
    public function testExecuteWithoutAvailableEllipsoidOption()
    {
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

        $this->assertTrue(is_string($this->commandTester->getDisplay()));
        $this->assertRegExp('/40\.279971519453, 24\.637336894406/', $this->commandTester->getDisplay());
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

        $this->assertTrue(is_string($this->commandTester->getDisplay()));
        $this->assertRegExp('/40\.280009426711, 24\.637268024987/', $this->commandTester->getDisplay());
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

        $this->assertTrue(is_string($this->commandTester->getDisplay()));
        $this->assertRegExp('/40\.278751982466, 24\.639552452771/', $this->commandTester->getDisplay());
    }
}
