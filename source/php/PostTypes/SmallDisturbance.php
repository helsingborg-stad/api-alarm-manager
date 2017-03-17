<?php

namespace ApiAlarmManager\PostTypes;

class SmallDisturbance extends \ApiAlarmManager\Entity\CustomPostType
{
    public function __construct()
    {
        parent::__construct(
            __('Small Disturbances', 'api-alarm-manager'),
            __('Small Disturbance', 'api-alarm-manager'),
            'small-disturbance',
            array(
                'description'          => __('Small Disturbances', 'api-alarm-manager'),
                'menu_icon'            => 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4KPHN2ZyB3aWR0aD0iMjRweCIgaGVpZ2h0PSIyNHB4IiB2aWV3Qm94PSIwIDAgMjQgMjQiIHZlcnNpb249IjEuMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayI+CiAgICA8IS0tIEdlbmVyYXRvcjogU2tldGNoIDQyICgzNjc4MSkgLSBodHRwOi8vd3d3LmJvaGVtaWFuY29kaW5nLmNvbS9za2V0Y2ggLS0+CiAgICA8dGl0bGU+aW5mbzwvdGl0bGU+CiAgICA8ZGVzYz5DcmVhdGVkIHdpdGggU2tldGNoLjwvZGVzYz4KICAgIDxkZWZzPjwvZGVmcz4KICAgIDxnIGlkPSJQYWdlLTEiIHN0cm9rZT0ibm9uZSIgc3Ryb2tlLXdpZHRoPSIxIiBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPgogICAgICAgIDxwYXRoIGQ9Ik0xMiwwIEM1LjM3MywwIDAsNS4zNzMgMCwxMiBDMCwxOC42MjcgNS4zNzMsMjQgMTIsMjQgQzE4LjYyNywyNCAyNCwxOC42MjcgMjQsMTIgQzI0LDUuMzczIDE4LjYyNywwIDEyLDAgWiBNMTEuOTk5LDUuNzUgQzEyLjY4OSw1Ljc1IDEzLjI1LDYuMzEgMTMuMjUsNyBDMTMuMjUsNy42OSAxMi42ODksOC4yNSAxMS45OTksOC4yNSBDMTEuMzA4NjQ0MSw4LjI1IDEwLjc0OSw3LjY5MDM1NTk0IDEwLjc0OSw3IEMxMC43NDksNi4zMDk2NDQwNiAxMS4zMDg2NDQxLDUuNzUgMTEuOTk5LDUuNzUgTDExLjk5OSw1Ljc1IFogTTE0LDE4IEwxMCwxOCBMMTAsMTcgQzEwLjQ4NCwxNi44MjEgMTEsMTYuNzk5IDExLDE2LjI2NSBMMTEsMTEuNzk4IEMxMSwxMS4yNjQgMTAuNDg0LDExLjE4IDEwLDExLjAwMSBMMTAsMTAuMDAxIEwxMywxMC4wMDEgTDEzLDE2LjI2NiBDMTMsMTYuODAxIDEzLjUxNywxNi44MjQgMTQsMTcuMDAxIEwxNCwxOCBaIiBpZD0iaW5mbyIgZmlsbD0iIzAwMDAwMCIgZmlsbC1ydWxlPSJub256ZXJvIj48L3BhdGg+CiAgICA8L2c+Cjwvc3ZnPg==',
                'public'               => true,
                'publicly_queriable'   => true,
                'show_ui'              => true,
                'show_in_nav_menus'    => true,
                'has_archive'          => true,
                'rewrite'              => array(
                    'slug'       => 'big-disturbance',
                    'with_front' => false
                ),
                'hierarchical'         => false,
                'exclude_from_search'  => false,
                'supports'             => array('title', 'revisions', 'editor', 'thumbnail'),
            )
        );
    }
}
