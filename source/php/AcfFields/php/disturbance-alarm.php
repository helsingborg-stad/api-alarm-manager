<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_58cf8618bda6a',
    'title' => __('Alarm', 'event-manager'),
    'fields' => array(
        0 => array(
            'post_type' => array(
                0 => 'alarm',
            ),
            'taxonomy' => array(
            ),
            'allow_null' => 0,
            'multiple' => 1,
            'return_format' => 'id',
            'ui' => 1,
            'key' => 'field_58cf862dd2cfc',
            'label' => __('Alarm connection', 'event-manager'),
            'name' => 'alarm_connection',
            'type' => 'post_object',
            'instructions' => __('If this distrurbance could be connected to specific alarm(s) - add them here.', 'event-manager'),
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'post_type',
                'operator' => '==',
                'value' => 'big-disturbance',
            ),
        ),
        1 => array(
            0 => array(
                'param' => 'post_type',
                'operator' => '==',
                'value' => 'small-disturbance',
            ),
        ),
    ),
    'menu_order' => 0,
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
    'active' => 1,
    'description' => '',
));
}