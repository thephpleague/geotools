<?php

/**
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Geotools\CLI\Convert;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Geotools\Geotools;
use Geotools\Coordinate\Coordinate;

/**
 * Command-line convert:utm class
 *
 * @author Antoine Corcy <contact@sbin.dk>
 */
class UTM extends Command
{
    protected function configure()
    {
        $this
            ->setName('convert:utm')
            ->setDescription('Convert decimal degrees coordinates in the Universal Transverse Mercator projection')
            ->addArgument('coordinate', InputArgument::REQUIRED, 'The "Lat,Long" coordinate');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $coordinate = new Coordinate($input->getArgument('coordinate'));

        $geotools = new Geotools();

        $output->writeln(sprintf('<info>%s</info>', $geotools->convert($coordinate)->toUTM()));
    }
}
