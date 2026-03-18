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

enum EllipsoidName: string
{
    case AIRY                  = 'AIRY';
    case AUSTRALIAN_NATIONAL   = 'AUSTRALIAN_NATIONAL';
    case BESSEL_1841           = 'BESSEL_1841';
    case BESSEL_1841_NAMBIA    = 'BESSEL_1841_NAMBIA';
    case CLARKE_1866           = 'CLARKE_1866';
    case CLARKE_1880           = 'CLARKE_1880';
    case EVEREST               = 'EVEREST';
    case FISCHER_1960_MERCURY  = 'FISCHER_1960_MERCURY';
    case FISCHER_1968          = 'FISCHER_1968';
    case GRS_1967              = 'GRS_1967';
    case GRS_1980              = 'GRS_1980';
    case HELMERT_1906          = 'HELMERT_1906';
    case HOUGH                 = 'HOUGH';
    case INTERNATIONAL         = 'INTERNATIONAL';
    case KRASSOVSKY            = 'KRASSOVSKY';
    case MODIFIED_AIRY         = 'MODIFIED_AIRY';
    case MODIFIED_EVEREST      = 'MODIFIED_EVEREST';
    case MODIFIED_FISCHER_1960 = 'MODIFIED_FISCHER_1960';
    case SOUTH_AMERICAN_1969   = 'SOUTH_AMERICAN_1969';
    case WGS60                 = 'WGS60';
    case WGS66                 = 'WGS66';
    case WGS72                 = 'WGS72';
    case WGS84                 = 'WGS84';
}
