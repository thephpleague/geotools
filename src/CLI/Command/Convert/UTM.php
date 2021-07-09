<?php

/*
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools\CLI\Command\Convert;

use League\Geotools\Coordinate\Coordinate;
use League\Geotools\Coordinate\Ellipsoid;
use League\Geotools\Geotools;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command-line convert:utm class
 *
 * @author Antoine Corcy <contact@sbin.dk>
 */
class UTM extends \Symfony\Component\Console\Command\Command
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
        $geotools   = new Geotools;

        $output->writeln(sprintf(
            '<value>%s</value>',
            $geotools->convert($coordinate)->toUniversalTransverseMercator()
        ));
        return 0;
    }
}
