<?php

/**
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Geotools\CLI\Convert;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Geotools\Geotools;
use Geotools\Coordinate\Coordinate;

/**
 * Command-line convert:dms class
 *
 * @author Antoine Corcy <contact@sbin.dk>
 */
class DMS extends Command
{
    protected function configure()
    {
        $this
            ->setName('convert:dms')
            ->setDescription('Convert and format decimal degrees coordinates to degrees minutes seconds coordinate')
            ->addArgument('coordinate', InputArgument::REQUIRED, 'The "Lat,Long" coordinate')
            ->addOption('format', null, InputOption::VALUE_REQUIRED,
                'If set, the format of the converted degrees minutes seconds coordinate');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $coordinate = new Coordinate($input->getArgument('coordinate'));

        $geotools = new Geotools();
        $convert  = $geotools->convert($coordinate);

        if ($input->getOption('format')) {
            $convert = $convert->toDMS($input->getOption('format'));
        } else {
            $convert = $convert->toDMS();
        }

        $output->writeln(sprintf('<info>%s</info>', $convert));
    }
}
