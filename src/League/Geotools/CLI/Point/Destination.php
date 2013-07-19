<?php

/**
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools\CLI\Point;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use League\Geotools\Geotools;
use League\Geotools\Coordinate\Coordinate;
use League\Geotools\Coordinate\Ellipsoid;

/**
 * Command-line point:destination class
 *
 * @author Antoine Corcy <contact@sbin.dk>
 */
class Destination extends Command
{
    protected function configure()
    {
        $availableEllipsoids = Ellipsoid::getAvailableEllipsoidNames();

        $this
            ->setName('point:destination')
            ->setDescription('Compute the destination coordinate with given bearing in degrees and a distance in meters')
            ->addArgument('origin', InputArgument::REQUIRED, 'The origin "Lat,Long" coordinate')
            ->addArgument('bearing', InputArgument::REQUIRED, 'The initial bearing in degrees')
            ->addArgument('distance', InputArgument::REQUIRED, 'The distance from the origin coordinate in meters')
            ->addOption('ellipsoid', null, InputOption::VALUE_REQUIRED,
                'If set, the name of the ellipsoid to use', Ellipsoid::WGS84)
            ->setHelp(<<<EOT
<info>Available ellipsoids</info>: $availableEllipsoids

<info>Example with SOUTH_AMERICAN_1969 ellipsoid, 25 degrees and 10000 meters</info>:

    %command.full_name% "40° 26.7717, -79° 56.93172" 25 10000 <comment>--ellipsoid=SOUTH_AMERICAN_1969</comment>
EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $from     = new Coordinate($input->getArgument('origin'), Ellipsoid::createFromName($input->getOption('ellipsoid')));
        $geotools = new Geotools();

        $destination = $geotools->point()->setFrom($from);
        $destination = $destination->destination($input->getArgument('bearing'), $input->getArgument('distance'));

        $output->getFormatter()->setStyle('value', new OutputFormatterStyle('green', 'black'));
        $output->writeln(sprintf(
            '<value>%s, %s</value>',
            $destination->getLatitude(), $destination->getLongitude()
        ));
    }
}
