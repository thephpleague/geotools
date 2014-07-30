<?php

/**
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools\CLI\Convert;

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
 * Command-line convert:utm class
 *
 * @author Antoine Corcy <contact@sbin.dk>
 */
class UTM extends Command
{
    protected function configure()
    {
        $availableEllipsoids = Ellipsoid::getAvailableEllipsoidNames();

        $this
            ->setName('convert:utm')
            ->setDescription('Convert decimal degrees coordinates in the Universal Transverse Mercator projection')
            ->addArgument('coordinate', InputArgument::REQUIRED, 'The "Lat,Long" coordinate')
            ->addOption('ellipsoid', null, InputOption::VALUE_REQUIRED,
                'If set, the name of the ellipsoid to use', Ellipsoid::WGS84)
            ->setHelp(<<<EOT
<info>Available ellipsoids</info>: $availableEllipsoids

<info>Example with CLARKE_1866 ellipsoid</info>:

    %command.full_name% "40.446195, -79.948862" <comment>--ellipsoid=CLARKE_1866</comment>
EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ellipsoid  = Ellipsoid::createFromName($input->getOption('ellipsoid'));
        $coordinate = new Coordinate($input->getArgument('coordinate'), $ellipsoid);
        $geotools   = new Geotools();

        $output->getFormatter()->setStyle('value', new OutputFormatterStyle('green', 'black'));
        $output->writeln(sprintf(
            '<value>%s</value>',
            $geotools->convert($coordinate)->toUTM()
        ));
    }
}
