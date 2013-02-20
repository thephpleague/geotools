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
 * Command-line point:middle class
 *
 * @author Antoine Corcy <contact@sbin.dk>
 */
class Middle extends Command
{
    protected function configure()
    {
        $this
            ->setName('point:middle')
            ->setDescription('Compute the half-way coordinate between 2 coordinates')
            ->addArgument('origin', InputArgument::REQUIRED, 'The origin "Lat,Long" coordinate')
            ->addArgument('destination', InputArgument::REQUIRED, 'The destination "Lat,Long" coordinate');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $from = new Coordinate($input->getArgument('origin'));
        $to   = new Coordinate($input->getArgument('destination'));

        $geotools = new Geotools();

        $output->writeln(sprintf(
            '<info>%s, %s</info>',
            $geotools->point()->setFrom($from)->setTo($to)->middle()->getLatitude(),
            $geotools->point()->setFrom($from)->setTo($to)->middle()->getLongitude()
        ));
    }
}
