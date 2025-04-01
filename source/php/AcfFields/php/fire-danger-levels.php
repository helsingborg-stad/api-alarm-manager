<?php 


if (function_exists('acf_add_local_field_group')) {

    acf_add_local_field_group(array(
    'key' => 'group_646dfb458aa04',
    'title' => __('Fire Danger Levels', 'event-manager'),
    'fields' => array(
        0 => array(
            'key' => 'field_646dfb45bc655',
            'label' => __('Fire Danger Levels', 'event-manager'),
            'name' => 'fire_danger_levels',
            'aria-label' => '',
            'type' => 'repeater',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'layout' => 'table',
            'pagination' => 0,
            'min' => 0,
            'max' => 0,
            'collapsed' => '',
            'button_label' => __('Add Row', 'event-manager'),
            'rows_per_page' => 20,
            'acfe_repeater_stylised_button' => 0,
            'sub_fields' => array(
                0 => array(
                    'key' => 'field_646dfb6bbc656',
                    'label' => __('Place', 'event-manager'),
                    'name' => 'place',
                    'aria-label' => '',
                    'type' => 'taxonomy',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'taxonomy' => 'place',
                    'add_term' => 1,
                    'save_terms' => 0,
                    'load_terms' => 0,
                    'return_format' => 'id',
                    'field_type' => 'select',
                    'allow_null' => 0,
                    'multiple' => 0,
                    'parent_repeater' => 'field_646dfb45bc655',
                    'bidirectional_target' => array(
                    ),
                ),
                1 => array(
                    'key' => 'field_646dfbbcbc657',
                    'label' => __('Level', 'event-manager'),
                    'name' => 'level',
                    'aria-label' => '',
                    'type' => 'radio',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'choices' => array(
                        1 => __('No fire ban', 'event-manager'),
                        2 => __('Fire ban', 'event-manager'),
                        3 => __('Strict fire ban', 'event-manager'),
                    ),
                    'default_value' => 1,
                    'return_format' => 'value',
                    'allow_null' => 0,
                    'other_choice' => 0,
                    'layout' => 'horizontal',
                    'save_other_choice' => 0,
                    'parent_repeater' => 'field_646dfb45bc655',
                ),
            ),
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'options_page',
                'operator' => '==',
                'value' => 'fire-danger-levels',
            ),
        ),
    ),
    'menu_order' => 0,
    'position' => 'normal',
    'style' => 'seamless',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
    'active' => true,
    'description' => '',
    'show_in_rest' => 0,
    'acfe_display_title' => '',
    'acfe_autosync' => '',
    'acfe_form' => 0,
    'acfe_meta' => '',
    'acfe_note' => '',
));

}