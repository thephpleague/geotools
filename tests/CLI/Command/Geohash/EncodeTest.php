<?php

/*
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools\Tests\CLI\Command\Geohash;

use League\Geotools\CLI\Application;
use League\Geotools\CLI\Command\Geohash\Encode;
use League\Geotools\Geohash\Geohash;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
class EncodeTest extends \League\Geotools\Tests\TestCase
{
    protected $application;
    protected $command;
    protected $commandTester;

    protected function setup(): void
    {
        $this->application = new Application;
        $this->application->add(new Encode);

        $this->command = $this->application->find('geohash:encode');

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
        $this->assertMatchesRegularExpression('/u09tu800gnqw/', $this->commandTester->getDisplay());
    }

    public function testExecuteInvalidLengthOption()
    {
        $this->expectException(\League\Geotools\Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage('The length should be between 1 and 12.');
        $this->commandTester->execute(array(
            'command'    => $this->command->getName(),
            'coordinate' => '48.8234055, 2.3072664',
            '--length'   => 13,
        ));
    }

    public function testExecuteWithLengthOption()
    {
        $this->commandTester->execute(array(
            'command'    => $this->command->getName(),
            'coordinate' => '40° 26.7717, -79° 56.93172',
            '--length'   => 4,
        ));

        $this->assertTrue(is_string($this->commandTester->getDisplay()));
        $this->assertMatchesRegularExpression('/<value>dppn<\/value>/', $this->commandTester->getDisplay());
    }

    public function testExecuteWithEmptyLengthOption()
    {
        $this->expectException(\League\Geotools\Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage('The length should be between 1 and 12.');
        $this->commandTester->execute(array(
            'command'    => $this->command->getName(),
            'coordinate' => '40° 26.7717, -79° 56.93172',
            '--length'   => '',
        ));
    }
}
