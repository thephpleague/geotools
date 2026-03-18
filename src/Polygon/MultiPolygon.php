<?php

namespace League\Geotools\Polygon;

use League\Geotools\Coordinate\Coordinate;
use League\Geotools\Coordinate\CoordinateInterface;
use League\Geotools\GeometryCollection;

class MultiPolygon extends GeometryCollection implements PolygonInterface
{
    use PolygonCenterTrait;
    const TYPE = 'MULTIPOLYGON';

    /**
     * @return string
     */
    public function getGeometryType()
    {
        return self::TYPE;
    }

    /**
     * @param  CoordinateInterface $coordinate
     * @return boolean
     */
    public function pointInPolygon(CoordinateInterface $coordinate)
    {
        /** @var PolygonInterface $polygon */
        foreach ($this->elements as $polygon) {
            if ($polygon->pointInPolygon($coordinate)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  CoordinateInterface $coordinate
     * @return boolean
     */
    public function pointOnBoundary(CoordinateInterface $coordinate)
    {
        /** @var PolygonInterface $polygon */
        foreach ($this->elements as $polygon) {
            if ($polygon->pointOnBoundary($coordinate)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  CoordinateInterface $coordinate
     * @return boolean
     */
    public function pointOnVertex(CoordinateInterface $coordinate)
    {
        /** @var PolygonInterface $polygon */
        foreach ($this->elements as $polygon) {
            if ($polygon->pointOnVertex($coordinate)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns the geographic centroid of all coordinates across every polygon
     * in this collection.
     *
     * @return CoordinateInterface|null  null when the collection is empty
     */
    public function getCenter(): ?CoordinateInterface
    {
        $sumX = $sumY = $sumZ = 0.0;
        $count = 0;
        $ellipsoid = null;

        foreach ($this->getCoordinates() as $coordinate) {
            [$x, $y, $z] = $this->coordinateToXYZ($coordinate);
            $sumX += $x;
            $sumY += $y;
            $sumZ += $z;
            $count++;
            if ($ellipsoid === null) {
                $ellipsoid = $coordinate->getEllipsoid();
            }
        }

        if ($count === 0) {
            return null;
        }

        $x = $sumX / $count;
        $y = $sumY / $count;
        $z = $sumZ / $count;

        $lng = rad2deg(atan2($y, $x));
        $hyp = sqrt($x * $x + $y * $y);
        $lat = rad2deg(atan2($z, $hyp));

        return new Coordinate([$lat, $lng], $ellipsoid);
    }

    /**
     * Returns the radius of the multi-polygon in meters, defined as the
     * maximum haversine distance from the centroid to any vertex.
     *
     * @return float
     */
    public function getRadius(): float
    {
        $center = $this->getCenter();
        if ($center === null) {
            return 0.0;
        }

        $radius = 0.0;
        $R    = $center->getEllipsoid()->getArithmeticMeanRadius();
        $latC = deg2rad($center->getLatitude());
        $lngC = deg2rad($center->getLongitude());

        foreach ($this->getCoordinates() as $coordinate) {
            $d = $this->haversineDistance(
                $R,
                $latC,
                $lngC,
                deg2rad($coordinate->getLatitude()),
                deg2rad($coordinate->getLongitude())
            );

            if ($d > $radius) {
                $radius = $d;
            }
        }

        return $radius;
    }
}
