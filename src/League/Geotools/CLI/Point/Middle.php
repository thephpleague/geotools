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
 * Command-line point:middle class
 *
 * @author Antoine Corcy <contact@sbin.dk>
 */
class Middle extends Command
{
    protected function configure()
    {
        $availableEllipsoids = Ellipsoid::getAvailableEllipsoidNames();

        $this
            ->setName('point:middle')
            ->setDescription('Compute the half-way coordinate between 2 coordinates')
            ->addArgument('origin', InputArgument::REQUIRED, 'The origin "Lat,Long" coordinate')
            ->addArgument('destination', InputArgument::REQUIRED, 'The destination "Lat,Long" coordinate')
            ->addOption('ellipsoid', null, InputOption::VALUE_REQUIRED,
                'If set, the name of the ellipsoid to use', Ellipsoid::WGS84)
            ->setHelp(<<<EOT
<info>Available ellipsoids</info>: $availableEllipsoids

<info>Example with KRASSOVSKY ellipsoid</info>:

    %command.full_name% "40° 26.7717, -79° 56.93172" "30°16′57″N 029°48′32″W" <comment>--ellipsoid=KRASSOVSKY</comment>
EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ellipsoid = Ellipsoid::createFromName($input->getOption('ellipsoid'));
        $from      = new Coordinate($input->getArgument('origin'), $ellipsoid);
        $to        = new Coordinate($input->getArgument('destination'), $ellipsoid);

        $geotools = new Geotools();
        $middle   = $geotools->point()->setFrom($from)->setTo($to)->middle();

        $output->getFormatter()->setStyle('value', new OutputFormatterStyle('green', 'black'));
        $output->writeln(sprintf(
            '<value>%s, %s</value>',
            $middle->getLatitude(), $middle->getLongitude()
        ));
    }
}
