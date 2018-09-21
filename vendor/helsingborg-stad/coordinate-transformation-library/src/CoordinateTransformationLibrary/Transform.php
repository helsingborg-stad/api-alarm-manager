<?php

namespace Drola\CoordinateTransformationLibrary;

use Drola\CoordinateTransformationLibrary\Position\RT90Position;
use Drola\CoordinateTransformationLibrary\Position\SWEREF99Position;
use Drola\CoordinateTransformationLibrary\Position\WGS84Position;
use Drola\CoordinateTransformationLibrary\Projection\SWEREFProjection;

class Transform
{
    public static function RT90ToWGS84($latitude, $longitude)
    {
        $position = new RT90Position($latitude, $longitude);
        $wgsPos = $position->toWGS84();
        return array($wgsPos->getLatitude(), $wgsPos->getLongitude());
    }

    public static function SWEREF99ToWGS84($latitude, $longitude)
    {
        $position = new SWEREF99Position($latitude, $longitude);
        $wgsPos = $position->toWGS84();
        return array($wgsPos->getLatitude(), $wgsPos->getLongitude());
    }

    public static function WGS84ToSWEREF99($latitude, $longitude)
    {
        $position = new WGS84Position($latitude, $longitude);
        $rtPos = new SWEREF99Position($position, SWEREFProjection::SWEREF_99_TM);
        return array($rtPos->getLatitude(), $rtPos->getLongitude());
    }
}
