<?php

/*
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools\CLI\Command\Vertex;

use League\Geotools\Coordinate\Coordinate;
use League\Geotools\Coordinate\Ellipsoid;
use League\Geotools\Geotools;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command-line vertex:initial-bearing class
 *
 * @author Antoine Corcy <contact@sbin.dk>
 */
class InitialBearing extends \Symfony\Component\Console\Command\Command
{
    protected function configure()
    {
        $availableEllipsoids = Ellipsoid::getAvailableEllipsoidNames();

        $this
            ->setName('vertex:initial-bearing')
            ->setDescription('Compute the initial bearing in degrees between 2 coordinates')
            ->addArgument('origin', InputArgument::REQUIRED, 'The origin "Lat,Long" coordinate')
            ->addArgument('destination', InputArgument::REQUIRED, 'The destination "Lat,Long" coordinate')
            ->addOption('ellipsoid', null, InputOption::VALUE_REQUIRED,
                'If set, the name of the ellipsoid to use', Ellipsoid::WGS84)
            ->setHelp(<<<EOT
<info>Available ellipsoids</info>: $availableEllipsoids

<info>Example with FISCHER_1968 ellipsoid</info>:

    %command.full_name% "40° 26.7717, -79° 56.93172" "30°16′57″N 029°48′32″W" <comment>--ellipsoid=FISCHER_1968</comment>
EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ellipsoid = Ellipsoid::createFromName($input->getOption('ellipsoid'));
        $from      = new Coordinate($input->getArgument('origin'), $ellipsoid);
        $to        = new Coordinate($input->getArgument('destination'), $ellipsoid);

        $geotools = new Geotools;

        $output->writeln(sprintf(
            '<value>%s</value>',
            $geotools->vertex()->setFrom($from)->setTo($to)->initialBearing()
        ));
        return 0;
    }
}
