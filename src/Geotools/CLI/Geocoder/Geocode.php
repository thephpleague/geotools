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
use Geotools\CLI\Command;

/**
 * Command-line geocoder:geocode class
 *
 * @author Antoine Corcy <contact@sbin.dk>
 */
class Geocode extends Command
{
    protected function configure()
    {
        $this
            ->setName('geocoder:geocode')
            ->setDescription('Geocode a street-address, IPv4 or IPv6 against a provider with an adapter')
            ->addArgument('value', InputArgument::REQUIRED, 'The street-address, IPv4 or IPv6 to geocode')
            ->addOption('provider', null, InputOption::VALUE_REQUIRED,
                'If set, the name of the provider to use, Google Maps by default')
            ->addOption('adapter', null, InputOption::VALUE_REQUIRED,
                'If set, the name of the adapter to use, cURL by default')
            ->addOption('args', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'If set, the provider constructor arguments like api key, locale, region, ssl, toponym and service');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
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

        $geocoded = $geocoder->geocode($input->getArgument('value'));

        $output->writeln(sprintf('<info>%s, %s</info>', $geocoded->getLatitude(), $geocoded->getLongitude()));
    }
}
