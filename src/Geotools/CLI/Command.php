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
     * @param string $adapter The name of the adapter to use.
     *
     * @return string The name of the adapter class to use.
     */
    protected function getAdapter($adapter)
    {
        $adapter  = $this->lowerize((trim($adapter)));
        $adapters = array(
            'buzz'    => 'BuzzHttpAdapter',
            'curl'    => 'CurlHttpAdapter',
            'guzzle'  => 'GuzzleHttpAdapter',
            'socket'  => 'SocketHttpAdapter',
            'zend'    => 'ZendHttpAdapter',
            'default' => 'CurlHttpAdapter',
        );

        $adapter = array_key_exists($adapter, $adapters) ? $adapters[$adapter] : $adapters['default'];

        return '\\Geocoder\\HttpAdapter\\' . $adapter;
    }

    /**
     * Returns the provider class name.
     * The default provider is Google Maps.
     *
     * @param string $provider The name of the provider to use.
     *
     * @return string The name of the provider class to use.
     */
    protected function getProvider($provider)
    {
        $provider = $this->lowerize((trim($provider)));
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
            'ip_geo_base'          => 'IpGeoBaseProvider',
            'baidu'                => 'BaiduProvider',
            'default'              => 'GoogleMapsProvider',
        );

        $provider = array_key_exists($provider, $providers) ? $providers[$provider] : $providers['default'];

        return '\\Geocoder\\Provider\\' . $provider;
    }

    /**
     * Retunrs the dumper class name.
     * The default dumper is WktDumper.
     *
     * @param string $dumper The name of the dumper to use.
     *
     * @return string The name of the dumper class to use.
     */
    protected function getDumper($dumper)
    {
        $dumper = $this->lowerize((trim($dumper)));
        $dumpers = array(
            'gpx'     => 'GpxDumper',
            'geojson' => 'GeoJsonDumper',
            'kml'     => 'KmlDumper',
            'wkb'     => 'WkbDumper',
            'wkt'     => 'WktDumper',
            'default' => 'WktDumper',
        );

        $dumper = array_key_exists($dumper, $dumpers) ? $dumpers[$dumper] : $dumpers['default'];

        return '\\Geocoder\\Dumper\\' . $dumper;
    }

    /**
     * Make a string lowercase.
     *
     * @param string $string A string to lowercase.
     *
     * @return string The lowercased string.
     */
    private function lowerize($string)
    {
        return extension_loaded('mbstring') ? mb_strtolower($string, 'UTF-8') : strtolower($string);
    }
}
