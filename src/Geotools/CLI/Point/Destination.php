<?php

/**
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Geotools\CLI\Point;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Geotools\Geotools;
use Geotools\Coordinate\Coordinate;

/**
 * Command-line point:destination class
 *
 * @author Antoine Corcy <contact@sbin.dk>
 */
class Destination extends Command
{
    protected function configure()
    {
        $this
            ->setName('point:destination')
            ->setDescription('Compute the destination coordinate with given bearing in degrees and a distance in meters')
            ->addArgument('origin', InputArgument::REQUIRED, 'The origin "Lat,Long" coordinate')
            ->addArgument('bearing', InputArgument::REQUIRED, 'The initial bearing in degrees')
            ->addArgument('distance', InputArgument::REQUIRED, 'The distance from the origin coordinate in meters');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $from = new Coordinate($input->getArgument('origin'));

        $geotools = new Geotools();
        $destination = $geotools->point()->setFrom($from)
            ->destination($input->getArgument('bearing'), $input->getArgument('distance'));

        $output->writeln(sprintf('<info>%s, %s</info>', $destination->getLatitude(), $destination->getLongitude()));
    }
}
