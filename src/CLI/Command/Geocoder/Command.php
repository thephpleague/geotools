<?php

/*
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools\CLI\Command\Geocoder;

use Psr\Cache\CacheItemPoolInterface;

/**
 * Command class
 *
 * @author Antoine Corcy <contact@sbin.dk>
 */
class Command extends \Symfony\Component\Console\Command\Command
{
    /**
     * Available providers.
     *
     * @var array
     */
    private $providers = [
        'free_geo_ip'          => 'FreeGeoIp',
        'host_ip'              => 'HostIp',
        'ip_info_db'           => 'IpInfoDb',
        'google_maps'          => 'GoogleMaps',
        'google_maps_business' => 'GoogleMapsBusiness',
        'bing_maps'            => 'BingMaps',
        'openstreetmap'        => 'OpenStreetMap',
        'cloudmade'            => 'CloudMade',
        'geoip'                => 'Geoip',
        'map_quest'            => 'MapQuest',
        'oio_rest'             => 'OIORest',
        'geocoder_ca'          => 'GeocoderCa',
        'geocoder_us'          => 'GeocoderUs',
        'ign_openls'           => 'IGNOpenLS',
        'data_science_toolkit' => 'DataScienceToolkit',
        'yandex'               => 'Yandex',
        'geo_plugin'           => 'GeoPlugin',
        'geo_ips'              => 'GeoIPs',
        'maxmind'              => 'MaxMind',
        'geonames'             => 'Geonames',
        'ip_geo_base'          => 'IpGeoBase',
        'baidu'                => 'Baidu',
        'tomtom'               => 'TomTom',
        'arcgis_online'        => 'ArcGISOnline',
    ];

    /**
     * Available dumpers.
     *
     * @var array
     */
    private $dumpers = [
        'gpx'     => 'Gpx',
        'geojson' => 'GeoJson',
        'kml'     => 'Kml',
        'wkb'     => 'Wkb',
        'wkt'     => 'Wkt',
    ];


    /**
     * @param  string $factoryCallable
     *
     * @return CacheItemPoolInterface
     */
    protected function getCache($factoryCallable)
    {
        $factoryCallable = $this->lowerize((trim($factoryCallable)));
        if (!is_callable($factoryCallable)) {
            throw new \LogicException(sprintf('Cache must be called with a valid callable on the format "Example\Acme::create". "%s" given.', $factoryCallable));
        }

        $psr6 = call_user_func($factoryCallable);
        if (!$psr6 instanceof CacheItemPoolInterface) {
            throw new \LogicException('Return value of factory callable must be a CacheItemPoolInterface');
        }

        return $psr6;
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

        return '\\Geocoder\\Provider\\' . $provider . '\\' . $provider;
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
