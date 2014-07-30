<?php

/**
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools\Tests\CLI\Geohash;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use League\Geotools\Tests\TestCase;
use League\Geotools\Geohash\Geohash;
use League\Geotools\CLI\Geohash\Encode;

/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
class EncodeTest extends TestCase
{
    protected $application;
    protected $command;
    protected $commandTester;

    protected function setUp()
    {
        $this->application = new Application();
        $this->application->add(new Encode());

        $this->command = $this->application->find('geohash:encode');

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
            '--length'   => ' ',
        ));
    }

    public function testExecuteWithoutLengthOption()
    {
        $this->commandTester->execute(array(
            'command'    => $this->command->getName(),
            'coordinate' => '48.8234055, 2.3072664',
        ));

        $this->assertTrue(is_string($this->commandTester->getDisplay()));
        $this->assertRegExp('/u09tu800gnqw/', $this->commandTester->getDisplay());
    }

    /**
     * @expectedException League\Geotools\Exception\InvalidArgumentException
     * @expectedExceptionMessage The length should be between 1 and 12.
     */
    public function testExecuteInvalidLengthOption()
    {
        $this->commandTester->execute(array(
            'command'    => $this->command->getName(),
            'coordinate' => '48.8234055, 2.3072664',
            '--length'   => 13,
        ));
    }

    public function testExecuteWithLengthOption()
    {
        $length = 4;

        $this->commandTester->execute(array(
            'command'    => $this->command->getName(),
            'coordinate' => '40° 26.7717, -79° 56.93172',
            '--length'   => $length,
        ));

        $this->assertTrue(is_string($this->commandTester->getDisplay()));
        $this->assertEquals($length, strlen(trim($this->commandTester->getDisplay())));
        $this->assertRegExp('/dppn/', $this->commandTester->getDisplay());
    }

    /**
     * @expectedException League\Geotools\Exception\InvalidArgumentException
     * @expectedExceptionMessage The length should be between 1 and 12.
     */
    public function testExecuteWithEmptyLengthOption()
    {
        $this->commandTester->execute(array(
            'command'    => $this->command->getName(),
            'coordinate' => '40° 26.7717, -79° 56.93172',
            '--length'   => '',
        ));
    }
}
