<?php

/**
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Geotools\Coordinate;

use Geotools\Exception\InvalidArgumentException;
use Geocoder\Result\ResultInterface;

/**
 * Coordinate class
 *
 * @author Antoine Corcy <contact@sbin.dk>
 */
class Coordinate implements CoordinateInterface
{
    /**
     * The latitude of the coordinate.
     *
     * @var double
     */
    protected $latitude;

    /**
     * The longitude of the coordinate.
     *
     * @var double
     */
    protected $longitude;


    /**
     * Set the latitude and the longitude of the coordinates.
     *
     * @param ResultInterface|array $coordinates The coordinates.
     *
     * @throws InvalidArgumentException
     */
    public function __construct($coordinates)
    {
        if ($coordinates instanceof ResultInterface) {
            $this->setLatitude($coordinates->getLatitude());
            $this->setLongitude($coordinates->getLongitude());
        } elseif (is_array($coordinates) && 2 === count($coordinates)) {
            $this->setLatitude($coordinates[0]);
            $this->setLongitude($coordinates[1]);
        } elseif (is_string($coordinates) && preg_match('/(\-?\d+\.?\d*)[, ] ?(\-?\d+\.?\d*)$/', $coordinates, $match)) {
            $this->setLatitude($match[1]);
            $this->setLongitude($match[2]);
        } else {
            throw new InvalidArgumentException(sprintf(
                '%s', 'It should be an array or a class which implements Geocoder\Result\ResultInterface !'
            ));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function setLatitude($latitude)
    {
        $this->latitude = (double) $latitude;
    }

    /**
     * {@inheritDoc}
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * {@inheritDoc}
     */
    public function setLongitude($longitude)
    {
        $this->longitude = (double) $longitude;
    }

    /**
     * {@inheritDoc}
     */
    public function getLongitude()
    {
        return $this->longitude;
    }
}
