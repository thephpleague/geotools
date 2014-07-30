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
use League\Geotools\CLI\Geohash\Decode;

/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
class DecodeTest extends TestCase
{
    protected $application;
    protected $command;
    protected $commandTester;

    protected function setUp()
    {
        $this->application = new Application();
        $this->application->add(new Decode());

        $this->command = $this->application->find('geohash:decode');

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
     * @expectedException League\Geotools\Exception\RuntimeException
     * @expectedExceptionMessage This geo hash is invalid.
     */
    public function testExecuteInvalidArguments()
    {
        $this->commandTester->execute(array(
            'command' => $this->command->getName(),
            'geohash' => 'foo, bar',
        ));
    }

    public function testExecuteShortGeohash()
    {
        $this->commandTester->execute(array(
            'command' => $this->command->getName(),
            'geohash' => 'dp',
        ));

        $this->assertTrue(is_string($this->commandTester->getDisplay()));
        $this->assertRegExp('/42\.1875, -84\.375/', $this->commandTester->getDisplay());
    }

    public function testExecuteLongGeohash()
    {
        $this->commandTester->execute(array(
            'command' => $this->command->getName(),
            'geohash' => 'dppnhep00mpx',
        ));

        $this->assertTrue(is_string($this->commandTester->getDisplay()));
        $this->assertRegExp('/40\.446195071563, -79\.948862101883/', $this->commandTester->getDisplay());
    }
}
