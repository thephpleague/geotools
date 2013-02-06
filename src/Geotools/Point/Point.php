<?php

/**
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Geotools\Point;

use Geotools\AbstractGeotools;
use Geotools\Coordinate\Coordinate;
use Geotools\Coordinate\CoordinateInterface;

/**
 * Point class
 *
 * @author Antoine Corcy <contact@sbin.dk>
 */
class Point extends AbstractGeotools implements PointInterface
{
    /**
     * {@inheritDoc}
     */
    public function setFrom(CoordinateInterface $from)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * {@inheritDoc}
     */
    public function setTo(CoordinateInterface $to)
    {
        $this->to = $to;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Returns the initial bearing from the origin coordinate
     * to the destination coordinate in degrees.
     *
     * @return float The bearing in degrees
     */
    public function bearing()
    {
        $latA = deg2rad($this->from->getLatitude());
        $latB = deg2rad($this->to->getLatitude());
        $dLng = deg2rad($this->to->getLongitude() - $this->from->getLongitude());

        $y = sin($dLng) * cos($latB);
        $x = cos($latA) * sin($latB) - sin($latA) * cos($latB) * cos($dLng);

        return (float) (rad2deg(atan2($y, $x)) + 360) % 360;
    }

    /**
     * Returns the caridnal point / direction from the origin coordinate to
     * the destination coordinate.
     * @see http://en.wikipedia.org/wiki/Cardinal_direction
     *
     * @return string The cardinal point / direction
     */
    public function cardinal()
    {
        return $this->cardinalPoints[round($this->bearing($this->from, $this->to) / 22.5)];
    }

    /**
     * Returns the half-way point / coordinate along a great circle
     * path between the origin and the destination coordinates.
     *
     * @return CoordinateInterface
     */
    public function middle()
    {
        $latA = deg2rad($this->from->getLatitude());
        $lngA = deg2rad($this->from->getLongitude());
        $latB = deg2rad($this->to->getLatitude());
        $lngB = deg2rad($this->to->getLongitude());

        $bx = cos($latB) * cos($lngB - $lngA);
        $by = cos($latB) * sin($lngB - $lngA);

        $lat3 = rad2deg(atan2(sin($latA) + sin($latB), sqrt((cos($latA) + $bx) * (cos($latA) + $bx) + $by * $by)));
        $lng3 = rad2deg($lngA + atan2($by, cos($latA) + $bx));

        return new Coordinate(array($lat3, $lng3));
    }

    /**
     * Returns the destination point with a given bearing in degrees travelling along a
     * (shortest distance) great circle arc and a distance in meters.
     *
     * @param integer   $bearing  The bearing of the origin in degrees.
     * @param $distance $distance The distance from the origin in meters.
     *
     * @return CoordinateInterface
     */
    public function destination($bearing, $distance)
    {
        $lat = deg2rad($this->from->getLatitude());
        $lng = deg2rad($this->from->getLongitude());

        $bearing = deg2rad($bearing);

        $endLat = asin(sin($lat) * cos($distance / AbstractGeotools::EARTH_RADIUS) + cos($lat) *
            sin($distance / AbstractGeotools::EARTH_RADIUS) * cos($bearing));
        $endLon = $lng + atan2(sin($bearing) * sin($distance / AbstractGeotools::EARTH_RADIUS) * cos($lat),
            cos($distance / AbstractGeotools::EARTH_RADIUS) - sin($lat) * sin($endLat));

        return new Coordinate(array(rad2deg($endLat), rad2deg($endLon)));
    }
}
