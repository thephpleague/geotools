<?php

/**
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Geotools\CLI\Geohash;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Geotools\Geotools;
use Geotools\Geohash\Geohash;
use Geotools\Coordinate\Coordinate;

/**
 * Command-line geohash:encode class
 *
 * @author Antoine Corcy <contact@sbin.dk>
 */
class Encode extends Command
{
    protected function configure()
    {
        $this
            ->setName('geohash:encode')
            ->setDescription('Encode a coordinate to a geo hash string')
            ->addArgument('coordinate', InputArgument::REQUIRED, 'The "Lat,Long" coordinate to encode')
            ->addOption('length', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_OPTIONAL,
                sprintf('If set, the length betwwen %s and %s of the encoded coordinate', Geohash::MIN_LENGTH,
                    Geohash::MAX_LENGTH));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $coordinate = new Coordinate($input->getArgument('coordinate'));

        $geotools = new Geotools();
        $geohash  = $geotools->geohash();

        if ($input->getOption('length')) {
            $geohash = $geohash->encode($coordinate, $input->getOption('length'));
        } else {
            $geohash = $geohash->encode($coordinate);
        }

        $output->writeln(sprintf('<info>%s</info>', $geohash->getGeohash()));
    }
}
