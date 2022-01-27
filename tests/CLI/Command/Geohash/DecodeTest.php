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

use League\Geotools\CLI\GeotoolsApplication;
use League\Geotools\CLI\Command\Geohash\Decode;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
class DecodeTest extends \League\Geotools\Tests\TestCase
{
    protected $application;
    protected $command;
    protected $commandTester;

    protected function setup(): void
    {
        $this->application = new GeotoolsApplication();
        $this->application->add(new Decode);

        $this->command = $this->application->find('geohash:decode');

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
        $this->expectException(\League\Geotools\Exception\RuntimeException::class);
        $this->expectExceptionMessage('This geo hash is invalid.');
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
        $this->assertMatchesRegularExpression('/42\.1875, -84\.375/', $this->commandTester->getDisplay());
    }

    public function testExecuteLongGeohash()
    {
        $this->commandTester->execute(array(
            'command' => $this->command->getName(),
            'geohash' => 'dppnhep00mpx',
        ));

        $this->assertTrue(is_string($this->commandTester->getDisplay()));
        $this->assertMatchesRegularExpression('/40\.4461950715631, -79\.9488621018827/', $this->commandTester->getDisplay());
    }
}
