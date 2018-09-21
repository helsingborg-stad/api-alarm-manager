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

abstract class SWEREFProjection
{
    const SWEREF_99_TM = 0;
    const SWEREF_99_12_00 = 1;
    const SWEREF_99_13_30 = 2;
    const SWEREF_99_15_00 = 3;
    const SWEREF_99_16_30 = 4;
    const SWEREF_99_18_00 = 5;
    const SWEREF_99_14_15 = 6;
    const SWEREF_99_15_45 = 7;
    const SWEREF_99_17_15 = 8;
    const SWEREF_99_18_45 = 9;
    const SWEREF_99_20_15 = 10;
    const SWEREF_99_21_45 = 11;
    const SWEREF_99_23_1 = 12;
}
