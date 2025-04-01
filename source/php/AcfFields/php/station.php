<?php 


if (function_exists('acf_add_local_field_group')) {

    acf_add_local_field_group(array(
    'key' => 'group_58ca3def60074',
    'title' => 'Station',
    'fields' => array(
        0 => array(
            'default_value' => '',
            'maxlength' => '',
            'placeholder' => '',
            'prepend' => '',
            'append' => '',
            'key' => 'field_58ca3e0e4973f',
            'label' => __('Station ID', 'api-alarm-manager'),
            'name' => 'station_id',
            'type' => 'text',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
        ),
        1 => array(
            'default_value' => '',
            'maxlength' => '',
            'placeholder' => '',
            'prepend' => '',
            'append' => '',
            'key' => 'field_58ca3fe049740',
            'label' => __('Street address', 'api-alarm-manager'),
            'name' => 'street_address',
            'type' => 'text',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '33.3333',
                'class' => '',
                'id' => '',
            ),
        ),
        2 => array(
            'default_value' => '',
            'maxlength' => '',
            'placeholder' => '',
            'prepend' => '',
            'append' => '',
            'key' => 'field_58ca41bb84d75',
            'label' => __('Postal code', 'api-alarm-manager')
            'name' => 'postal_code',
            'type' => 'text',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '33.3333',
                'class' => '',
                'id' => '',
            ),
        ),
        3 => array(
            'default_value' => '',
            'maxlength' => '',
            'placeholder' => '',
            'prepend' => '',
            'append' => '',
            'key' => 'field_58ca403a49741',
            'label' => __('City', 'api-alarm-manager')
            'name' => 'city',
            'type' => 'text',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '33.3333',
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
                'value' => 'station',
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
    'local' => 'php',
    'modified' => 1489671888,
));

}