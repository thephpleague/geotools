<?php

/*
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools\CLI\Command\Geohash;

use League\Geotools\Geotools;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command-line geohash:decode class
 *
 * @author Antoine Corcy <contact@sbin.dk>
 */
class Decode extends \Symfony\Component\Console\Command\Command
{
    protected function configure()
    {
        $this
            ->setName('geohash:decode')
            ->setDescription('Decode a geo hash string to a coordinate')
            ->addArgument('geohash', InputArgument::REQUIRED, 'The geo hash to decode to coordinate')
            ->setHelp(<<<EOT
<info>Example</info>:              %command.full_name% spey61y
EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $geotools   = new Geotools;
        $coordinate = $geotools->geohash()->decode($input->getArgument('geohash'))->getCoordinate();

        $output->writeln(sprintf(
            '<value>%s, %s</value>',
            $coordinate->getLatitude(), $coordinate->getLongitude()
        ));
        return 0;
    }
}
