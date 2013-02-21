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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Geocoder\Geocoder;
use Geocoder\Formatter\Formatter;
use Geotools\Coordinate\Coordinate;

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
                'If set, the provider constructor arguments like api, locale, region, ssl, toponym and service')
            ->addOption('format', null, InputOption::VALUE_REQUIRED,
                'If set, the format of the reversed geocoding');
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

    /**
     * Returns the adapter class name.
     * The default adapter is curl.
     *
     * @param string $adapter The name of the adapter.
     *
     * @return string The name of the adapter class.
     */
    private function getAdapter($adapter)
    {
        $adapter  = strtolower($adapter);
        $adapters = array(
            'buzz'   => 'BuzzHttpAdapter',
            'curl'   => 'CurlHttpAdapter',
            'guzzle' => 'GuzzleHttpAdapter',
            'socket' => 'SocketHttpAdapter',
            'zend'   => 'ZendHttpAdapter',
        );

        $adapter = array_key_exists($adapter, $adapters) ? $adapters[$adapter] : $adapters['curl'];

        return '\\Geocoder\\HttpAdapter\\' . $adapter;
    }

    /**
     * Returns the provider class name.
     * The default provider is Google Maps.
     *
     * @param string $provider The name of the provider to use.
     *
     * @return string The name of the provider class name to use.
     */
    private function getProvider($provider)
    {
        $provider = strtolower($provider);
        $providers = array(
            'free_geo_ip'          => 'FreeGeoIpProvider',
            'host_ip'              => 'HostIpProvider',
            'ip_info_db'           => 'IpInfoDbProvider',
            'yahoo'                => 'YahooProvider',
            'google_maps'          => 'GoogleMapsProvider',
            'google_maps_business' => 'GoogleMapsBusinessProvider',
            'bing_maps'            => 'BingMapsProvider',
            'openstreetmaps'       => 'OpenStreetMapsProvider',
            'cloudmade'            => 'CloudMadeProvider',
            'geoip'                => 'GeoipProvider',
            'map_quest'            => 'MapQuestProvider',
            'oio_rest'             => 'OIORestProvider',
            'geocoder_ca'          => 'GeocoderCaProvider',
            'geocoder_us'          => 'GeocoderUsProvider',
            'ign_openls'           => 'IGNOpenLSProvider',
            'data_science_toolkit' => 'DataScienceToolkitProvider',
            'yandex'               => 'YandexProvider',
            'geo_plugin'           => 'GeoPluginProvider',
            'geo_ips'              => 'GeoIPsProvider',
            'maxmind'              => 'MaxMindProvider',
            'geonames'             => 'GeonamesProvider',
        );

        $provider = array_key_exists($provider, $providers) ? $providers[$provider] : $providers['google_maps'];

        return '\\Geocoder\\Provider\\' . $provider;
    }
}
