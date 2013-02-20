<?php

/**
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Geotools\CLI\Distance;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Geotools\Geotools;
use Geotools\Coordinate\Coordinate;

/**
 * Command-line distance:haversine class
 *
 * @author Antoine Corcy <contact@sbin.dk>
 */
class Haversine extends Command
{
    protected function configure()
    {
        $this
            ->setName('distance:haversine')
            ->setDescription('Compute the distance between 2 coordinates using the haversine algorithm')
            ->addArgument('origin', InputArgument::REQUIRED, 'The origin "Lat,Long" coordinate')
            ->addArgument('destination', InputArgument::REQUIRED, 'The destination "Lat,Long" coordinate')
            ->addOption('km', null, InputOption::VALUE_NONE, 'If set, the distance will be shown in kilometers')
            ->addOption('mile', null, InputOption::VALUE_NONE, 'If set, the distance will be shown in miles');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $from = new Coordinate($input->getArgument('origin'));
        $to   = new Coordinate($input->getArgument('destination'));

        $geotools = new Geotools();
        $distance = $geotools->distance()->setFrom($from)->setTo($to);

        if ($input->getOption('km')) {
            $distance->in('km');
        }

        if ($input->getOption('mile')) {
            $distance->in('mile');
        }

        $output->writeln(sprintf('<info>%s</info>', $distance->haversine()));
    }
}
