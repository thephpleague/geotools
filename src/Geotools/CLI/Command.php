<?php

/**
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Geotools\CLI;

use Symfony\Component\Console\Command\Command as BaseCommand;

/**
 * Command class
 *
 * @author Antoine Corcy <contact@sbin.dk>
 */
class Command extends BaseCommand
{
    /**
     * Returns the adapter class name.
     * The default adapter is cURL.
     *
     * @param string $adapter The name of the adapter.
     *
     * @return string The name of the adapter class.
     */
    protected function getAdapter($adapter)
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
    protected function getProvider($provider)
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
