<?php

namespace ApiAlarmManager\Entity;

/**
 * Default get and save functions
 */

class ApiFields
{
    /**
     * Returning a numeric value formatted by acf if exist
     * @return  int, float, null
     * @version 0.3.0 creating consumer accessable meta values.
     */
    public function numericGetCallBack($object, $field_name, $request, $formatted = true)
    {
        $return_value = self::getFieldGetMetaData($object, $field_name, $request);

        if (is_numeric($return_value)) {
            return $return_value;
        }

        return null;
    }

    /**
     * Returning a numeric value formatted by acf if exist
     * @return  int, float, null
     * @version 0.3.0 creating consumer accessable meta values.
     */
    public function boolGetCallBack($object, $field_name, $request, $formatted = true)
    {
        $return_value = self::getFieldGetMetaData($object, $field_name, $request);

        if (is_numeric($return_value) ||is_bool($return_value)) {
            return (bool) $return_value;
        }

        return false;
    }

    /**
     * Returning a numeric value
     * @return  int, float, null
     * @version 0.3.0 creating consumer accessable meta values.
     */
    public function unformattedNumericGetCallBack($object, $field_name, $request)
    {
        return $this->numericGetCallBack($object, $field_name, $request, false);
    }

    /**
     * Returning a string value formatted by acf if exist
     * @return  string, null
     * @version 0.3.0 creating consumer accessable meta values.
     */
    public function stringGetCallBack($object, $field_name, $request, $formatted = true)
    {
        $return_value = self::getFieldGetMetaData($object, $field_name, $request, $formatted);

        if (is_string($return_value) && !empty($return_value)) {
            return $return_value;
        }

        return null;
    }

    /**
     * Returning a string value
     * @return  string, null
     * @version 0.3.0 creating consumer accessable meta values.
     */
    public function unformattedStringGetCallBack($object, $field_name, $request)
    {
        return $this->stringGetCallBack($object, $field_name, $request, false);
    }

    /**
     * Returning a object formatted by acf if exist
     * @return  object, null
     * @version 0.3.0 creating consumer accessable meta values.
     */
    public function objectGetCallBack($object, $field_name, $request, $formatted = true)
    {
        $return_value = self::getFieldGetMetaData($object, $field_name, $request);

        if (is_array($return_value)||is_object($return_value) && !empty($return_value)) {
            return $return_value;
        }

        return null;
    }

    /**
     * Returning a object
     * @return  object, null
     * @version 0.3.0 creating consumer accessable meta values.
     */
    public function unformattedObjectGetCallBack($object, $field_name, $request)
    {
        return $this->objectGetCallBack($object, $field_name, $request, false);
    }

    /**
     * Update a string in database
     * @return  bool
     * @version 0.3.0 creating consumer accessable meta values.
     */
    public function stringUpdateCallBack($value, $object, $field_name)
    {
        if (!$value || !is_string($value)) {
            return;
        }

        return update_post_meta($object->ID, $field_name, strip_tags($value));
    }

    /**
     * Update a int in database
     * @return  bool
     * @version 0.3.0 creating consumer accessable meta values.
     */
    public function numericUpdateCallBack($value, $object, $field_name)
    {
        if (!$value || !is_numeric($value)) {
            return;
        }

        return update_post_meta($object->ID, $field_name, $value);
    }

    /**
     * Update a json-object in database
     * @return  bool
     * @version 0.3.0 creating consumer accessable meta values.
     */
    public function objectUpdateCallBack($value, $object, $field_name)
    {
        if (!$value || !is_object($value) && !is_array($value)) {
            return;
        }

        return update_post_meta($object->ID, $field_name, $value);
    }

    /**
     * Update acf repeater field in database
     * @return  bool
     * @version 0.3.0 creating consumer accessable meta values.
     */
    public function acfUpdateCallBack($value, $object, $field_name)
    {
        global $wpdb;

        if (!$value || !is_object($value) && !is_array($value)) {
            return;
        }

        // Get acf field key
        $field_name = esc_sql("_".$field_name);
        $field_key = $wpdb->get_results("SELECT meta_value FROM $wpdb->postmeta WHERE $wpdb->postmeta.meta_key = '$field_name' LIMIT 1", ARRAY_A);
        $key = $field_key[0]['meta_value'];

        if (preg_match('/field_[0-9]+/', $key)) {
            return update_field($key, $value, $object->ID);
        }

        return;
    }

    /**
     * Returning a formatted or unformatted meta field from database.
     * @return  int, string, object, null, bool
     * @version 0.3.0 creating consumer accessable meta values.
     */
    public static function getFieldGetMetaData($object, $field_name, $request, $formatted = true)
    {
        if (function_exists('get_field') && $formatted) {
            return get_field($field_name, $object['id']);
        }

        return get_post_meta($object['id'], $field_name, true);
    }

    /**
     * Replace id with taxonomy name
     *
     * @param   object  $object      The response object.
     * @param   string  $field_name  The name of the field to add.
     * @param   object  $request     The WP_REST_Request object.
     *
     * @return  object|null
     */
    public function renameTaxonomies($object, $field_name, $request)
    {
        if (! empty($object[$field_name])) {
            $taxonomies = $object[$field_name];
        } else {
            return null;
        }

        foreach ($taxonomies as &$val) {
            $term = get_term($val, $field_name);
            $val  = $term->name;
        }

        return apply_filters($object['type'] . '_taxonomies', $taxonomies);
    }

    /**
     * Replace id with array with taxonomy id, name and slug
     *
     * @param   object  $object      The response object.
     * @param   string  $field_name  The name of the field to add.
     * @param   object  $request     The WP_REST_Request object.
     *
     * @return  object|null
     */
    public function getTaxonomyCallback($object, $field_name, $request)
    {
        if (! empty($object[$field_name])) {
            $taxonomies = $object[$field_name];
        } else {
            return null;
        }

        foreach ($taxonomies as &$val) {
            $term = get_term($val, $field_name);
            $val = array(
                'id'    => $term->term_id,
                'name'  => $term->name,
                'slug'  => $term->slug
            );
        }

        return apply_filters($object['type'] . '_taxonomies', $taxonomies);
    }

    /**
     * Add data / meta data to additional locations field.
     *
     * @param   object  $object      The response object.
     * @param   string  $field_name  The name of the field to add.
     * @param   object  $request     The WP_REST_Request object.
     *
     * @return  object|null
     */
    public function getStation($object, $field_name, $request)
    {
        $returnValue = self::getFieldGetMetaData($object, $field_name, $request);

        if (is_a($returnValue, 'WP_Post')) {
            $stationId = $returnValue->ID;
        } else {
            return null;
        }

        $station = get_post($stationId);

        if (!$station) {
            return null;
        }

        return array(
            'id' => $station->ID,
            'station_id' => get_field('station_id', $station->ID),
            'title' => $station->post_title,
            'content' => $station->post_content,
            'street_address' => get_field('street_address', $station->ID),
            'postal_code' => get_field('postal_code', $station->ID),
            'city' => get_field('city', $station->ID)
        );
    }

    /**
     * Return plaintext field for posts
     *
     * @param   object  $object      The response object.
     * @param   string  $field_name  The name of the field to add.
     * @param   object  $request     The WP_REST_Request object.
     *
     * @return  object|null
     */
    public function addPlaintextField($object, $field_name, $request)
    {
        $object[$field_name]['plain_text'] = strip_tags(html_entity_decode($object[$field_name]['rendered']));
        return $object[$field_name];
    }

    public function stringRenderedPlainGetCallback($object, $field_name, $request)
    {
        $return_value = self::getFieldGetMetaData($object, $field_name, $request);

        return $object[$field_name] = array(
            'rendered' => $return_value,
            'plain_text' => strip_tags(html_entity_decode($return_value))
        );
    }
}
