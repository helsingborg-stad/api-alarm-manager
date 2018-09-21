<?php

namespace Drola\CoordinateTransformationLibrary;

use Drola\CoordinateTransformationLibrary\Position\RT90Position;
use Drola\CoordinateTransformationLibrary\Projection\RT90Projection;
use Drola\CoordinateTransformationLibrary\Position\WGS84Position;
use Drola\CoordinateTransformationLibrary\Position\WGS84Format;
use Drola\CoordinateTransformationLibrary\Position\SWEREF99Position;
use Drola\CoordinateTransformationLibrary\Projection\SWEREFProjection;

class Format
{
    /**
     * Format coordinates
     *
     * @param  float  $lat    Latitude
     * @param  float  $lon    Longitude
     * @param  string $format Format
     *
     * @return array          X, Y or lat, lon
     */
    public static function format($lat, $lon, $format)
    {
        $wgs84 = new WGS84Position($lat, $lon);

        switch ($format) {
            case 'wgs84_dms':
                return array(
                    $wgs84->latitudeToString(WGS84Format::DEGREES_MINUTES_SECONDS),
                    $wgs84->longitudeToString(WGS84Format::DEGREES_MINUTES_SECONDS)
                );
            case 'wgs84_dm':
                return array(
                    $wgs84->latitudeToString(WGS84Format::DEGREES_MINUTES),
                    $wgs84->longitudeToString(WGS84Format::DEGREES_MINUTES)
                );
            case 'wgs84_dd':
            case 'wgs84_decimal':
                return array(
                    $wgs84->latitudeToString(WGS84Format::DEGREES),
                    $wgs84->longitudeToString(WGS84Format::DEGREES)
                );
            case 'rt90':
                $rtPos = new RT90Position($wgs84, RT90Projection::RT90_2_5_GON_V);
                $x = ((double) round($rtPos->getLatitude() * 1000) / 1000);
                $y = ((double) round($rtPos->getLongitude() * 1000) / 1000);
                return array($x, $y);
            case 'sweref99':
                $rtPos = new SWEREF99Position($wgs84, SWEREFProjection::SWEREF_99_TM);
                $x = ((double) round($rtPos->getLatitude() * 1000) / 1000);
                $y = ((double) round($rtPos->getLongitude() * 1000) / 1000);
                return array($x, $y);
        }
    }

    /**
     * Get list of available formats
     *
     * @return array Formats
     */
    public static function getAvailableFormats()
    {
        return array(
            'wgs84_dms',
            'wgs84_dm',
            'wgs84_dd',
            'wgs84_decimal',
            'rt90',
            'sweref99'
        );
    }
}
