Geotools
========

**Geotools** is a PHP geo-related library, built atop [Geocoder](https://github.com/willdurand/Geocoder) and
[React](https://github.com/reactphp/react) libraries.

[![Build Status](https://secure.travis-ci.org/toin0u/Geotools.png)](http://travis-ci.org/toin0u/Geotools)
[![project status](http://stillmaintained.com/toin0u/Geotools.png)](http://stillmaintained.com/toin0u/Geotools)


Features
--------

* Batch geocode & reverse geocoding requests in **serie** / in **parallel**.
* Calcul the distance in **meter** (by default), **km**  or **mile** between two coordinates using **flat**,
**haversine** or **vincenty** algorithms.
* Calcul the **initial bearing** from the origin coordinate to the destination coordinate in degrees.
* Calcul the **caridnal point** (direction) from the origin coordinate to the destination coordinate.
* Calcul the **half-way point** (coordinate) between the origin and the destination coordinates.
* Calcul the **destination point** with given bearing in degrees and a distance in meters.
* Encode a coordinate to a **geo hash** string and decode it to a coordinate.
* ... more to come ...


Installation
------------

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
Longitudes below -180.0 or abode 180.0 degrees are *wrapped* through `\Geotools\coordinaterdinate\Coordinate::normalizeLongitude()`.

``` php
<?php

$coordinate = new \Geotools\Coordinate\Coordinate($geocoderResult);
// or
$coordinate = new \Geotools\Coordinate\Coordinate(array(48.8234055, 2.3072664));
// or
$coordinate = new \Geotools\Coordinate\Coordinate('48.8234055, 2.3072664');
// result will be:
printf('Latitude: %F\n', $coordinate->getLatitude()); // 48.8234055
printf('Longitude: %F\n', $coordinate->getLongitude()); // 2.3072664
```

### Batch ###

It provides a very handy way to batch geocode and reverse geocoding in *serie* or in *parallel*.
Thanks to [Geocoder](https://github.com/willdurand/Geocoder) and [React](https://github.com/reactphp/react) libraries.

```php
<?php

$geocoder = new \Geocoder\Geocoder();
$adapter  = new \Geocoder\HttpAdapter\CurlHttpAdapter();

$geocoder->registerProviders(array(
    new \Geocoder\Provider\GoogleMapsProvider($adapter), // ok
    new \Geocoder\Provider\OpenStreetMapsProvider($adapter), // ok
    new \Geocoder\Provider\BingMapsProvider($adapter, '<FAKE_API_KEY>'), // throws InvalidCredentialsException
    new \Geocoder\Provider\YandexProvider($adapter), // ok
    new \Geocoder\Provider\FreeGeoIpProvider($adapter), // throws UnsupportedException
    new \Geocoder\Provider\HostIpProvider($adapter), // throws UnsupportedException
    new \Geocoder\Provider\GeoipProvider(), // throws UnsupportedException
));

$geotools = new \Geotools\Geotools();
$results  = $geotools->batch($geocoder)->geocode('10 rue Gambetta, Paris, France')->parallel();

$dumper = new \Geocoder\Dumper\WktDumper();
foreach ($results as $providerName => $providerResult) {
    // if a provider throws an exception (UnsupportedException, InvalidCredentialsException ...)
    // an empty /Geocoder/Result/Geocoded instance is returned. It's possible to use dumpers
    // and/or formatters from the Geocoder library
    printf("%s: %s\n", $providerName, $dumper->dump($providerResult));
}
```

You should get something like:

```
google_maps: POINT(2.307266 48.823405) // ok
openstreetmaps: POINT(2.391636 48.863936) // ok
bing_maps: POINT(0.000000 0.000000) // InvalidCredentialsException thrown
yandex: POINT(2.225684 48.874010) // ok
free_geo_ip: POINT(0.000000 0.000000) // UnsupportedException thrown
host_ip: POINT(0.000000 0.000000) // UnsupportedException thrown
geoip: POINT(0.000000 0.000000) // UnsupportedException thrown
```

Batch reverse geocoding is something like:

``` php
<?php

// ... like the previous exemple ...
$coordinate = new \Geotools\Coordinate\Coordinate(array(48.8234055, 2.3072664));
$results = $geotools->batch($geocoder)->reverse($coordinate)->parallel();
// ...
```

If you want to batch it in serie, replace the method `parallel()` to `serie()`.

### Distance ###

It provides tools to calculate the distance in *meter* (by default), *km* or *mile* between two coordinates
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

It provides methods to calculate the *initial bearing* in degrees, the *cardinal direction*, the *middle point*
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
