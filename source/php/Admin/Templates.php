<?php

namespace ApiAlarmManager\Admin;

class Templates
{
    public function __construct()
    {
        add_action('edit_form_after_title', array($this, 'templateSelector'));
    }

    public function templateSelector()
    {
        global $post;

        if (!in_array($post->post_type, array('big-disturbance', 'small-disturbance'))) {
            return;
        }

        $templates = get_field('templates', 'option');

        echo '<style scoped>
        label[for="disturbance-template"] {
            font-size: 16px;
            display: block;
            margin-top: 20px;
        }

        select#disturbance-template {
            margin-top: 3px;
            font-size: 16px;
            padding: 8px;
            height: auto;
        }
        </style>';

        echo '<label for="disturbance-template">' . __('Create from template', 'api-alarm-manager') . ':</label>';
        echo '<select name="disturbance-template" id="disturbance-template" class="widefat"><option value="">' . __('Select template…', 'api-alarm-manager') . '</option>';

        foreach ($templates as $template) {
            echo '<option data-title="' . $template['title'] . '" data-message="' . $template['message'] . '">' . $template['template_name'] . '</option>';
        }

        echo '</select>';
    }
}
