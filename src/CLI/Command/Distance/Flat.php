<?php

/*
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools\CLI\Command\Distance;

use League\Geotools\Coordinate\Coordinate;
use League\Geotools\Coordinate\Ellipsoid;
use League\Geotools\Geotools;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command-line distance:flat class
 *
 * @author Antoine Corcy <contact@sbin.dk>
 */
class Flat extends \Symfony\Component\Console\Command\Command
{
    protected function configure()
    {
        $availableEllipsoids = Ellipsoid::getAvailableEllipsoidNames();

        $this
            ->setName('distance:flat')
            ->setDescription('Compute the distance between 2 coordinates using the flat algorithm, in meters by default')
            ->addArgument('origin', InputArgument::REQUIRED, 'The origin "Lat,Long" coordinate')
            ->addArgument('destination', InputArgument::REQUIRED, 'The destination "Lat,Long" coordinate')
            ->addOption('km', null, InputOption::VALUE_NONE, 'If set, the distance will be shown in kilometers')
            ->addOption('mi', null, InputOption::VALUE_NONE, 'If set, the distance will be shown in miles')
            ->addOption('ft', null, InputOption::VALUE_NONE, 'If set, the distance will be shown in feet')
            ->addOption('ellipsoid', null, InputOption::VALUE_REQUIRED,
                'If set, the name of the ellipsoid to use', Ellipsoid::WGS84)
            ->setHelp(<<<EOT
<info>Available ellipsoids</info>: $availableEllipsoids

<info>Example with WGS60 ellipsoid and output in kilometers</info>:

    %command.full_name% "40° 26.7717, -79° 56.93172" "30°16′57″N 029°48′32″W" <comment>--ellipsoid=WGS60 --km</comment>
EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $ellipsoid = Ellipsoid::createFromName($input->getOption('ellipsoid'));
        $from      = new Coordinate($input->getArgument('origin'), $ellipsoid);
        $to        = new Coordinate($input->getArgument('destination'), $ellipsoid);

        $geotools = new Geotools;
        $distance = $geotools->distance()->setFrom($from)->setTo($to);

        if ($input->getOption('km')) {
            $distance->in('km');
        }

        if ($input->getOption('mi')) {
            $distance->in('mi');
        }

        if ($input->getOption('ft')) {
            $distance->in('ft');
        }

        $output->writeln(sprintf('<value>%s</value>', $distance->flat()));
        return 0;
    }
}
