<?php

namespace ApiAlarmManager;

class Station extends \ApiAlarmManager\Entity\PostManager
{
    public $post_type = 'station';
    public string $post_title;
    public string $_alarm_manager_uid;
    public string $station_id;
    public string $city;
    public int|string $ID;
}
