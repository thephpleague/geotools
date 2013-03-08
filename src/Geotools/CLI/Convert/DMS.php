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
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Geotools\Geotools;
use Geotools\Coordinate\Coordinate;
use Geotools\Convert\ConvertInterface;

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
                'If set, the format of the converted degrees minutes seconds coordinate',
                ConvertInterface::DEFAULT_DMS_FORMAT)
            ->setHelp(<<<EOT
<info>Exemple</info>:              %command.full_name% "40.446195, -79.948862" <comment>--format="%P%D:%M:%S, %p%d:%m:%s"</comment>
EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $coordinate = new Coordinate($input->getArgument('coordinate'));
        $geotools   = new Geotools();

        $output->getFormatter()->setStyle('value', new OutputFormatterStyle('green', 'black'));
        $output->writeln(sprintf(
            '<value>%s</value>',
            $geotools->convert($coordinate)->toDMS($input->getOption('format'))
        ));
    }
}
