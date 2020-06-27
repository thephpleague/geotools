<?php

/*
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools\Tests;

use League\Geotools\CoordinateCouple;

/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
class CoordinateCoupeTest extends TestCase
{
    protected $geotools;

    protected function setup(): void
    {
        $this->geotools = new MockCoordinateCouple;
    }

    public function testFrom()
    {
        $this->assertNull($this->geotools->getFrom());
    }

    public function testTo()
    {
        $this->assertNull($this->geotools->getTo());
    }
}

class MockCoordinateCouple
{
    use CoordinateCouple;

    public function getFrom()
    {
        return $this->from;
    }

    public function getTo()
    {
        return $this->to;
    }
}
