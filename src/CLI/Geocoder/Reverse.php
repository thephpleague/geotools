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
use Geocoder\Formatter\Formatter;
use League\Geotools\Coordinate\Coordinate;
use League\Geotools\CLI\Command;

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
            ->addOption('adapter', null, InputOption::VALUE_REQUIRED,
                'If set, the name of the adapter to use, cURL by default', 'curl')
            ->addOption('raw', null, InputOption::VALUE_NONE,
                'If set, the raw format of the reverse geocoding result')
            ->addOption('json', null, InputOption::VALUE_NONE,
                'If set, the json format of the reverse geocoding result')
            ->addOption('args', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'If set, the provider constructor arguments like api key, locale, region, ssl, toponym and service')
            ->addOption('format', null, InputOption::VALUE_REQUIRED,
                'If set, the format of the reverse geocoding result', '%S %n, %z %L')
            ->setHelp(<<<EOT
<info>Available adapters</info>:   {$this->getAdapters()}
<info>Available providers</info>:  {$this->getProviders()} <comment>(some providers need arguments)</comment>
<info>Available dumpers</info>:    {$this->getDumpers()}

<info>Use the default provider with the socket adapter and format the output</info>:

    %command.full_name% "48.8631507, 2.388911" <comment>--format="%L, %R, %C" --adapter=socket</comment>

<info>Use the OpenStreetMaps provider with the default adapter</info>:

    %command.full_name% "48.8631507, 2.388911" <comment>--provider=openstreetmaps</comment>
EOT
            );
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
            $geocoder->registerProvider(@new $provider(@new $adapter(), $args));
        } else {
            $geocoder->registerProvider(@new $provider(@new $adapter()));
        }

        $reversed = $geocoder->reverse($coordinate->getLatitude(), $coordinate->getLongitude());

        $formatter = new Formatter($reversed);

        if ($input->getOption('raw')) {
            $result[] = sprintf('<label>Adapter</label>:       <value>%s</value>', $adapter);
            $result[] = sprintf('<label>Provider</label>:      <value>%s</value>', $provider);
            if ($input->getOption('args')) {
                $result[] = sprintf('<label>Arguments</label>:     <value>%s</value>', $args);
            }
            $result[] = '---';
            $result[] = sprintf('<label>Latitude</label>:      <value>%s</value>', $reversed->getLatitude());
            $result[] = sprintf('<label>Longitude</label>:     <value>%s</value>', $reversed->getLongitude());
            if (null !== $bounds = $reversed->getBounds()) {
                $result[] = '<label>Bounds</label>';
                $result[] = sprintf(' - <label>South</label>: <value>%s</value>', $bounds['south']);
                $result[] = sprintf(' - <label>West</label>:  <value>%s</value>', $bounds['west']);
                $result[] = sprintf(' - <label>North</label>: <value>%s</value>', $bounds['north']);
                $result[] = sprintf(' - <label>East</label>:  <value>%s</value>', $bounds['east']);
            }
            $result[] = sprintf('<label>Street Number</label>: <value>%s</value>', $reversed->getStreetNumber());
            $result[] = sprintf('<label>Street Name</label>:   <value>%s</value>', $reversed->getStreetName());
            $result[] = sprintf('<label>Zipcode</label>:       <value>%s</value>', $reversed->getZipcode());
            $result[] = sprintf('<label>City</label>:          <value>%s</value>', $reversed->getCity());
            $result[] = sprintf('<label>City District</label>: <value>%s</value>', $reversed->getCityDistrict());
            $result[] = sprintf('<label>County</label>:        <value>%s</value>', $reversed->getCounty());
            $result[] = sprintf('<label>County Code</label>:   <value>%s</value>', $reversed->getCountyCode());
            $result[] = sprintf('<label>Region</label>:        <value>%s</value>', $reversed->getRegion());
            $result[] = sprintf('<label>Region Code</label>:   <value>%s</value>', $reversed->getRegionCode());
            $result[] = sprintf('<label>Country</label>:       <value>%s</value>', $reversed->getCountry());
            $result[] = sprintf('<label>Country Code</label>:  <value>%s</value>', $reversed->getCountryCode());
            $result[] = sprintf('<label>Timezone</label>:      <value>%s</value>', $reversed->getTimezone());
        } elseif ($input->getOption('json')) {
            $result = sprintf('<value>%s</value>', json_encode($reversed->toArray()));
        } else {
            $result = sprintf('<value>%s</value>', $formatter->format($input->getOption('format')));
        }

        $output->getFormatter()->setStyle('label', new OutputFormatterStyle('yellow', 'black'));
        $output->getFormatter()->setStyle('value', new OutputFormatterStyle('green', 'black'));
        $output->writeln($result);
    }
}
