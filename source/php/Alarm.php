<?php

namespace ApiAlarmManager;

class Alarm extends \ApiAlarmManager\Entity\PostManager
{
    public $post_type = 'alarm';
    public string $post_title;
    public string $post_date;
    public string $_alarm_manager_uid;
    public string $alarm_id;
    public string $case_id;
    public string $type;
    public string $extend;
    public string $station;
    public string $address;
    public string $city;
    public string $coordinate_x;
    public string $coordinate_y;
    public string $zone;
    public string $to_zone;
    public string $ID;
}
