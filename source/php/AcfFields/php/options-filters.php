<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_58ca5f387fb86',
    'title' => __('Filters', 'event-manager'),
    'fields' => array(
        0 => array(
            'message' => __('Alarm filters are used to avoid importing of alarms containing unwanted keywords. For example: Suicide Note: Adding a new filter keyword will remove any existing alarms containing the keyword.', 'event-manager'),
            'esc_html' => 0,
            'new_lines' => 'wpautop',
            'key' => 'field_58ca5f3d8b5ea',
            'label' => __('About filters', 'event-manager'),
            'name' => '',
            'type' => 'message',
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
            'sub_fields' => array(
                0 => array(
                    'default_value' => '',
                    'maxlength' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'key' => 'field_58ca5fae8b5ec',
                    'label' => __('Keyword', 'event-manager'),
                    'name' => 'keyword',
                    'type' => 'text',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                ),
            ),
            'min' => 0,
            'max' => 0,
            'layout' => 'table',
            'button_label' => __('Add keyword', 'event-manager'),
            'collapsed' => '',
            'key' => 'field_58ca5f798b5eb',
            'label' => __('Filters', 'event-manager'),
            'name' => 'alarm_filters',
            'type' => 'repeater',
            'instructions' => '',
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
                'param' => 'options_page',
                'operator' => '==',
                'value' => 'alarm-manager-options',
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