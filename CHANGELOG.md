CHANGELOG
=========

0.8.3
-----

### Fixed

- Fix incompatibility with PHP >= 7.4 by saving longitude and latitude as a string

### Changed

- Use react/event-loop: 1.0

0.8.2
-----

### Fixed 

- Fix namespace issue when creating provider class name
- PHPUnit deprecations

### Changed

- Supported PHP versions >= 7.3
- PHPUnit 8.5

0.8.1
-----

### Fixed 

- Symfony 5 support.

0.8.0 (2018-02-22)
------------------

### Added

- We use `willdurand/geocoder` 4. 
- Add a method to vertex to compute the determinant with another vertex
- `ArrayCollection::merge`
- `BoundingBox::merge`
- `BoundingBoxInterface::merge`
- `BoundingBoxInterface::getAsPolygon`
- Added abstract class `GeometryCollection`
- Added `GeometryInterface`
- Added `MultiPolygon`

### Changes

- Renamed `BoundingBox::getPolygon` to `BoundingBox::getAsPolygon`
- `PolygonInterface` extends `GeometryInterface`

### Fixed

- Decimal-Degrees parser from Decimal-Minutes 

### Removed

- Removed `AbstractGeotools` class in favor of `CoordinateCouple`. Also added constants to `GeotoolsInterface`.
- Our HTTP layer in favor of HTTPlug
- Our cache layer in favor of PSR-6

0.7.0 (2016-02-03)
------------------

* Updated: `Point` is now `Vertex` [BC break]
* Updated: use `Predis` 1.0 version
* Updated: tests against PHP7
* Updated: documentation and badges
* Added: allow Symfony console, property-access and serializer ~3.0

0.6.0 (2015-10-11)
------------------

* Fixed: cache layer: Redis, Memcached and MongoDB
* Added: cache possibility in CLI
* Added: 10:10 algorithm
* Updated: symfony console, serializer and property-access to ~2.7

0.5.0 (2015-10-10)
------------------

* Updated: use Geocoder 3.2.x
* Added: Polygon class
* Added: Bounding box class
* Fixed: division by zero in vincenty algorithm
* Dropped: PHP 5.3 and stub to JsonSerializable
* Updated: switch from async to promise
* Updated: documentation
* Added: code of conduct

0.4.0 (2014-07-30)
------------------

* Uses: PSR-4
* Removed: not relevant autoloads
* Fixed: tests
* Fixed: typos

0.3.3 (2014-05-16)
------------------

* Fixed: HHVM compatible tested on `HipHop VM 3.1.0-dev+2014.05.15`
* Added: falling tests in Distance with same coordinates (@kornrunner)
* Fixed: division by zero in computing distance between 2 identical coordinates (@kornrunner)
* Added: `setFromString` method to create and modify coordinate + doc - fix #31
* Fixed: coordinate parsing issue

0.3.2 (2014-03-15)
------------------

* Updated: geotools CLI moved in bin folder
* Updated: use Geocoder 2.4.x
* Added: great circle formula and CLI + tests
* Added: test against php 5.6
* Updated: repo name
* Added: coverage and scrutinizer-ci badges
* Updated: organisation name
* Added: test against HHVM

0.3.1 (2013-11-16)
------------------

* Updated: use Geocoder 2.3.x
* Updated: use SensioLabs Insight
* Updated: documentation
* Fixed: travis, packagist and sensiolabs insight badges
* Fixed: tests

0.3.0 (2013-07-19)
------------------

* Updated: loep (The League of Extraordinary Packages) is now owner
* Updated: use Geocoder 2.0.0

0.2.4 (2013-05-03)
------------------

* Updated: made it working with Geocoder 1.5.0
* Updated: integration with frameworks in features list
* Added: integration with Silex
* Added: integration with frameworks
* Updated: Contribution doc
* Added: memcached and mongo extensions to travis-ci
* Added: mongodb service to travis-ci
* Added: expire to Memcached cache - fix #26
* Added: expire to Redis cache + test - fix #26

0.2.3 (2013-03-29)
------------------

* Updated: MongoDB test coverage
* Added: Memcached cache test - fix #22
* Refactored: Redis and MongoDB caches tests
* Added: MongoDB cache test - fix #22
* Added: Redis cache test - fix #22
* Added: Memcached cache - fix #24

0.2.2 (2013-03-26)
------------------

* Added: Redis cache - fix #23
* Updated: MongoDB cache search by key
* Fixed: MangoDB cache
* Updated: doc with try .. catch bloc
* Fixed: Batch test for php 5.3
* Added: Cache interface + mongoDB - fix #2
* Refactored: Point test

0.2.1 (2013-03-16)
------------------

* Added: arcgis_online provider
* Merge branch 'BatchImproved'
* Fixed: batched result object embed provider's name, query and exception string - fix #6
* Added: Geocoder dev-master as require-dev
* Fixed: CLI tests

0.2.0 (2013-03-12)
------------------

* Fixed: empty ellipsoid name throws an exception now
* Added: Ellipsoid support to Point, Convert and Distance CLI - fix #7
* Added: Ellipsoid support to Point CLI
* Added: Ellipsoid support to Distance CLI
* Added: Ellipsoid support to Convert CLI
* Added: TomTom provier to CLI - fix #14
* Changed: mile parameter to mi in Distance to be more consistent [BC break]

0.1.12 (2013-03-08)
-------------------

* Added: command exemples and refactoring
* Added: help to geocoding and reverse geocoding CLI
* Fixed: homepage in composer.json
* Updated: doc and composer.json
* Fixed: php warning in CLI on wrong providers constuction arguments
* Updated: list of contributors
* Updated: geohash doc

0.1.11 (2013-03-05)
-------------------

* Added: international feet unit to CLI + test - fix #10
* Updated: relative links to absolute ones
* Added: ip_geo_base and baidu as CLI providers - fix #8
* Fixed: feet unit + test
* Added International Feet as a unit
* Added a bunch of tests.

0.1.10 (2013-02-27)
-------------------

* Added: support of different ellipsoid + doc + tests - fix #5
* Refactored: Doc + CLI commands + tests
* Improved: geocoder:geocode and geocoder:reverse CLI + tests
* Added: lowerize() method using mbstring extension

0.1.9 (2013-02-24)
------------------

* Added: dumper option to geocoder:geocode CLI + test
* Fixed: composer.json
* Added: cURL requirement for tests and CLI
* Removed: old files
* Fixed: finalCardinal() into CLI + test

0.1.8 (2013-02-21)
------------------

* Refactored: getAdapter and getProvider in CLI
* Added: CLI for Geocoder class + tests
* Added: CLI for Geocoder class + tests
* Updated: composer installation info
* Added: logo to CLI
* Fixed: travis-ci config
* Added: finalBearing() to CLI + test
* Added: finalCardinal() method + test
* Updated: cardinal() method to initialCardinal() [BC break]
* Added: finalBearing() + test
* Renamed: bearing() method to initialBearing() [BC break]

0.1.7 (2013-02-20)
------------------

* Added: CLI for Convert class + tests
* Added: CLI for Geohash class + tests
* Updated: doc with internal links
* Fixed: CLI include paths

0.1.6 (2013-02-20)
------------------

* Added: CLI for Distance and Point classes + tests
* Updated: phpunit bootstrap
* Updated: composer installation info
* Updated: Convert UTM zone exceptions are covered
* Updated: Point and Distance chainable logic and refactoring [BC break]
* Added: UTM conversion + tests + doc
* Updated: geodetic datum into doc

0.1.5 (2013-02-13)
------------------

* Added: Convert class, tests and doc
* Updated: doc about Coordinate class
* Updated: method visibility in Coordinate class
* Added: Coordinate class support different DMS coordinates
* Fixed: thrown message on invalid coordinate
* Fixed: typo calculate to compute

0.1.4 (2013-02-10)
------------------

* Updated: Batch class test
* Added: test to AbstractGeotools class
* Refactored: Batch tests
* Updated: doc with a better batch exemple
* Added: batch a set of values/coordinates againt a set of providers + tests
* Fixed: changelog list

0.1.3 (2013-02-09)
------------------

* Added: geohash ref to the doc
* Refactored: tests
* Added: Geohash class, tests and doc
* Added: normalize methods to Coordinate class
* Updated: Coordinate support string in its constructor
* Updated: Testcase's expects methods
* Updated: test to Batch class
* Refactored: Batch test class
* Updated: TestCase stub clases

0.1.2 (2013-02-08)
------------------

* Fixed: test to be compatible with PHP 5.3.x
* Added: test to Distance class
* Added: test to Batch class
* Added: test to Point class
* Updated: test to Getools class with a CoordinateInterface stub
* Updated: Contributing doc
* Updated: test to Geotools class
* Added: test to Coordinate class
* Added: test to Geotools class

0.1.1 (2013-02-06)
------------------

* Fixed: the minimum-stability of React/Async

0.1.0 (2013-02-06)
------------------

* Added: Contributing doc
* Added: Travis-ci to the doc
* Added: stillmaintained.com to the doc
* Initial import
