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

use League\Geotools\Coordinate\Coordinate;
use League\Geotools\Geohash\Geohash;
use League\Geotools\Geotools;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command-line geohash:encode class
 *
 * @author Antoine Corcy <contact@sbin.dk>
 */
class Encode extends \Symfony\Component\Console\Command\Command
{
    protected function configure()
    {
        $this
            ->setName('geohash:encode')
            ->setDescription('Encode a coordinate to a geo hash string, the length is 12 by default')
            ->addArgument('coordinate', InputArgument::REQUIRED, 'The "Lat,Long" coordinate to encode')
            ->addOption('length', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_OPTIONAL,
                sprintf('If set, the length between %s and %s of the encoded coordinate', Geohash::MIN_LENGTH,
                    Geohash::MAX_LENGTH), 12)
            ->setHelp(<<<EOT
<info>Example</info>:              %command.full_name% "40° 26.7717, -79° 56.93172" <comment>--length=3</comment>
EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $coordinate = new Coordinate($input->getArgument('coordinate'));
        $geotools   = new Geotools;

        $output->writeln(sprintf(
            '<value>%s</value>',
            $geotools->geohash()->encode($coordinate, $input->getOption('length'))->getGeohash()
        ));
        return 0;
    }
}
