<?php

/*
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools\CLI;

use League\Geotools\CLI\Output\ConsoleOutput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Application;
/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
class GeotoolsApplication extends Application
{
    /**
     * @var string
     */
    private $logo = '
      ________              __                .__
     /  _____/  ____  _____/  |_  ____   ____ |  |   ______
    /   \  ____/ __ \/  _ \   __\/  _ \ /  _ \|  |  /  ___/
    \    \_\  \  ___(  <_> )  | (  <_> |  <_> )  |__\___ \
     \______  /\___  >____/|__|  \____/ \____/|____/____  >
            \/     \/                                   \/

';

    /**
     * {@inheritdoc}
     */
    public function getHelp(): string
    {
        return $this->logo . parent::getHelp();
    }

    /**
     * {@inheritdoc}
     */
    public function run(InputInterface $input = null, OutputInterface $output = null): int
    {
        return parent::run($input, new ConsoleOutput);
    }
}
