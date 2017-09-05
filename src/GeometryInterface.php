<?php

/*
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools;

use League\Geotools\BoundingBox\BoundingBoxInterface;
use League\Geotools\Coordinate\Coordinate;
use League\Geotools\Coordinate\CoordinateCollection;
use League\Geotools\Coordinate\Ellipsoid;

/**
 * @author Rémi San <remi.san@gmail.com>
 */
interface GeometryInterface
{
    /**
     * Returns the geometry type.
     *
     * @return string
     */
    public function getGeometryType();

    /**
     * Returns the ellipsoid of the geometry.
     *
     * @return Ellipsoid
     */
    public function getEllipsoid();

    /**
     * Returns the precision of the geometry.
     *
     * @return integer
     */
    public function getPrecision();

    /**
     *  Returns a vertex of this <code>Geometry</code> (usually, but not necessarily, the first one).
     *  The returned coordinate should not be assumed to be an actual Coordinate object used in
     *  the internal representation.
     *
     * @return Coordinate if there's a coordinate in the collection
     * @return null if this Geometry is empty
     */
    public function getCoordinate();

    /**
     *  Returns a collection containing the values of all the vertices for this geometry.
     *  If the geometry is a composite, the array will contain all the vertices
     *  for the components, in the order in which the components occur in the geometry.
     *
     *@return CoordinateCollection the vertices of this <code>Geometry</code>
     */
    public function getCoordinates();

    /**
     * Returns true if the geometry is empty.
     *
     * @return boolean
     */
    public function isEmpty();

    /**
     * Returns the bounding box of the Geometry
     *
     * @return BoundingBoxInterface
     */
    public function getBoundingBox();
}
