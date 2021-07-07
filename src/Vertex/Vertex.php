<?php

/*
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools\Vertex;

use League\Geotools\Coordinate\Coordinate;
use League\Geotools\Coordinate\CoordinateInterface;
use League\Geotools\Coordinate\Ellipsoid;
use League\Geotools\CoordinateCouple;
use League\Geotools\Geotools;

/**
 * Vertex class
 *
 * @author Antoine Corcy <contact@sbin.dk>
 */
class Vertex implements VertexInterface
{
    use CoordinateCouple;

    /**
     * @var double
     */
    protected $gradient;

    /**
     * @var double
     */
    protected $ordinateIntercept;

    /**
     * @var integer
     */
    private $precision = 8;

    /**
     * {@inheritDoc}
     */
    public function setFrom(CoordinateInterface $from)
    {
        $this->from = $from;

        if (empty($this->to) || ($this->to->getLatitude() - $this->from->getLatitude() === 0)) {
            return $this;
        }

        if ($this->to->getLatitude() !== $this->from->getLatitude()) {
            $this->gradient = ($this->to->getLongitude() - $this->from->getLongitude()) / ($this->to->getLatitude() - $this->from->getLatitude());
            $this->ordinateIntercept = $this->from->getLongitude() - $this->from->getLatitude() * $this->gradient;
        } else {
            $this->gradient = null;
            $this->ordinateIntercept = null;
        }

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

        if (empty($this->from) || ($this->to->getLatitude() - $this->from->getLatitude() === 0)) {
            return $this;
        }

        if ($this->to->getLatitude() !== $this->from->getLatitude()) {
            $this->gradient = ($this->to->getLongitude() - $this->from->getLongitude()) / ($this->to->getLatitude() - $this->from->getLatitude());
            $this->ordinateIntercept = $this->from->getLongitude() - $this->from->getLatitude() * $this->gradient;
        } else {
            $this->gradient = null;
            $this->ordinateIntercept = null;
        }

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
     * {@inheritDoc}
     */
    public function getGradient()
    {
        return $this->gradient;
    }

    /**
     * {@inheritDoc}
     */
    public function getOrdinateIntercept()
    {
        return $this->ordinateIntercept;
    }

    /**
     * @return integer
     */
    public function getPrecision()
    {
        return $this->precision;
    }

    /**
     * @param  integer $precision
     * @return $this
     */
    public function setPrecision($precision)
    {
        $this->precision = $precision;

        return $this;
    }

    /**
     * Returns the initial bearing from the origin coordinate
     * to the destination coordinate in degrees.
     *
     * @return float The initial bearing in degrees
     */
    public function initialBearing()
    {
        Ellipsoid::checkCoordinatesEllipsoid($this->from, $this->to);

        $latA = deg2rad($this->from->getLatitude());
        $latB = deg2rad($this->to->getLatitude());
        $dLng = deg2rad($this->to->getLongitude() - $this->from->getLongitude());

        $y = sin($dLng) * cos($latB);
        $x = cos($latA) * sin($latB) - sin($latA) * cos($latB) * cos($dLng);

        return fmod(rad2deg(atan2($y, $x)) + 360, 360);
    }

    /**
     * Returns the final bearing from the origin coordinate
     * to the destination coordinate in degrees.
     *
     * @return float The final bearing in degrees
     */
    public function finalBearing()
    {
        Ellipsoid::checkCoordinatesEllipsoid($this->from, $this->to);

        $latA = deg2rad($this->to->getLatitude());
        $latB = deg2rad($this->from->getLatitude());
        $dLng = deg2rad($this->from->getLongitude() - $this->to->getLongitude());

        $y = sin($dLng) * cos($latB);
        $x = cos($latA) * sin($latB) - sin($latA) * cos($latB) * cos($dLng);

        return fmod(fmod(rad2deg(atan2($y, $x)) + 360, 360) + 180, 360);
    }

    /**
     * Returns the initial cardinal point / direction from the origin coordinate to
     * the destination coordinate.
     * @see http://en.wikipedia.org/wiki/Cardinal_direction
     *
     * @return string The initial cardinal point / direction
     */
    public function initialCardinal()
    {
        Ellipsoid::checkCoordinatesEllipsoid($this->from, $this->to);

        return Geotools::$cardinalPoints[(integer) round($this->initialBearing() / 22.5)];
    }

    /**
     * Returns the final cardinal point / direction from the origin coordinate to
     * the destination coordinate.
     * @see http://en.wikipedia.org/wiki/Cardinal_direction
     *
     * @return string The final cardinal point / direction
     */
    public function finalCardinal()
    {
        Ellipsoid::checkCoordinatesEllipsoid($this->from, $this->to);

        return Geotools::$cardinalPoints[(integer) round($this->finalBearing() / 22.5)];
    }

    /**
     * Returns the half-way point / coordinate along a great circle
     * path between the origin and the destination coordinates.
     *
     * @return CoordinateInterface
     */
    public function middle()
    {
        Ellipsoid::checkCoordinatesEllipsoid($this->from, $this->to);

        $latA = deg2rad($this->from->getLatitude());
        $lngA = deg2rad($this->from->getLongitude());
        $latB = deg2rad($this->to->getLatitude());
        $lngB = deg2rad($this->to->getLongitude());

        $bx = cos($latB) * cos($lngB - $lngA);
        $by = cos($latB) * sin($lngB - $lngA);

        $lat3 = rad2deg(atan2(sin($latA) + sin($latB), sqrt((cos($latA) + $bx) * (cos($latA) + $bx) + $by * $by)));
        $lng3 = rad2deg($lngA + atan2($by, cos($latA) + $bx));

        return new Coordinate([$lat3, $lng3], $this->from->getEllipsoid());
    }

    /**
     * Returns the destination point with a given bearing in degrees travelling along a
     * (shortest distance) great circle arc and a distance in meters.
     *
     * @param integer $bearing  The bearing of the origin in degrees.
     * @param integer $distance The distance from the origin in meters.
     *
     * @return CoordinateInterface
     */
    public function destination($bearing, $distance)
    {
        $lat = deg2rad($this->from->getLatitude());
        $lng = deg2rad($this->from->getLongitude());

        $bearing = deg2rad($bearing);

        $endLat = asin(sin($lat) * cos($distance / $this->from->getEllipsoid()->getA()) + cos($lat) *
            sin($distance / $this->from->getEllipsoid()->getA()) * cos($bearing));
        $endLon = $lng + atan2(sin($bearing) * sin($distance / $this->from->getEllipsoid()->getA()) * cos($lat),
            cos($distance / $this->from->getEllipsoid()->getA()) - sin($lat) * sin($endLat));

        return new Coordinate([rad2deg($endLat), rad2deg($endLon)], $this->from->getEllipsoid());
    }

    /**
     * Returns true if the vertex passed on argument is on the same line as this object
     *
     * @param  Vertex  $vertex The vertex to compare
     * @return boolean
     */
    public function isOnSameLine(Vertex $vertex) {
        if (is_null($this->getGradient()) && is_null($vertex->getGradient()) && $this->from->getLongitude() == $vertex->getFrom()->getLongitude()) {
            return true;
        } elseif (!is_null($this->getGradient()) && !is_null($vertex->getGradient())) {
            return (
                bccomp($this->getGradient(), $vertex->getGradient(), $this->getPrecision()) === 0
                &&
                bccomp($this->getOrdinateIntercept(), $vertex->getOrdinateIntercept(), $this->getPrecision()) ===0
            );
        } else {
            return false;
        }
    }

    /**
     * Returns the other coordinate who is not the coordinate passed on argument
     * @param  CoordinateInterface $coordinate
     * @return null|Coordinate
     */
    public function getOtherCoordinate(CoordinateInterface $coordinate) {
        if ($coordinate->isEqual($this->from)) {
            return $this->to;
        } else if ($coordinate->isEqual($this->to)) {
            return $this->from;
        }
        return null;
    }

    /**
     * Returns the determinant value between $this (vertex) and another vertex.
     *
     * @param  Vertex $vertex [description]
     * @return [type]         [description]
     */
    public function getDeterminant(Vertex $vertex) {
        $abscissaVertexOne = $this->to->getLatitude() - $this->from->getLatitude();
        $ordinateVertexOne = $this->to->getLongitude() - $this->from->getLongitude();
        $abscissaVertexSecond = $vertex->getTo()->getLatitude() - $vertex->getFrom()->getLatitude();
        $ordinateVertexSecond = $vertex->getTo()->getLongitude() - $vertex->getFrom()->getLongitude();

        return bcsub(
            bcmul($abscissaVertexOne, $ordinateVertexSecond, $this->precision),
            bcmul($abscissaVertexSecond, $ordinateVertexOne, $this->precision),
            $this->precision
        );
    }

}
