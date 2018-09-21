<?php

/**
 *  CoordinateTransformationLibrary - David Gustafsson 2012.
 *
 *  RT90, SWEREF99 and WGS84 coordinate transformation library
 *
 * This library is a PHP port of the .NET library by Björn Sållarp.
 *  calculations are based entirely on the excellent
 *  javscript library by Arnold Andreassons.
 *
 * Source: http://www.lantmateriet.se/geodesi/
 * Source: Arnold Andreasson, 2007. http://mellifica.se/konsult
 * Source: Björn Sållarp. 2009. http://blog.sallarp.com
 * Source: Mathias Åhsberg, 2009. http://github.com/goober/
 * Author: David Gustafsson, 2012. http://github.com/david-xelera/
 *
 * License: http://creativecommons.org/licenses/by-nc-sa/3.0/
 */

namespace Drola\CoordinateTransformationLibrary\Projection;

abstract class RT90Projection
{
    const RT90_7_5_GON_V = 0;
    const RT90_5_0_GON_V = 1;
    const RT90_2_5_GON_V = 2;
    const RT90_0_0_GON_V = 3;
    const RT90_2_5_GON_O = 5;
    const RT90_5_0_GON_O = 6;
}
