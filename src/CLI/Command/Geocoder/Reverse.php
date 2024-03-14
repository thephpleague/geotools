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

use Geocoder\Formatter\StringFormatter;
use Geocoder\ProviderAggregator;
use Http\Discovery\HttpClientDiscovery;
use League\Geotools\Batch\Batch;
use League\Geotools\Coordinate\Coordinate;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command-line geocoder:reverse class
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
                'If set, the name of the provider to use, Google Maps by default', 'google_maps')
            ->addOption('cache', null, InputOption::VALUE_REQUIRED,
                'If set, the name of the cache to use, Redis by default')
            ->addOption('raw', null, InputOption::VALUE_NONE,
                'If set, the raw format of the reverse geocoding result')
            ->addOption('json', null, InputOption::VALUE_NONE,
                'If set, the json format of the reverse geocoding result')
            ->addOption('args', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'If set, the provider constructor arguments like api key, locale, region, ssl, toponym and service')
            ->addOption('format', null, InputOption::VALUE_REQUIRED,
                'If set, the format of the reverse geocoding result', '%S %n, %z %L')
            ->setHelp(<<<EOT
<info>Available providers</info>:  {$this->getProviders()} <comment>(some providers need arguments)</comment>
<info>Available dumpers</info>:    {$this->getDumpers()}

<info>Use the default provider with the socket adapter and format the output</info>:

    %command.full_name% "48.8631507, 2.388911" <comment>--format="%L, %R, %C" --adapter=socket</comment>

<info>Use the OpenStreetMap provider with the default adapter</info>:

    %command.full_name% "48.8631507, 2.388911" <comment>--provider=openstreetmap</comment>
EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $coordinate = new Coordinate($input->getArgument('coordinate'));

        $geocoder = new ProviderAggregator;
        $httpClient = HttpClientDiscovery::find();
        $provider = $this->getProvider($input->getOption('provider'));

        if ($input->getOption('args')) {
            $args = is_array($input->getOption('args'))
                ? implode(',', $input->getOption('args'))
                : $input->getOption('args');
            $geocoder->registerProvider(new $provider($httpClient, $args));
        } else {
            $geocoder->registerProvider(new $provider($httpClient));
        }

        $batch = new Batch($geocoder);
        if ($input->getOption('cache')) {
            $batch->setCache( $this->getCache($input->getOption('cache')));
        }

        $reversed = $batch->reverse($coordinate)->parallel();
        $address = $reversed[0]->first();

        if ($input->getOption('raw')) {
            $result = array();
            $result[] = sprintf('<label>HttpClient</label>:       <value>%s</value>', get_class($httpClient));
            $result[] = sprintf('<label>Provider</label>:      <value>%s</value>', $provider);
            $result[] = sprintf('<label>Cache</label>:         <value>%s</value>', isset($cache) ? $cache : 'None');
            if ($input->getOption('args')) {
                $result[] = sprintf('<label>Arguments</label>:     <value>%s</value>', $args);
            }
            $result[] = '---';
            $coordinates = $address->getCoordinates();
            $result[] = sprintf('<label>Latitude</label>:      <value>%s</value>', null !== $coordinates ? $coordinates->getLatitude() : '');
            $result[] = sprintf('<label>Longitude</label>:     <value>%s</value>', null !== $coordinates ? $coordinates->getLongitude() : '');
            if ($address->getBounds()) {
                $bounds = $address->getBounds()->toArray();
                $result[] = '<label>Bounds</label>';
                $result[] = sprintf(' - <label>South</label>: <value>%s</value>', $bounds['south']);
                $result[] = sprintf(' - <label>West</label>:  <value>%s</value>', $bounds['west']);
                $result[] = sprintf(' - <label>North</label>: <value>%s</value>', $bounds['north']);
                $result[] = sprintf(' - <label>East</label>:  <value>%s</value>', $bounds['east']);
            }
            $result[] = sprintf('<label>Street Number</label>: <value>%s</value>', $address->getStreetNumber());
            $result[] = sprintf('<label>Street Name</label>:   <value>%s</value>', $address->getStreetName());
            $result[] = sprintf('<label>Zipcode</label>:       <value>%s</value>', $address->getPostalCode());
            $result[] = sprintf('<label>City</label>:          <value>%s</value>', $address->getLocality());
            $result[] = sprintf('<label>City District</label>: <value>%s</value>', $address->getSubLocality());
            if ( NULL !== $adminLevels = $address->getAdminLevels() ) {
                $result[] = '<label>Admin Levels</label>';
                foreach ($adminLevels as $adminLevel) {
                    $result[] = sprintf(' - <label>%s</label>: <value>%s</value>', $adminLevel->getCode(), $adminLevel->getName());
                }
            }
            $country = $address->getCountry();
            $result[] = sprintf('<label>Country</label>:       <value>%s</value>', null !== $country ? $country->getName() : '');
            $result[] = sprintf('<label>Country Code</label>:  <value>%s</value>', null !== $country ? $country->getCode() : '');
            $result[] = sprintf('<label>Timezone</label>:      <value>%s</value>', $address->getTimezone());
        } elseif ($input->getOption('json')) {
            $result = sprintf('<value>%s</value>', json_encode($address->toArray()));
        } else {
            $result = (new StringFormatter)->format($address, $input->getOption('format'));
        }

        $output->writeln($result);
        return 0;
    }
}
