<?php

namespace ApiAlarmManager\PostTypes;

class BigDisturbance extends \ApiAlarmManager\Entity\CustomPostType
{
    public function __construct()
    {
        parent::__construct(
            __('Big Disturbances', 'api-alarm-manager'),
            __('Big Disturbance', 'api-alarm-manager'),
            'big-disturbance',
            array(
                'description'          => __('Big Disturbances', 'api-alarm-manager'),
                'menu_icon'            => 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4KPHN2ZyB3aWR0aD0iMTZweCIgaGVpZ2h0PSIyNHB4IiB2aWV3Qm94PSIwIDAgMTYgMjQiIHZlcnNpb249IjEuMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayI+CiAgICA8IS0tIEdlbmVyYXRvcjogU2tldGNoIDQyICgzNjc4MSkgLSBodHRwOi8vd3d3LmJvaGVtaWFuY29kaW5nLmNvbS9za2V0Y2ggLS0+CiAgICA8dGl0bGU+ZGFuZ2VyPC90aXRsZT4KICAgIDxkZXNjPkNyZWF0ZWQgd2l0aCBTa2V0Y2guPC9kZXNjPgogICAgPGRlZnM+PC9kZWZzPgogICAgPGcgaWQ9IlBhZ2UtMSIgc3Ryb2tlPSJub25lIiBzdHJva2Utd2lkdGg9IjEiIGZpbGw9Im5vbmUiIGZpbGwtcnVsZT0iZXZlbm9kZCI+CiAgICAgICAgPHBvbHlnb24gaWQ9ImRhbmdlciIgZmlsbD0iIzAwMDAwMCIgZmlsbC1ydWxlPSJub256ZXJvIiBwb2ludHM9IjYgMSAxMiAwIDggOCAxNiA2IDguNjA1IDE5LjkwMyAxMSAyMCA2IDI0IDUgMTggNi45NTUgMTkuMzc5IDkgMTIgMCAxNSI+PC9wb2x5Z29uPgogICAgPC9nPgo8L3N2Zz4=',
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

        add_filter('acf/fields/post_object/query/name=alarm_connection', array($this, 'alarmConnection'), 10, 3);
        add_filter('acf/fields/post_object/result/name=alarm_connection', array($this, 'alarmConnectionResult'), 10, 4);
    }

    /**
     * Arguments of the query to get alarm connections
     * @param  array  $args   Arguments
     * @param  array  $field  Acf field
     * @param  int    $postId The current postid
     * @return array          Modified arguments
     */
    public function alarmConnection($args, $field, $postId)
    {
        $args['date_query'] = array(
            array(
                'after'     => 'midnight 2 days ago',  // or '-2 days'
                'inclusive' => true,
            )
        );

        $args['orderby'] = 'date';
        $args['order'] = 'DESC';

        return $args;
    }

    /**
     * Add info to the title of the alarm in dropdown for connections
     * @param  string $title   Default title
     * @param  WP_Post $post   Post object for the alarm
     * @param  array   $field  Acf Field
     * @param  int     $postId Current posts id
     * @return string          Modified title
     */
    public function alarmConnectionResult($title, $post, $field, $postId)
    {
        $address = '';
        if (get_field('address', $post->ID)) {
            $address = ' (' . get_field('address', $post->ID) . ')';
        }

        return date('Y-m-d H:i', strtotime($post->post_date)) . ': <strong>' . $title . '</strong>' . $address;
    }
}
