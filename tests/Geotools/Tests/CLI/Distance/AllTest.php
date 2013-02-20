<?php

/**
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Geotools\Tests\CLI\Distance;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Geotools\Tests\TestCase;
use Geotools\CLI\Distance\All;

/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
class AllTest extends TestCase
{
    protected $application;
    protected $command;
    protected $commandTester;

    protected function setUp()
    {
        $this->application = new Application();
        $this->application->add(new All());

        $this->command = $this->application->find('distance:all');

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
            'command'     => $this->command->getName(),
            'origin'      => 'foo, bar',
            'destination' => ' '
        ));
    }

    public function testExecuteWithoutOptions()
    {
        $this->commandTester->execute(array(
            'command'     => $this->command->getName(),
            'origin'      => '40° 26.7717, -79° 56.93172',
            'destination' => '30°16′57″N 029°48′32″W'
        ));

        $this->assertTrue(is_string($this->commandTester->getDisplay()));
        $this->assertRegExp('/4690203.1048522/', $this->commandTester->getDisplay());
        $this->assertRegExp('/4625820.522074/', $this->commandTester->getDisplay());
        $this->assertRegExp('/4629759.2704927/', $this->commandTester->getDisplay());
    }

    public function testExecuteWithKmOption()
    {
        $this->commandTester->execute(array(
            'command'     => $this->command->getName(),
            'origin'      => '40° 26.7717, -79° 56.93172',
            'destination' => '30°16′57″N 029°48′32″W',
            '--km'        => true
        ));

        $this->assertTrue(is_string($this->commandTester->getDisplay()));
        $this->assertRegExp('/4690.2031048522/', $this->commandTester->getDisplay());
        $this->assertRegExp('/4625.820522074/', $this->commandTester->getDisplay());
        $this->assertRegExp('/4629.7592704927/', $this->commandTester->getDisplay());
    }

    public function testExecuteWithMileOption()
    {
        $this->commandTester->execute(array(
            'command'     => $this->command->getName(),
            'origin'      => '40° 26.7717, -79° 56.93172',
            'destination' => '30°16′57″N 029°48′32″W',
            '--mile'      => true
        ));

        $this->assertTrue(is_string($this->commandTester->getDisplay()));
        $this->assertRegExp('/2914.3570950973/', $this->commandTester->getDisplay());
        $this->assertRegExp('/2874.351612877/', $this->commandTester->getDisplay());
        $this->assertRegExp('/2876.7990376779/', $this->commandTester->getDisplay());
    }
}
