Geotools
========

**Geotools** is a PHP geo-related library, built atop [Geocoder](https://github.com/willdurand/Geocoder) and
[React](https://github.com/reactphp/react) libraries.

[![Build Status](https://secure.travis-ci.org/toin0u/Geotools.png)](http://travis-ci.org/toin0u/Geotools)
[![project status](http://stillmaintained.com/toin0u/Geotools.png)](http://stillmaintained.com/toin0u/Geotools)


Features
--------

* **Batch** geocode & reverse geocoding request(s) in **serie** / in **parallel** against one or a **set of providers**.
* Compute the distance in **meter** (by default), **km**  or **mile** between two coordinates using **flat**,
**haversine** or **vincenty** algorithms.
* Compute the **initial bearing** from the origin coordinate to the destination coordinate in degrees.
* Compute the **cardinal point** (direction) from the origin coordinate to the destination coordinate.
* Compute the **half-way point** (coordinate) between the origin and the destination coordinates.
* Compute the **destination point** with given bearing in degrees and a distance in meters.
* Encode a coordinate to a **geo hash** string and decode it to a coordinate, read more in
[wikipedia](http://en.wikipedia.org/wiki/Geohash) and on [geohash.org](http://geohash.org/).
* ... more to come ...


Installation
------------

Geotools can be found on [Packagist](https://packagist.org/packages/toin0u/geotools).
The recommended way to install Geotools is through [composer](http://getcomposer.org).

Run these commands to install composer, Geotools and its dependencies:

``` bash
% wget http://getcomposer.org/composer.phar
% php composer.phar init --require="toin0u/geotools:*" -n
% php composer.phar install
```

Now you can add the autoloader, and you will have access to the library:

``` php
<?php

require 'vendor/autoload.php';
```

If you don't use neither **Composer** nor a _ClassLoader_ in your application, just require the provided autoloader:

``` php
<?php

require_once 'src/autoload.php';
```


Usage & API
-----------

### Coordinate ###

**Geotools** is built atop [Geocoder](https://github.com/willdurand/Geocoder). It means it's possible to use the
`\Geocoder\Result\ResultInterface` directly but it's also possible to use a *string* or a simple *array* with its
latitude and longitude.

Latitudes below -90.0 or above 90.0 degrees are *capped* through `\Geotools\Coordinate\Coordinate::normalizeLatitude()`.  
Longitudes below -180.0 or abode 180.0 degrees are *wrapped* through `\Geotools\Coordinate\Coordinate::normalizeLongitude()`.

``` php
<?php

$coordinate = new \Geotools\Coordinate\Coordinate($geocoderResult); // \Geocoder\Result\ResultInterface
// or
$coordinate = new \Geotools\Coordinate\Coordinate(array(48.8234055, 2.3072664));
// or
$coordinate = new \Geotools\Coordinate\Coordinate('48.8234055, 2.3072664');
// the result will be:
printf('Latitude: %F\n', $coordinate->getLatitude()); // 48.8234055
printf('Longitude: %F\n', $coordinate->getLongitude()); // 2.3072664
```

### Batch ###

It provides a very handy way to batch geocode and reverse geocoding requests in *serie* or in *parallel* against
a set of providers.  
Thanks to [Geocoder](https://github.com/willdurand/Geocoder) and [React](https://github.com/reactphp/react) libraries.

It's possible to batch *one request* (a string) or a *set of request* (an array) against *one provider* or
*set of providers*.

```php
<?php

$geocoder = new \Geocoder\Geocoder();
$adapter  = new \Geocoder\HttpAdapter\CurlHttpAdapter();

$geocoder->registerProviders(array(
    new \Geocoder\Provider\GoogleMapsProvider($adapter),
    new \Geocoder\Provider\OpenStreetMapsProvider($adapter),
    new \Geocoder\Provider\BingMapsProvider($adapter, '<FAKE_API_KEY>'), // throws InvalidCredentialsException
    new \Geocoder\Provider\YandexProvider($adapter),
    new \Geocoder\Provider\FreeGeoIpProvider($adapter),
    new \Geocoder\Provider\GeoipProvider(),
));

$geotools = new \Geotools\Geotools();
$results  = $geotools->batch($geocoder)->geocode(array(
    'Paris, France',
    'Copenhagen, Denmark',
    '74.200.247.59',
    '::ffff:66.147.244.214'
))->parallel();

$dumper = new \Geocoder\Dumper\WktDumper();
foreach ($results as $result) {
    // if a provider throws an exception (UnsupportedException, InvalidCredentialsException ...)
    // an empty /Geocoder/Result/Geocoded instance is returned. It's possible to use dumpers
    // and/or formatters from the Geocoder library
    printf("%s\n", $dumper->dump($result));
}
```

You should get 24 results something like (6 providers against 4 values to geocode):

```
POINT(2.352222 48.856614) // GoogleMapsProvider, OK! Address-based supported
POINT(12.568337 55.676097) // GoogleMapsProvider, OK! Address-based supported
POINT(0.000000 0.000000) // GoogleMapsProvider, IPv4 UnsupportedException thrown
POINT(0.000000 0.000000) // GoogleMapsProvider, IPv6 UnsupportedException thrown
POINT(2.320035 48.858841) // OpenStreetMapsProvider, OK! Address-based supported
POINT(12.570069 55.686724) // OpenStreetMapsProvider, OK! Address-based supported
POINT(0.000000 0.000000) // OpenStreetMapsProvider, IPv4 UnsupportedException thrown
POINT(0.000000 0.000000) // OpenStreetMapsProvider, IPv6 UnsupportedException thrown
POINT(0.000000 0.000000) // BingMapsProvider, InvalidCredentialsException thrown
POINT(0.000000 0.000000) // BingMapsProvider, InvalidCredentialsException thrown
POINT(0.000000 0.000000) // BingMapsProvider, InvalidCredentialsException thrown
POINT(0.000000 0.000000) // BingMapsProvider, InvalidCredentialsException thrown
POINT(2.341198 48.856929) // YandexProvider, OK! Address-based supported
POINT(12.567602 55.675682) // YandexProvider, OK! Address-based supported
POINT(0.000000 0.000000) // YandexProvider, IPv4 UnsupportedException thrown
POINT(0.000000 0.000000) // YandexProvider, IPv6 UnsupportedException thrown
POINT(0.000000 0.000000) // FreeGeoIpProvider, Address-based UnsupportedException thrown
POINT(0.000000 0.000000) // FreeGeoIpProvider, Address-based UnsupportedException thrown
POINT(-122.415600 37.748400) // FreeGeoIpProvider, OK! IPv4 supported
POINT(-111.613300 40.218100) // FreeGeoIpProvider, OK! IPv6 supported
POINT(0.000000 0.000000) // GeoipProvider, Address-based UnsupportedException thrown
POINT(0.000000 0.000000) // GeoipProvider, Address-based UnsupportedException thrown
POINT(-122.415604 37.748402) // GeoipProvider, OK! IPv4 supported
POINT(0.000000 0.000000) // GeoipProvider, NoResultException thrown but IPv6 is supported
```

Batch reverse geocoding is something like:

``` php
<?php

// ... $geocoder like the previous example ...
// If you want to reverse one coordinate
$results = $geotools->batch($geocoder)->reverse(
    new \Geotools\Coordinate\Coordinate(array(2.307266 48.823405))
)->parallel();
// Or if you want to reverse geocoding 3 coordinates
$coordinates = array(
    new \Geotools\Coordinate\Coordinate(array(2.307266 48.823405)),
    new \Geotools\Coordinate\Coordinate(array(12.568337 55.676097)),
    new \Geotools\Coordinate\Coordinate('-74.005973 40.714353')),
);
$results = $geotools->batch($geocoder)->reverse($coordinates)->parallel();
// ...
```

If you want to batch it in serie, replace the method `parallel()` to `serie()`.

To optimize batch requests you need to register providers according to their **capabilities** and what you're
**looking for** (geocode street addresses, geocode IPv4, geocode IPv6 or reverse geocoding),
please read more at the [Geocoder library doc](https://github.com/willdurand/Geocoder#freegeoipprovider).

### Distance ###

It provides methods to compute the distance in *meter* (by default), *km* or *mile* between two coordinates
using *flat* (most performant), *haversine* or *vincenty* (most accurate) algorithms.

``` php
<?php

$geotools = new \Geotools\Geotools();
$coordA   = new \Geotools\Coordinate\Coordinate(array(48.8234055, 2.3072664));
$coordB   = new \Geotools\Coordinate\Coordinate(array(43.296482, 5.36978));

echo $geotools->from($coordA)->to($coordB)->distance()->flat(); // 661220.36979254 (meters)
echo $geotools->from($coordA)->to($coordB)->distance()->in('km')->haversine(); // 659.16650524477
echo $geotools->from($coordA)->to($coordB)->distance()->in('mile')->vincenty(); // 410.41281759044
```

### Point ###

It provides methods to compute the *initial bearing* in degrees, the *cardinal direction*, the *middle point*
and the *destination point*. The middle and the destination points returns a `\Geotools\Coordinate\Coordinate` object.

``` php
<?php

$geotools = new \Geotools\Geotools();
$coordA   = new \Geotools\Coordinate\Coordinate(array(48.8234055, 2.3072664));
$coordB   = new \Geotools\Coordinate\Coordinate(array(43.296482, 5.36978));

echo $geotools->from($coordA)->to($coordB)->point()->bearing(); // 157 (degrees)
echo $geotools->from($coordA)->to($coordB)->point()->cardinal(); // SSE (SouthSouthEast)

$middlePoint = $geotools->from($coordA)->to($coordB)->point()->middle(); // \Geotools\Coordinate\Coordinate
echo $middlePoint->getLatitude(); // 46.070143125815
echo $middlePoint->getLongitude(); // 3.9152401085931

$destinationPoint = $geotools->from($coordA)->point()->destination(180, 200000); // \Geotools\Coordinate\Coordinate
echo $destinationPoint->getLatitude(); // 47.026774663314
echo $destinationPoint->getLongitude(); // 2.3072664
```


### Geohash ###

It provides methods to get the *geo hash* and its *bounding box's coordinates* of a coordinate and the
*coordinate* and its *bounding box's coordinates* of a geo hash.

``` php
<?php

$geotools       = new \Geotools\Geotools();
$coordToGeohash = new \Geotools\Coordinate\Coordinate('43.296482, 5.36978');

// encoding
$geotools->geohash()->encode($coordToGeohash, 3)->getGeohash(); // spe
$encoded = $geotools->geohash()->encode($coordToGeohash); // 12 is the default length
echo $encoded->getGeohash(); // spey61yhkcnp
$boundingBox = $encoded->getBoundingBox(); // returns an array of \Geotools\Coordinate\CordinateInterface

// decoding
$decoded = $geotools->geohash()->decode('spey61y');
echo $decoded->getCoordinate()->getLatitude(); // 43.296432495117
echo $decoded->getCoordinate()->getLongitude(); // 5.3702545166016
$boundingBox = $decoded->getBoundingBox(); // returns an array of \Geotools\Coordinate\CordinateInterface
```


Unit Tests
----------

Rename the `phpunit.xml.dist` file to `phpunit.xml`.

``` bash
% php composer.phar install --dev
```

Once installed, just launch the following command:

``` bash
% phpunit
```


Contributing
------------

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.


Credits
-------

* Antoine Corcy <contact@sbin.dk>
* [All contributors](https://github.com/toin0u/Geotools/contributors)
* **Geotools** is influenced by [Geokit](https://github.com/jsor/Geokit),
[Geotools-for-CodeIgniter](https://github.com/weejames/Geotools-for-CodeIgniter),
[geotools-php](https://github.com/jillesvangurp/geotools-php) and so on...


Changelog
---------

[See the changelog file](CHANGELOG.md)


Support
-------

[Please open an issues in github](https://github.com/toin0u/Geotools/issues)


License
-------

Geotools is released under the MIT License. See the bundled [LICENSE](LICENSE) file for details.
