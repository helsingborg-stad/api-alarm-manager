<?php 


if (function_exists('acf_add_local_field_group')) {

    acf_add_local_field_group(array(
    'key' => 'group_591ab89a6e9f5',
    'title' => __('Templates', 'api-alarm-manager'),
    'fields' => array(
        0 => array(
            'sub_fields' => array(
                0 => array(
                    'default_value' => '',
                    'maxlength' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'key' => 'field_591ab8b0f6700',
                    'label' => __('Template name', 'api-alarm-manager'),
                    'name' => 'template_name',
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
                1 => array(
                    'default_value' => '',
                    'maxlength' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'key' => 'field_591ab8d4f6702',
                    'label' => __('Title', 'api-alarm-manager'),
                    'name' => 'title',
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
                2 => array(
                    'tabs' => 'all',
                    'toolbar' => 'full',
                    'media_upload' => 1,
                    'default_value' => '',
                    'delay' => 0,
                    'key' => 'field_591ab8c1f6701',
                    'label' => __('Message', 'api-alarm-manager'),
                    'name' => 'message',
                    'type' => 'wysiwyg',
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
            'layout' => 'block',
            'button_label' => __('Add template', 'api-alarm-manager'),
            'collapsed' => '',
            'key' => 'field_591ab89ff66ff',
            'label' => __('Templates', 'api-alarm-manager'),
            'name' => 'templates',
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