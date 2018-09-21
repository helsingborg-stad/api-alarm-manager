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

namespace Drola\Tests\CoordinateTransformationLibrary;

use Drola\CoordinateTransformationLibrary\Format;

class CoordinateTransformationLibraryFormattingTest extends \PHPUnit_Framework_TestCase
{
    public function getCases()
    {
        return array(
            array('wgs84_dms', 59.3489, 18.0473, array("N 59º 20' 56,04000\"", "E 18º 2' 50,28000\"")),
            array('rt90', 59.98200833333333, 17.83503333333333, array(6653174.343, 1613318.742)),
            array('sweref99', 59.98200833333333, 17.83503333333333, array(6652797.165, 658185.201))
        );
    }

    /**
     * @dataProvider getCases
     */
    public function testFormat($format, $lat, $lon, $expected)
    {
        $result = Format::format($lat, $lon, $format);
        $this->assertEquals($expected, $result);
    }
}
