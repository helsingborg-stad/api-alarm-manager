<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_58ca5ce582e3b',
    'title' => __('Contal FTP', 'event-manager'),
    'fields' => array(
        0 => array(
            'placement' => 'left',
            'endpoint' => 0,
            'key' => 'field_58ca63e750b0b',
            'label' => __('FTP Connection', 'event-manager'),
            'name' => '',
            'type' => 'tab',
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
            'message' => __('The FTP-connection should be set to where the alarm xml-files are hosted.', 'event-manager'),
            'esc_html' => 0,
            'new_lines' => 'wpautop',
            'key' => 'field_58ca5cfcafada',
            'label' => __('FTP Connection', 'event-manager'),
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
        2 => array(
            'default_value' => 0,
            'message' => '',
            'ui' => 0,
            'ui_on_text' => '',
            'ui_off_text' => '',
            'key' => 'field_58ca5d6bafadc',
            'label' => __('Enable contal FTP', 'event-manager'),
            'name' => 'ftp_enabled',
            'type' => 'true_false',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
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
            'key' => 'field_58ca5d45afadb',
            'label' => __('Server', 'event-manager'),
            'name' => 'server',
            'type' => 'text',
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => array(
                0 => array(
                    0 => array(
                        'field' => 'field_58ca5d6bafadc',
                        'operator' => '==',
                        'value' => '1',
                    ),
                ),
            ),
            'wrapper' => array(
                'width' => '33.3333',
                'class' => '',
                'id' => '',
            ),
        ),
        4 => array(
            'default_value' => '',
            'maxlength' => '',
            'placeholder' => '',
            'prepend' => '',
            'append' => '',
            'key' => 'field_58ca5d9eafadd',
            'label' => __('Username', 'event-manager'),
            'name' => 'username',
            'type' => 'text',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => array(
                0 => array(
                    0 => array(
                        'field' => 'field_58ca5d6bafadc',
                        'operator' => '==',
                        'value' => '1',
                    ),
                ),
            ),
            'wrapper' => array(
                'width' => '33.3333',
                'class' => '',
                'id' => '',
            ),
        ),
        5 => array(
            'default_value' => '',
            'maxlength' => '',
            'placeholder' => '',
            'prepend' => '',
            'append' => '',
            'key' => 'field_58ca5dacafade',
            'label' => __('Password', 'event-manager'),
            'name' => 'password',
            'type' => 'text',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => array(
                0 => array(
                    0 => array(
                        'field' => 'field_58ca5d6bafadc',
                        'operator' => '==',
                        'value' => '1',
                    ),
                ),
            ),
            'wrapper' => array(
                'width' => '33.3333',
                'class' => '',
                'id' => '',
            ),
        ),
        6 => array(
            'layout' => 'horizontal',
            'choices' => array(
                'passive' => __('Passive mode', 'event-manager'),
                'active' => __('Active mode', 'event-manager'),
            ),
            'default_value' => 'passive',
            'other_choice' => 0,
            'save_other_choice' => 0,
            'allow_null' => 0,
            'return_format' => 'value',
            'key' => 'field_58ca5dfa7eead',
            'label' => __('FTP mode', 'event-manager'),
            'name' => 'ftp_mode',
            'type' => 'radio',
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => array(
                0 => array(
                    0 => array(
                        'field' => 'field_58ca5d6bafadc',
                        'operator' => '==',
                        'value' => '1',
                    ),
                ),
            ),
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
        ),
        7 => array(
            'default_value' => '/',
            'maxlength' => '',
            'placeholder' => '',
            'prepend' => '',
            'append' => '',
            'key' => 'field_58ca67ba79c52',
            'label' => __('FTP folder', 'event-manager'),
            'name' => 'ftp_folder',
            'type' => 'text',
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => array(
                0 => array(
                    0 => array(
                        'field' => 'field_58ca5d6bafadc',
                        'operator' => '==',
                        'value' => '1',
                    ),
                ),
            ),
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
        ),
        8 => array(
            'placement' => 'left',
            'endpoint' => 0,
            'key' => 'field_58ca66e350b0c',
            'label' => __('FTP Auto import', 'event-manager'),
            'name' => '',
            'type' => 'tab',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
        ),
        9 => array(
            'message' => __('The FTP auto import will run in intervals according to the below setting. The auto import will automatically check for new alarms in the contal ftp and import these.', 'event-manager'),
            'esc_html' => 0,
            'new_lines' => 'wpautop',
            'key' => 'field_58ca678179c51',
            'label' => __('FTP Auto import', 'event-manager'),
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
        10 => array(
            'default_value' => 0,
            'message' => '',
            'ui' => 0,
            'ui_on_text' => '',
            'ui_off_text' => '',
            'key' => 'field_58ca670c50b0d',
            'label' => __('Activate auto import', 'event-manager'),
            'name' => 'auto_import_active',
            'type' => 'true_false',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
        ),
        11 => array(
            'default_value' => 10,
            'min' => 1,
            'max' => '',
            'step' => '',
            'placeholder' => '',
            'prepend' => '',
            'append' => __('minutes', 'event-manager'),
            'key' => 'field_58ca672650b0e',
            'label' => __('Import interval', 'event-manager'),
            'name' => 'import_interval',
            'type' => 'number',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => array(
                0 => array(
                    0 => array(
                        'field' => 'field_58ca670c50b0d',
                        'operator' => '==',
                        'value' => '1',
                    ),
                ),
            ),
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