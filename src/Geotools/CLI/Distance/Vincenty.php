<?php

/**
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Geotools\CLI\Distance;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Geotools\Geotools;
use Geotools\Coordinate\Coordinate;

/**
 * Command-line distance:vincenty class
 *
 * @author Antoine Corcy <contact@sbin.dk>
 */
class Vincenty extends Command
{
    protected function configure()
    {
        $this
            ->setName('distance:vincenty')
            ->setDescription('Compute the distance between 2 coordinates using the vincenty algorithm,  in meters by default')
            ->addArgument('origin', InputArgument::REQUIRED, 'The origin "Lat,Long" coordinate')
            ->addArgument('destination', InputArgument::REQUIRED, 'The destination "Lat,Long" coordinate')
            ->addOption('km', null, InputOption::VALUE_NONE, 'If set, the distance will be shown in kilometers')
            ->addOption('mi', null, InputOption::VALUE_NONE, 'If set, the distance will be shown in miles')
            ->addOption('ft', null, InputOption::VALUE_NONE, 'If set, the distance will be shown in feet')
            ->setHelp(<<<EOT
<info>Exemple</info>:              %command.full_name% "40° 26.7717, -79° 56.93172" "30°16′57″N 029°48′32″W" <comment>--mi</comment>
EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $from = new Coordinate($input->getArgument('origin'));
        $to   = new Coordinate($input->getArgument('destination'));

        $geotools = new Geotools();
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

        $output->getFormatter()->setStyle('value', new OutputFormatterStyle('green', 'black'));
        $output->writeln(sprintf('<value>%s</value>', $distance->vincenty()));
    }
}
