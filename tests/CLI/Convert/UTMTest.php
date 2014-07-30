<?php

/**
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools\Tests\CLI\Convert;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use League\Geotools\Tests\TestCase;
use League\Geotools\CLI\Convert\UTM;

/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
class UTMTest extends TestCase
{
    protected $application;
    protected $command;
    protected $commandTester;

    protected function setUp()
    {
        $this->application = new Application();
        $this->application->add(new UTM());

        $this->command = $this->application->find('convert:utm');

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
            'command'    => $this->command->getName(),
            'coordinate' => 'foo, bar',
        ));
    }

    public function testExecute()
    {
        $this->commandTester->execute(array(
            'command'    => $this->command->getName(),
            'coordinate' => '48.8234055, 2.3072664',
        ));

        $this->assertTrue(is_string($this->commandTester->getDisplay()));
        $this->assertRegExp('/31U 449152 5408055/', $this->commandTester->getDisplay());
    }

    /**
     * @expectedException League\Geotools\Exception\InvalidArgumentException
     * @expectedExceptionMessage Please provide an ellipsoid name !
     */
    public function testExecuteWithEmptyEllipsoidOption()
    {
        $this->commandTester->execute(array(
            'command'     => $this->command->getName(),
            'coordinate'  => '40° 26.7717, -79° 56.93172',
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
            'coordinate'  => '40° 26.7717, -79° 56.93172',
            '--ellipsoid' => 'foo',
        ));
    }

    public function testExecuteWithEllipsoidOption_MODIFIED_FISCHER_1960()
    {
        $this->commandTester->execute(array(
            'command'     => $this->command->getName(),
            'coordinate'  => '40° 26.7717, -79° 56.93172',
            '--ellipsoid' => 'MODIFIED_FISCHER_1960',
        ));

        $this->assertTrue(is_string($this->commandTester->getDisplay()));
        $this->assertRegExp('/17T 589139 4477828/', $this->commandTester->getDisplay());
    }

    public function testExecuteWithEllipsoidOption_BESSEL_1841_NAMBIA()
    {
        $this->commandTester->execute(array(
            'command'     => $this->command->getName(),
            'coordinate'  => '40° 26.7717, -79° 56.93172',
            '--ellipsoid' => 'BESSEL_1841_NAMBIA',
        ));

        $this->assertTrue(is_string($this->commandTester->getDisplay()));
        $this->assertRegExp('/17T 589129 4477424/', $this->commandTester->getDisplay());
    }

    public function testExecuteWithEllipsoidOption_CLARKE_1866()
    {
        $this->commandTester->execute(array(
            'command'     => $this->command->getName(),
            'coordinate'  => '40° 26.7717, -79° 56.93172',
            '--ellipsoid' => 'CLARKE_1866',
        ));

        $this->assertTrue(is_string($this->commandTester->getDisplay()));
        $this->assertRegExp('/17T 589141 4477602/', $this->commandTester->getDisplay());
    }
}
