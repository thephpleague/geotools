<?php

/*
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools\Polygon;

use League\Geotools\Coordinate\CoordinateInterface;

/**
 * Shared helpers for computing the geographic centroid and radius of a
 * collection of coordinates.
 */
trait PolygonCenterTrait
{
    /**
     * Converts a lat/lng coordinate to its (x, y, z) unit-sphere representation.
     *
     * @param  CoordinateInterface $coordinate
     * @return float[]  [x, y, z]
     */
    private function coordinateToXYZ(CoordinateInterface $coordinate): array
    {
        $lat = deg2rad($coordinate->getLatitude());
        $lng = deg2rad($coordinate->getLongitude());

        return [
            cos($lat) * cos($lng),
            cos($lat) * sin($lng),
            sin($lat),
        ];
    }

    /**
     * Returns the haversine distance in metres between a centre coordinate and
     * a vertex coordinate.
     *
     * @param  float $R     Mean radius of the earth in metres
     * @param  float $latC  Centre latitude in radians
     * @param  float $lngC  Centre longitude in radians
     * @param  float $latV  Vertex latitude in radians
     * @param  float $lngV  Vertex longitude in radians
     * @return float        Distance in metres
     */
    private function haversineDistance(float $R, float $latC, float $lngC, float $latV, float $lngV): float
    {
        $dLat = $latV - $latC;
        $dLng = $lngV - $lngC;

        $a = (sin($dLat / 2) ** 2) + cos($latC) * cos($latV) * (sin($dLng / 2) ** 2);

        return 2 * $R * asin(sqrt($a));
    }
}
