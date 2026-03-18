Geotools
========

**Geotools** is a PHP geo-related library, built atop [Geocoder](https://github.com/geocoder-php/Geocoder) and
[React](https://github.com/reactphp/react) libraries.

[![Latest Version](https://poser.pugx.org/league/geotools/v/stable)](https://github.com/thephpleague/geotools/releases)
[![Total Downloads](https://poser.pugx.org/league/geotools/downloads)](https://packagist.org/packages/league/geotools)
[![CI](https://github.com/thephpleague/geotools/actions/workflows/ci.yml/badge.svg)](https://github.com/thephpleague/geotools/actions/workflows/ci.yml)
[![Quality Score](https://img.shields.io/scrutinizer/g/thephpleague/geotools.svg?style=flat-square)](https://scrutinizer-ci.com/g/thephpleague/geotools/?branch=master)

Features
--------

* **Batch** geocode & reverse geocoding request(s) in **series** / in **parallel** against one or a
**set of providers**. [»](#batch)
* **Cache** geocode & reverse geocoding result(s) with **PSR-6** to improve performances. [»](#batch)
* Compute geocode & reverse geocoding in the **command-line interface** (CLI) + dumpers and formatters. [»](#cli)
* Accept **almost** all kind of WGS84
[geographic coordinates](http://en.wikipedia.org/wiki/Geographic_coordinate_conversion) as coordinates.
[»](#coordinate--ellipsoid)
* Support **23 different ellipsoids** and it's easy to provide a new one if needed. [»](#coordinate--ellipsoid)
* **Convert** and **format** decimal degrees coordinates to decimal minutes or degrees minutes seconds coordinates.
[»](#convert)
* **Convert** decimal degrees coordinates in the
[Universal Transverse Mercator](http://en.wikipedia.org/wiki/Universal_Transverse_Mercator_coordinate_system)
(UTM) projection. [»](#convert)
* Compute the distance in **meter** (by default), **km**, **mi** or **ft** between two coordinates using **flat**,
**great circle**, **haversine** or **vincenty** algorithms. [»](#distance)
* Compute the initial and final **bearing** from the origin coordinate to the destination coordinate in degrees.
[»](#point)
* Compute the initial and final **cardinal point** (direction) from the origin coordinate to the destination
coordinate, read more in [wikipedia](http://en.wikipedia.org/wiki/Cardinal_direction). [»](#point)
* Compute the **half-way point** (coordinate) between the origin and the destination coordinates. [»](#point)
* Compute the **destination point** (coordinate) with given bearing in degrees and a distance in meters. [»](#point)
* Encode a coordinate to a **geo hash** string and decode it to a coordinate, read more in
[wikipedia](http://en.wikipedia.org/wiki/Geohash) and on [geohash.org](http://geohash.org/). [»](#geohash)
* Encode a coordinate via the 10:10 algorithm. [»](#1010)
* **Polygon** class provides methods to check either a point (coordinate) is in, or on the polygon's boundaries.
[»](#polygon)
* A **command-line interface** (CLI) for **Distance**, **Point**, **Geohash** and **Convert** classes. [»](#cli)
* ... more to come ...


Installation
------------

**Geotools** can be found on [Packagist](https://packagist.org/packages/league/geotools).
The recommended way to install **Geotools** is through [composer](http://getcomposer.org).

Run the following on the command line:

```bash
composer require league/geotools
```

**Requirements:** PHP 8.2 or higher.

Now you can add the autoloader, and you will have access to the library:

```php
<?php

require 'vendor/autoload.php';
```


Usage & API
-----------

## Coordinate & Ellipsoid

The default geodetic datum is [WGS84](http://en.wikipedia.org/wiki/World_Geodetic_System) and coordinates are in
decimal degrees.

Here are the available ellipsoids: `AIRY`, `AUSTRALIAN_NATIONAL`, `BESSEL_1841`, `BESSEL_1841_NAMBIA`,
`CLARKE_1866`, `CLARKE_1880`, `EVEREST`, `FISCHER_1960_MERCURY`, `FISCHER_1968`, `GRS_1967`, `GRS_1980`,
`HELMERT_1906`, `HOUGH`, `INTERNATIONAL`, `KRASSOVSKY`, `MODIFIED_AIRY`, `MODIFIED_EVEREST`,
`MODIFIED_FISCHER_1960`, `SOUTH_AMERICAN_1969`, `WGS60`, `WGS66`, `WGS72`, and `WGS84`.

You can reference ellipsoids either by their string name or via the `EllipsoidName` enum (PHP 8.1+):

```php
<?php

use League\Geotools\Coordinate\Ellipsoid;
use League\Geotools\Coordinate\EllipsoidName;

// Using the enum (recommended)
$ellipsoid = Ellipsoid::createFromName(EllipsoidName::WGS84);

// Using a string constant (still supported)
$ellipsoid = Ellipsoid::createFromName(Ellipsoid::WGS84);
```

If you need to use a custom ellipsoid, create an array like this:

```php
<?php

$myEllipsoid = \League\Geotools\Coordinate\Ellipsoid::createFromArray([
    'name' => 'My Ellipsoid', // The name of the Ellipsoid
    'a'    => 123.0,          // The semi-major axis (equatorial radius) in meters
    'invF' => 456.0           // The inverse flattening
]);
```

**Geotools** is built atop [Geocoder](https://github.com/geocoder-php/Geocoder). It means it's possible to use a
`\Geocoder\Model\Address` directly but it's also possible to use a *string* or a simple *array* with its
latitude and longitude.

It supports [valid and acceptable geographic coordinates](http://en.wikipedia.org/wiki/Geographic_coordinate_conversion)
like:
* 40:26:46N,079:56:55W
* 40:26:46.302N 079:56:55.903W
* 40°26′47″N 079°58′36″W
* 40d 26′ 47″ N 079d 58′ 36″ W
* 40.446195N 79.948862W
* 40.446195, -79.948862
* 40° 26.7717, -79° 56.93172

Latitudes below -90.0 or above 90.0 degrees are *capped* through `\League\Geotools\Coordinate\Coordinate::normalizeLatitude()`.
Longitudes below -180.0 or above 180.0 degrees are *wrapped* through `\League\Geotools\Coordinate\Coordinate::normalizeLongitude()`.

```php
<?php

use League\Geotools\Coordinate\Coordinate;
use League\Geotools\Coordinate\Ellipsoid;
use League\Geotools\Coordinate\EllipsoidName;

// from a \Geocoder\Model\Address instance within Airy ellipsoid
$coordinate = new Coordinate($geocoderResult, Ellipsoid::createFromName(EllipsoidName::AIRY));
// or in an array of latitude/longitude coordinate within GRS 1980 ellipsoid
$coordinate = new Coordinate([48.8234055, 2.3072664], Ellipsoid::createFromName(EllipsoidName::GRS_1980));
// or in latitude/longitude coordinate within WGS84 ellipsoid
$coordinate = new Coordinate('48.8234055, 2.3072664');
// or in degrees minutes seconds coordinate within WGS84 ellipsoid
$coordinate = new Coordinate('48°49′24″N, 2°18′26″E');
// or in decimal minutes coordinate within WGS84 ellipsoid
$coordinate = new Coordinate('48 49.4N, 2 18.43333E');
// the result will be:
printf("Latitude: %F\n", $coordinate->getLatitude());   // 48.8234055
printf("Longitude: %F\n", $coordinate->getLongitude()); // 2.3072664
printf("Ellipsoid name: %s\n", $coordinate->getEllipsoid()->getName()); // WGS 84
printf("Equatorial radius: %F\n", $coordinate->getEllipsoid()->getA());  // 6378137.0
printf("Polar distance: %F\n", $coordinate->getEllipsoid()->getB());     // 6356752.314245
printf("Inverse flattening: %F\n", $coordinate->getEllipsoid()->getInvF()); // 298.257224
printf("Mean radius: %F\n", $coordinate->getEllipsoid()->getArithmeticMeanRadius()); // 6371008.771415
// it's also possible to modify the coordinate without creating another coordinate
$coordinate->setFromString('40°26′47″N 079°58′36″W');
printf("Latitude: %F\n", $coordinate->getLatitude());   // 40.446388888889
printf("Longitude: %F\n", $coordinate->getLongitude()); // -79.976666666667
```


## Convert

It provides methods to convert *decimal degrees* WGS84 coordinates to *degrees minutes seconds*
or *decimal minutes* WGS84 coordinates. You can format the output string easily.

You can also convert them in the Universal Transverse Mercator (UTM) projection (Southwest coast of Norway and the
region of Svalbard are covered).

```php
<?php

$geotools   = new \League\Geotools\Geotools();
$coordinate = new \League\Geotools\Coordinate\Coordinate('40.446195, -79.948862');
$converted  = $geotools->convert($coordinate);
// convert to decimal minutes without and with format string
printf("%s\n", $converted->toDecimalMinutes()); // 40 26.7717N, -79 56.93172W
// convert to degrees minutes seconds without and with format string
printf("%s\n", $converted->toDegreesMinutesSeconds('<p>%P%D:%M:%S, %p%d:%m:%s</p>')); // <p>40:26:46, -79:56:56</p>
// convert in the UTM projection (standard format)
printf("%s\n", $converted->toUniversalTransverseMercator()); // 17T 589138 4477813
```

Here is the mapping:

**Decimal minutes** | Latitude | Longitude
--- | --- | ---
Positive or negative sign | `%P` | `%p`
Direction | `%L` | `%l`
Degrees | `%D` | `%d`
Decimal minutes | `%N` | `%n`

**Degrees minutes seconds** | Latitude | Longitude
--- | --- | ---
Positive or negative sign | `%P` | `%p`
Direction | `%L` | `%l`
Degrees | `%D` | `%d`
Minutes | `%M` | `%m`
Seconds | `%S` | `%s`

## Batch

It provides a very handy way to batch geocode and reverse geocoding requests in *serie* or in *parallel* against
a set of providers.
Thanks to [Geocoder](https://github.com/geocoder-php/Geocoder) and [React](https://github.com/reactphp/react) libraries.

It's possible to batch *one request* (a string) or a *set of requests* (an array) against *one provider* or a
*set of providers*.

**Cache:** any [PSR-6](https://www.php-fig.org/psr/psr-6/) compatible cache pool is supported (e.g.
`symfony/cache`, `cache/array-adapter`, etc.).

> **Note:** Before implementing caching in your app, please be sure that doing so does not violate the Terms of
> Service of your geocoding provider(s).

```php
<?php

use Http\Discovery\HttpClientDiscovery;
use Psr\Cache\CacheItemPoolInterface;

$geocoder = new \Geocoder\ProviderAggregator();
$httpClient = HttpClientDiscovery::find();

$geocoder->registerProviders([
    new \Geocoder\Provider\GoogleMaps\GoogleMaps($httpClient),
    new \Geocoder\Provider\Nominatim\Nominatim($httpClient, 'https://nominatim.openstreetmap.org', 'my-app'),
]);

// Use any PSR-6 cache implementation, e.g. Symfony ArrayAdapter:
// $cache = new \Symfony\Component\Cache\Adapter\ArrayAdapter();

try {
    $geotools = new \League\Geotools\Geotools();

    $results = $geotools->batch($geocoder)->geocode([
        'Paris, France',
        'Copenhagen, Denmark',
    ])->parallel();
} catch (\Exception $e) {
    die($e->getMessage());
}

$dumper = new \Geocoder\Dumper\WktDumper();
foreach ($results as $result) {
    // if a provider throws an exception, a BatchGeocoded instance is returned
    // which embeds the provider name, the query and the exception message.
    printf("%s|%s|%s\n",
        $result->getProviderName(),
        $result->getQuery(),
        '' === $result->getExceptionMessage() ? $dumper->dump($result) : $result->getExceptionMessage()
    );
}
```

Batch reverse geocoding:

```php
<?php

// If you want to reverse one coordinate
try {
    $results = $geotools->batch($geocoder)->reverse(
        new \League\Geotools\Coordinate\Coordinate([2.307266, 48.823405])
    )->parallel();
} catch (\Exception $e) {
    die($e->getMessage());
}

// Or reverse geocode multiple coordinates
$coordinates = [
    new \League\Geotools\Coordinate\Coordinate([2.307266, 48.823405]),
    new \League\Geotools\Coordinate\Coordinate([12.568337, 55.676097]),
    new \League\Geotools\Coordinate\Coordinate('-74.005973 40.714353'),
];
$results = $geotools->batch($geocoder)->reverse($coordinates)->parallel();
```

If you want to batch in serie, replace `parallel()` with `serie()`.

## Distance

It provides methods to compute the distance in *meter* (by default), *km*, *mi* or *ft* between two coordinates
using *flat* (most performant), *great circle*, *haversine* or *vincenty* (most accurate) algorithms.

Those coordinates should be in the same ellipsoid.

```php
<?php

$geotools = new \League\Geotools\Geotools();
$coordA   = new \League\Geotools\Coordinate\Coordinate([48.8234055, 2.3072664]);
$coordB   = new \League\Geotools\Coordinate\Coordinate([43.296482, 5.36978]);
$distance = $geotools->distance()->setFrom($coordA)->setTo($coordB);

printf("%s\n", $distance->flat());               // 659166.50038742 (meters)
printf("%s\n", $distance->greatCircle());        // 659021.90812846
printf("%s\n", $distance->in('km')->haversine()); // 659.02190812846
printf("%s\n", $distance->in('mi')->vincenty());  // 409.05330679648
printf("%s\n", $distance->in('ft')->flat());      // 2162619.7519272
```

## Point

It provides methods to compute the initial and final *bearing* in degrees, the initial and final *cardinal direction*,
the *middle point* and the *destination point*. The middle and the destination points return a
`\League\Geotools\Coordinate\Coordinate` object with the same ellipsoid.

```php
<?php

$geotools = new \League\Geotools\Geotools();
$coordA   = new \League\Geotools\Coordinate\Coordinate([48.8234055, 2.3072664]);
$coordB   = new \League\Geotools\Coordinate\Coordinate([43.296482, 5.36978]);
$vertex   = $geotools->vertex()->setFrom($coordA)->setTo($coordB);

printf("%d\n", $vertex->initialBearing()); // 157 (degrees)
printf("%s\n", $vertex->initialCardinal()); // SSE (SouthSouthEast)
printf("%d\n", $vertex->finalBearing());   // 160 (degrees)
printf("%s\n", $vertex->finalCardinal());  // SSE (SouthSouthEast)

$middlePoint = $vertex->middle(); // \League\Geotools\Coordinate\Coordinate
printf("%s\n", $middlePoint->getLatitude());  // 46.070143125815
printf("%s\n", $middlePoint->getLongitude()); // 3.9152401085931

$destinationPoint = $geotools->vertex()->setFrom($coordA)->destination(180, 200000); // \League\Geotools\Coordinate\Coordinate
printf("%s\n", $destinationPoint->getLatitude());  // 47.026774650075
printf("%s\n", $destinationPoint->getLongitude()); // 2.3072664
```

## Geohash

It provides methods to get the *geo hash* and its *bounding box's coordinates* (SouthWest & NorthEast)
of a coordinate and the *coordinate* and its *bounding box's coordinates* (SouthWest & NorthEast) of a geo hash.

```php
<?php

$geotools       = new \League\Geotools\Geotools();
$coordToGeohash = new \League\Geotools\Coordinate\Coordinate('43.296482, 5.36978');

// encoding
$encoded = $geotools->geohash()->encode($coordToGeohash, 4); // 12 is the default length / precision
printf("%s\n", $encoded->getGeohash()); // spey
// encoded bounding box
$boundingBox = $encoded->getBoundingBox(); // array of \League\Geotools\Coordinate\CoordinateInterface
$southWest   = $boundingBox[0];
$northEast   = $boundingBox[1];
printf("http://www.openstreetmap.org/?minlon=%s&minlat=%s&maxlon=%s&maxlat=%s&box=yes\n",
    $southWest->getLongitude(), $southWest->getLatitude(),
    $northEast->getLongitude(), $northEast->getLatitude()
); // http://www.openstreetmap.org/?minlon=5.2734375&minlat=43.2421875&maxlon=5.625&maxlat=43.41796875&box=yes

// decoding
$decoded = $geotools->geohash()->decode('spey61y');
printf("%s\n", $decoded->getCoordinate()->getLatitude());  // 43.296432495117
printf("%s\n", $decoded->getCoordinate()->getLongitude()); // 5.3702545166016
// decoded bounding box
$boundingBox = $decoded->getBoundingBox(); // array of \League\Geotools\Coordinate\CoordinateInterface
$southWest   = $boundingBox[0];
$northEast   = $boundingBox[1];
printf("http://www.openstreetmap.org/?minlon=%s&minlat=%s&maxlon=%s&maxlat=%s&box=yes\n",
    $southWest->getLongitude(), $southWest->getLatitude(),
    $northEast->getLongitude(), $northEast->getLatitude()
);
```

You can also get information about neighbor geohash points:

```php
<?php

$geotools = new \League\Geotools\Geotools();
$decoded  = $geotools->geohash()->decode('spey61y');

printf("%s\n", $decoded->getNeighbor(\League\Geotools\Geohash\Geohash::DIRECTION_NORTH));      // spey64n
printf("%s\n", $decoded->getNeighbor(\League\Geotools\Geohash\Geohash::DIRECTION_SOUTH_EAST)); // spey61x

// get all neighbor geohashes
print_r($decoded->getNeighbors(true));
/**
 * Array
 * (
 *     [north]      => spey64n
 *     [south]      => spey61w
 *     [west]       => spey61v
 *     [east]       => spey61z
 *     [north_west] => spey64j
 *     [north_east] => spey64p
 *     [south_west] => spey61t
 *     [south_east] => spey61x
 * )
 */
```

## 10:10

Represent a location with 10m accuracy using a 10 character code that includes features to prevent errors in
entering the code. Read more about the algorithm [here](http://blog.jgc.org/2006/07/simple-code-for-entering-latitude-and.html).

```php
<?php

$tenten = new \League\Geotools\Geohash\TenTen;
$tenten->encode(new \League\Geotools\Coordinate\Coordinate([51.09559, 1.12207])); // MEQ N6G 7NY5
```

## Vertex

Represents a segment with a direction. You can find if two vertices are on the same line.

```php
<?php

use League\Geotools\Coordinate\Coordinate;
use League\Geotools\Vertex\Vertex;

$vertexA = new Vertex();
$vertexA->setFrom(new Coordinate([48.8234055, 2.3072664]));
$vertexA->setTo(new Coordinate([43.296482, 5.36978]));

$vertexB = new Vertex();
$vertexB->setFrom(new Coordinate([48.8234055, 2.3072664]));
$vertexB->setTo(new Coordinate([43.296482, 5.36978]));

$vertexA->isOnSameLine($vertexB); // true
```

## Polygon

It helps you to know if a point (coordinate) is in a Polygon or on the Polygon's boundaries and if it is on
a Polygon's vertex.

First you need to create the polygon, you can provide:
- an array of arrays
- an array of `Coordinate`
- a `CoordinateCollection`

```php
<?php

$polygon = new \League\Geotools\Polygon\Polygon([
    [48.9675969, 1.7440796],
    [48.4711003, 2.5268555],
    [48.9279131, 3.1448364],
    [49.3895245, 2.6119995],
]);

$polygon->setPrecision(5); // set the comparison precision
$polygon->pointInPolygon(new \League\Geotools\Coordinate\Coordinate([49.1785607, 2.4444580])); // true
$polygon->pointInPolygon(new \League\Geotools\Coordinate\Coordinate([49.1785607, 5]));         // false
$polygon->pointOnBoundary(new \League\Geotools\Coordinate\Coordinate([48.7193486, 2.13546755])); // true
$polygon->pointOnBoundary(new \League\Geotools\Coordinate\Coordinate([47.1587188, 2.87841795])); // false
$polygon->pointOnVertex(new \League\Geotools\Coordinate\Coordinate([48.4711003, 2.5268555])); // true
$polygon->pointOnVertex(new \League\Geotools\Coordinate\Coordinate([49.1785607, 2.4444580])); // false
$polygon->getBoundingBox(); // returns the BoundingBox object
```

## CLI

It provides command lines to compute methods provided by **Distance**, **Point**, **Geohash** and **Convert** classes.
Thanks to the [Symfony Console Component](https://github.com/symfony/Console).

```bash
$ php geotools list                                                           # list available commands
$ php geotools help distance:flat                                             # get help for a command
$ php geotools distance:flat "40° 26.7717, -79° 56.93172" "30°16′57″N 029°48′32″W"  # 4690203.1048522
$ php geotools distance:haversine "35,45" "45,35" --ft                       # 4593030.9787593
$ php geotools distance:vincenty "35,45" "45,35" --km                        # 1398.4080717661
$ php geotools d:v "35,45" "45,35" --km --ellipsoid=WGS60                    # 1398.4145201642
$ php geotools point:initial-cardinal "40:26:46.302N 079:56:55.903W" "43.296482, 5.36978" # NE
$ php geotools point:final-cardinal "40:26:46.302N 079:56:55.903W" "43.296482, 5.36978"   # ESE
$ php geotools point:destination "40° 26.7717, -79° 56.93172" 25 10000      # 40.527599285543, -79.898914904538
$ php geotools p:d "40° 26.7717, -79° 56.93172" 25 10000 --ellipsoid=GRS_1980
$ php geotools geohash:encode "40° 26.7717, -79° 56.93172" --length=3       # dpp
$ php geotools convert:dm "40.446195, -79.948862" --format="%P%D°%N %p%d°%n"  # 40°26.7717 -79°56.93172
$ php geotools convert:dms "40.446195, -79.948862" --format="%P%D:%M:%S, %p%d:%m:%s" # 40:26:46, -79:56:56
$ php geotools convert:utm "60.3912628, 5.3220544"                           # 32V 297351 6700644
$ php geotools c:u "60.3912628, 5.3220544" --ellipsoid=AIRY                  # 32V 297371 6700131
```


Unit Tests
----------

Install dependencies:

```bash
composer install
```

Run the test suite:

```bash
./vendor/bin/phpunit
```


Credits
-------

* [Antoine Corcy](https://twitter.com/toin0u)
* [Pascal Borreli](https://twitter.com/pborreli)
* [Phil Sturgeon](https://twitter.com/philsturgeon)
* [Gabriel Bull](mailto:me@gabrielbull.com)
* [All contributors](https://github.com/thephpleague/geotools/contributors)


Acknowledgments
---------------
* [Geocoder](https://github.com/geocoder-php/Geocoder) -
[MIT](https://raw.github.com/geocoder-php/Geocoder/master/LICENSE)
* [ReactPHP](https://github.com/reactphp/) -
[MIT](https://raw.github.com/reactphp/react/master/LICENSE)
* [Symfony Console Component](https://github.com/symfony/Console) -
[MIT](https://raw.github.com/symfony/Console/master/LICENSE)
* [Symfony Serializer Component](https://github.com/symfony/Serializer) -
[MIT](https://raw.github.com/symfony/Serializer/master/LICENSE)
* [Geokit](https://github.com/jsor/Geokit),
[Geotools-for-CodeIgniter](https://github.com/weejames/Geotools-for-CodeIgniter),
[geotools-php](https://github.com/jillesvangurp/geotools-php) ...


Changelog
---------

[See the changelog file](https://github.com/thephpleague/geotools/blob/master/CHANGELOG.md)

Contributing
------------

Please see [CONTRIBUTING](https://github.com/thephpleague/geotools/blob/master/CONTRIBUTING.md) for details.

Support
-------

Bugs and feature requests are tracked on [GitHub](https://github.com/thephpleague/geotools/issues)

Contributor Code of Conduct
---------------------------

As contributors and maintainers of this project, we pledge to respect all people
who contribute through reporting issues, posting feature requests, updating
documentation, submitting pull requests or patches, and other activities.

We are committed to making participation in this project a harassment-free
experience for everyone, regardless of level of experience, gender, gender
identity and expression, sexual orientation, disability, personal appearance,
body size, race, age, or religion.

Examples of unacceptable behavior by participants include the use of sexual
language or imagery, derogatory comments or personal attacks, trolling, public
or private harassment, insults, or other unprofessional conduct.

Project maintainers have the right and responsibility to remove, edit, or reject
comments, commits, code, wiki edits, issues, and other contributions that are
not aligned to this Code of Conduct. Project maintainers who do not follow the
Code of Conduct may be removed from the project team.

Instances of abusive, harassing, or otherwise unacceptable behavior may be
reported by opening an issue or contacting one or more of the project
maintainers.

This Code of Conduct is adapted from the [Contributor
Covenant](https://contributor-covenant.org), version 1.0.0, available at
[https://contributor-covenant.org/version/1/0/0/](https://contributor-covenant.org/version/1/0/0/)

License
-------

Geotools is released under the MIT License. See the bundled
[LICENSE](https://github.com/thephpleague/geotools/blob/master/LICENSE) file for details.
