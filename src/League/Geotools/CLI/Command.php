<?php

/**
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools\CLI;

use Symfony\Component\Console\Command\Command as BaseCommand;

/**
 * Command class
 *
 * @author Antoine Corcy <contact@sbin.dk>
 */
class Command extends BaseCommand
{
    /**
     * Available adapters.
     *
     * @var array
     */
    private $adapters = array(
        'buzz'    => 'BuzzHttpAdapter',
        'curl'    => 'CurlHttpAdapter',
        'guzzle'  => 'GuzzleHttpAdapter',
        'socket'  => 'SocketHttpAdapter',
        'zend'    => 'ZendHttpAdapter',
    );

    /**
     * Available providers.
     *
     * @var array
     */
    private $providers = array(
        'free_geo_ip'          => 'FreeGeoIpProvider',
        'host_ip'              => 'HostIpProvider',
        'ip_info_db'           => 'IpInfoDbProvider',
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
        'tomtom'               => 'TomTomProvider',
        'arcgis_online'        => 'ArcGISOnlineProvider',
    );

    /**
     * Available dumpers.
     *
     * @var array
     */
    private $dumpers = array(
        'gpx'     => 'GpxDumper',
        'geojson' => 'GeoJsonDumper',
        'kml'     => 'KmlDumper',
        'wkb'     => 'WkbDumper',
        'wkt'     => 'WktDumper',
    );


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
        $adapter = $this->lowerize((trim($adapter)));
        $adapter = array_key_exists($adapter, $this->adapters)
            ? $this->adapters[$adapter]
            : $this->adapters['curl'];

        return '\\Geocoder\\HttpAdapter\\' . $adapter;
    }

    /**
     * Returns the list of available adapters sorted by alphabetical order.
     *
     * @return string The list of available adapters comma separated.
     */
    protected function getAdapters()
    {
        ksort($this->adapters);

        return implode(', ', array_keys($this->adapters));
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
        $provider = array_key_exists($provider, $this->providers)
            ? $this->providers[$provider]
            : $this->providers['google_maps'];

        return '\\Geocoder\\Provider\\' . $provider;
    }

    /**
     * Returns the list of available providers sorted by alphabetical order.
     *
     * @return string The list of available providers comma separated.
     */
    protected function getProviders()
    {
        ksort($this->providers);

        return implode(', ', array_keys($this->providers));
    }

    /**
     * Returns the dumper class name.
     * The default dumper is WktDumper.
     *
     * @param string $dumper The name of the dumper to use.
     *
     * @return string The name of the dumper class to use.
     */
    protected function getDumper($dumper)
    {
        $dumper = $this->lowerize((trim($dumper)));
        $dumper = array_key_exists($dumper, $this->dumpers)
            ? $this->dumpers[$dumper]
            : $this->dumpers['wkt'];

        return '\\Geocoder\\Dumper\\' . $dumper;
    }

    /**
     * Returns the list of available dumpers sorted by alphabetical order.
     *
     * @return string The list of available dumpers comma separated.
     */
    protected function getDumpers()
    {
        ksort($this->dumpers);

        return implode(', ', array_keys($this->dumpers));
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
