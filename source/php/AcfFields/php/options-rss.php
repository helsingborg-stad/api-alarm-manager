<?php

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key'                   => 'group_591aafe3cd78b',
    'title'                 => __('RSS', 'api-alarm-manager'),
    'fields'                => array(
        0 => array(
            'default_value'     => '',
            'placeholder'       => '',
            'key'               => 'field_591aaff0aca87',
            'label'             => __('RSS Permalink', 'api-alarm-manager'),
            'name'              => 'rss_permalink',
            'type'              => 'url',
            'instructions'      => __('The hash for specific alarms will be appended automatically', 'api-alarm-manager'),
            'required'          => 0,
            'conditional_logic' => 0,
            'wrapper'           => array(
                'width' => '',
                'class' => '',
                'id'    => '',
            ),
        ),
    ),
    'location'              => array(
        0 => array(
            0 => array(
                'param'    => 'options_page',
                'operator' => '==',
                'value'    => 'alarm-manager-options',
            ),
        ),
    ),
    'menu_order'            => 0,
    'position'              => 'normal',
    'style'                 => 'default',
    'label_placement'       => 'top',
    'instruction_placement' => 'label',
    'hide_on_screen'        => '',
    'active'                => 1,
    'description'           => '',
    ));
}
