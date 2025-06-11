<?php

/*
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools\Coordinate;

use League\Geotools\ArrayCollection;
use League\Geotools\Exception\InvalidArgumentException;

/**
 * @author Gabriel Bull <me@gabrielbull.com>
 */
class CoordinateCollection extends ArrayCollection
{
    /**
     * @var Ellipsoid
     */
    private $ellipsoid;

    /**
     * CoordinateCollection constructor.
     *
     * @param CoordinateInterface[] $coordinates
     * @param ?Ellipsoid            $ellipsoid
     */
    public function __construct(array $coordinates = array(), ?Ellipsoid $ellipsoid = null)
    {
        if ($ellipsoid) {
            $this->ellipsoid = $ellipsoid;
        } elseif (count($coordinates) > 0) {
            $this->ellipsoid = reset($coordinates)->getEllipsoid();
        }

        $this->checkCoordinatesArray($coordinates);

        parent::__construct($coordinates);
    }

    /**
     * @param string     $key
     * @param CoordinateInterface $value
     */
    public function set($key, $value)
    {
        $this->checkEllipsoid($value);
        $this->elements[$key] = $value;
    }

    /**
     * @param  CoordinateInterface $value
     * @return bool
     */
    public function add($value)
    {
        $this->checkEllipsoid($value);
        $this->elements[] = $value;

        return true;
    }

    /**
     * @return Ellipsoid
     */
    public function getEllipsoid()
    {
        return $this->ellipsoid;
    }

    /**
     * @param array CoordinateInterface[] $coordinates
     */
    private function checkCoordinatesArray(array $coordinates)
    {
        foreach ($coordinates as $coordinate) {
            $this->checkEllipsoid($coordinate);
        }
    }

    /**
     * @param CoordinateInterface $coordinate
     *
     * @throws InvalidArgumentException
     */
    private function checkEllipsoid(CoordinateInterface $coordinate)
    {
        if ($this->ellipsoid === null) {
            $this->ellipsoid = $coordinate->getEllipsoid();
        }

        if ($coordinate->getEllipsoid() != $this->ellipsoid) {
            throw new InvalidArgumentException("Ellipsoids don't match");
        }
    }
}
