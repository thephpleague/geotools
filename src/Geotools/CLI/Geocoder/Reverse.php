<?php

/**
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Geotools\CLI\Geocoder;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Geocoder\Geocoder;
use Geocoder\Formatter\Formatter;
use Geotools\Coordinate\Coordinate;
use Geotools\CLI\Command;

/**
 * Command-line geocoder:revese class
 *
 * @author Antoine Corcy <contact@sbin.dk>
 */
class Reverse extends Command
{
    protected function configure()
    {
        $this
            ->setName('geocoder:reverse')
            ->setDescription('Reverse geocode street address, IPv4 or IPv6 against a provider with an adapter')
            ->addArgument('coordinate', InputArgument::REQUIRED, 'The coordinate to reverse')
            ->addOption('provider', null, InputOption::VALUE_REQUIRED,
                'If set, the name of the provider to use, Google Maps by default')
            ->addOption('adapter', null, InputOption::VALUE_REQUIRED,
                'If set, the name of the adapter to use, cURL by default')
            ->addOption('args', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'If set, the provider constructor arguments like api key, locale, region, ssl, toponym and service')
            ->addOption('format', null, InputOption::VALUE_REQUIRED,
                'If set, the format of the revers geocoding result');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $coordinate = new Coordinate($input->getArgument('coordinate'));

        $geocoder = new Geocoder();
        $adapter  = $this->getAdapter($input->getOption('adapter'));
        $provider = $this->getProvider($input->getOption('provider'));

        if ($input->getOption('args')) {
            $args = is_array($input->getOption('args'))
                ? implode(',', $input->getOption('args'))
                : $input->getOption('args');
            $geocoder->registerProvider(new $provider(new $adapter(), $args));
        } else {
            $geocoder->registerProvider(new $provider(new $adapter()));
        }

        $reversed = $geocoder->reverse($coordinate->getLatitude(), $coordinate->getLongitude());

        $formatter = new Formatter($reversed);

        if ($input->getOption('format')) {
            $formatted = $formatter->format($input->getOption('format'));
        } else {
            $formatted = $formatter->format('%S %n, %z %L');
        }

        $output->writeln(sprintf('<info>%s</info>', $formatted));
    }
}
