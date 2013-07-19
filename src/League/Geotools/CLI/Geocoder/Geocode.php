<?php

/**
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools\CLI\Geocoder;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Geocoder\Geocoder;
use League\Geotools\CLI\Command;

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
                'If set, the name of the provider to use, Google Maps by default', 'google_maps')
            ->addOption('adapter', null, InputOption::VALUE_REQUIRED,
                'If set, the name of the adapter to use, cURL by default', 'curl')
            ->addOption('raw', null, InputOption::VALUE_NONE,
                'If set, the raw format of the reverse geocoding result')
            ->addOption('json', null, InputOption::VALUE_NONE,
                'If set, the json format of the reverse geocoding result')
            ->addOption('dumper', null, InputOption::VALUE_REQUIRED,
                'If set, the name of the dumper to use, no dumper by default')
            ->addOption('args', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'If set, the provider constructor arguments like api key, locale, region, ssl, toponym and service')
            ->setHelp(<<<EOT
<info>Available adapters</info>:   {$this->getAdapters()}
<info>Available providers</info>:  {$this->getProviders()} <comment>(some providers need arguments)</comment>
<info>Available dumpers</info>:    {$this->getDumpers()}

<info>Use the default provider with the socket adapter and dump the output in WKT standard</info>:

    %command.full_name% paris <comment>--adapter=socket --dumper=wkt</comment>

<info>Use the OpenStreetMaps provider with the default adapter</info>:

    %command.full_name% paris <comment>--provider=openstreetmaps</comment>

<info>Use the FreeGeoIp provider with the socket adapter</info>

    %command.full_name% 74.200.247.59 <comment>--provider="free_geo_ip" --adapter="socket"</comment>

<info>Use the default provider with the french locale and region via SSL</info>:

    %command.full_name% "Tagensvej 47, Copenhagen" <comment>--args=da_DK --args=Denmark --args="true"</comment>
EOT
            );
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
            $geocoder->registerProvider(@new $provider(@new $adapter(), $args));
        } else {
            $geocoder->registerProvider(@new $provider(@new $adapter()));
        }

        $geocoded = $geocoder->geocode($input->getArgument('value'));

        if ($input->getOption('raw')) {
            $result[] = sprintf('<label>Adapter</label>:       <value>%s</value>', $adapter);
            $result[] = sprintf('<label>Provider</label>:      <value>%s</value>', $provider);
            if ($input->getOption('args')) {
                $result[] = sprintf('<label>Arguments</label>:     <value>%s</value>', $args);
            }
            $result[] = '---';
            $result[] = sprintf('<label>Latitude</label>:      <value>%s</value>', $geocoded->getLatitude());
            $result[] = sprintf('<label>Longitude</label>:     <value>%s</value>', $geocoded->getLongitude());
            if (null !== $bounds = $geocoded->getBounds()) {
                $result[] = '<label>Bounds</label>';
                $result[] = sprintf(' - <label>South</label>: <value>%s</value>', $bounds['south']);
                $result[] = sprintf(' - <label>West</label>:  <value>%s</value>', $bounds['west']);
                $result[] = sprintf(' - <label>North</label>: <value>%s</value>', $bounds['north']);
                $result[] = sprintf(' - <label>East</label>:  <value>%s</value>', $bounds['east']);
            }
            $result[] = sprintf('<label>Street Number</label>: <value>%s</value>', $geocoded->getStreetNumber());
            $result[] = sprintf('<label>Street Name</label>:   <value>%s</value>', $geocoded->getStreetName());
            $result[] = sprintf('<label>Zipcode</label>:       <value>%s</value>', $geocoded->getZipcode());
            $result[] = sprintf('<label>City</label>:          <value>%s</value>', $geocoded->getCity());
            $result[] = sprintf('<label>City District</label>: <value>%s</value>', $geocoded->getCityDistrict());
            $result[] = sprintf('<label>County</label>:        <value>%s</value>', $geocoded->getCounty());
            $result[] = sprintf('<label>County Code</label>:   <value>%s</value>', $geocoded->getCountyCode());
            $result[] = sprintf('<label>Region</label>:        <value>%s</value>', $geocoded->getRegion());
            $result[] = sprintf('<label>Region Code</label>:   <value>%s</value>', $geocoded->getRegionCode());
            $result[] = sprintf('<label>Country</label>:       <value>%s</value>', $geocoded->getCountry());
            $result[] = sprintf('<label>Country Code</label>:  <value>%s</value>', $geocoded->getCountryCode());
            $result[] = sprintf('<label>Timezone</label>:      <value>%s</value>', $geocoded->getTimezone());
        } elseif ($input->getOption('json')) {
            $result = sprintf('<value>%s</value>', json_encode($geocoded->toArray()));
        } elseif ($input->getOption('dumper')) {
            $dumper = $this->getDumper($input->getOption('dumper'));
            $dumper = new $dumper();
            $result = sprintf('<value>%s</value>', $dumper->dump($geocoded));
        } else {
            $result = sprintf('<value>%s, %s</value>', $geocoded->getLatitude(), $geocoded->getLongitude());
        }

        $output->getFormatter()->setStyle('label', new OutputFormatterStyle('yellow', 'black'));
        $output->getFormatter()->setStyle('value', new OutputFormatterStyle('green', 'black'));
        $output->writeln($result);
    }
}
