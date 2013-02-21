<?php

/**
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Geotools\Tests\CLI\Point;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Geotools\Tests\TestCase;
use Geotools\CLI\Point\Destination;

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
     * @expectedException Geotools\Exception\InvalidArgumentException
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
        $this->assertRegExp('/47\.026774663314, 2\.3072664/', $this->commandTester->getDisplay());
    }
}
