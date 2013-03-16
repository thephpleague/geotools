CHANGELOG
=========

0.2.2 (xxxx-xx-xx)
------------------

n/a

0.2.1 (2012-03-16)
------------------

* Added: arcgis_online provider
* Merge branch 'BatchImproved'
* Fixed: batched result object embed provider's name, query and exception string - fix #6
* Added: Geocoder dev-master as require-dev
* Fixed: CLI tests

0.2.0 (2012-03-12)
------------------

* Fixed: empty ellipsoid name throws an exception now
* Added: Ellipsoid support to Point, Convert and Distance CLI - fix #7
* Added: Ellipsoid support to Point CLI
* Added: Ellipsoid support to Distance CLI
* Added: Ellipsoid support to Convert CLI
* Added: TomTom provier to CLI - fix #14
* Changed: mile parameter to mi in Distance to be more consistent [BC break]

0.1.12 (2012-03-08)
-------------------

* Added: command exemples and refactoring
* Added: help to geocoding and reverse geocoding CLI
* Fixed: homepage in composer.json
* Updated: doc and composer.json
* Fixed: php warning in CLI on wrong providers constuction arguments
* Updated: list of contributors
* Updated: geohash doc

0.1.11 (2012-03-05)
-------------------

* Added: international feet unit to CLI + test - fix #10
* Updated: relative links to absolute ones
* Added: ip_geo_base and baidu as CLI providers - fix #8
* Fixed: feet unit + test
* Added International Feet as a unit
* Added a bunch of tests.

0.1.10 (2012-02-27)
-------------------

* Added: support of different ellipsoid + doc + tests - fix #5
* Refactored: Doc + CLI commands + tests
* Improved: geocoder:geocode and geocoder:reverse CLI + tests
* Added: lowerize() method using mbstring extension

0.1.9 (2012-02-24)
------------------

* Added: dumper option to geocoder:geocode CLI + test
* Fixed: composer.json
* Added: cURL requirement for tests and CLI
* Removed: old files
* Fixed: finalCardinal() into CLI + test

0.1.8 (2012-02-21)
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
