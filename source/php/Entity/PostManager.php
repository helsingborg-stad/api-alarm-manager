<?php

namespace ApiAlarmManager\Entity;

abstract class PostManager
{
    /**
     * Post object sticky values
     */
    public $post_type = null;
    public $post_status = 'publish';

    /**
     * Keys that counts as post object properties
     * Any other key will be treated as meta properties
     * @var array
     */
    public $allowedPostFields = array(
        'ID',
        'post_author',
        'post_date',
        'post_date_gmt',
        'post_content',
        'post_content_filtered',
        'post_title',
        'post_excerpt',
        'post_status',
        'comment_status',
        'ping_status',
        'post_password',
        'post_name',
        'to_ping',
        'pinged',
        'post_modified',
        'post_modified_gmt',
        'post_parent',
        'menu_order',
        'post_mime_type',
        'guid',
        'tax_input'
    );

    /**
     * Constructor
     * @param array $postData Post object fields as array
     * @param array $metaData Post meta as array
     */
    public function __construct($postData = array(), $metaData = array())
    {
        if (is_null($this->post_type)) {
            throw new \Exception('You need to specify a post type by setting the class property $postType');
            exit;
        }

        // Add post data as separate object parameters
        foreach ($postData as $key => $value) {
            $this->{$key} = $value;
        }

        // Add meta data as separate object parameters
        foreach ($metaData as $key => $value) {
            $this->{$key} = $value;
        }
    }

    /**
     * Save hooks
     * @param  string $postType Saved post type
     * @param  object $object   Saved object
     * @return void
     */
    public function beforeSave()
    {
        //No code
    }

    public function afterSave()
    {
        return true;
    }

    /**
     * Get  posts
     * @param  integer        $count       Number of posts to get
     * @param  array          $metaQuery   Meta query
     * @param  string         $postType    Post type
     * @param  array|string   $postStatus  Post status
     * @return array                       Found posts
     */
    public static function get($count, $metaQuery, $postType, $postStatus = array('publish', 'draft'))
    {
        $args = array(
            'posts_per_page' => $count,
            'post_type'      => $postType,
            'orderby'        => 'date',
            'order'          => 'DESC'
        );

        $args['post_status'] = (array)$postStatus;

        if (is_array($metaQuery)) {
            $args['meta_query'] = $metaQuery;
        }

        $posts = get_posts($args);

        if ($count == 1 && isset($posts[0])) {
            $posts = $posts[0];
        }

        return $posts;
    }

    /**
     * Remove all values that are empty, except the value 0
     * @param  $metaValue
     * @return $metaValue
     */
    public function removeEmpty($metaValue)
    {
        if (is_array($metaValue)) {
            return $metaValue;
        }

        return $metaValue !== null && $metaValue !== false && $metaValue !== '';
    }

    /**
     * Saves the event and it's data
     * @var nonSyncFields  Fields to discard when updating existing post
     * @return integer The inserted/updated post id
     */
    public function save(array $nonSyncFields = array())
    {
        $this->beforeSave();

        $nonSyncFields = array_filter($nonSyncFields, function ($item) {
            return strtolower($item) !== 'id';
        });

        // Arrays for holding save data
        $post = array();
        $meta = array();
        $post['post_status'] = $this->post_status;

        // Get the default class variables and set it's keys to forbiddenKeys
        $defaultData    = get_class_vars(get_class($this));
        $forbiddenKeys  = array_keys($defaultData);

        $data = array_filter(get_object_vars($this), function ($item) use ($forbiddenKeys) {
            return !in_array($item, $forbiddenKeys);
        }, ARRAY_FILTER_USE_KEY);

        // If data key is allowed post field add to $post else add to $meta
        foreach ($data as $key => $value) {
            if (in_array($key, $this->allowedPostFields)) {
                $post[$key] = $value;
                continue;
            }

            $meta[$key] = $value;
        }

        // Do not include null values in meta
        $meta = array_filter($meta, array($this, 'removeEmpty'));

        $post['post_type'] = $this->post_type;
        $post['meta_input'] = $meta;

        // Check if duplicate by matching "_alarm_manager_uid" meta value
        if (isset($meta['_alarm_manager_uid'])) {
            $duplicate = self::get(
                1,
                array(
                    'relation' => 'OR',
                    array(
                        'key' => '_alarm_manager_uid',
                        'value' => $meta['_alarm_manager_uid'],
                        'compare' => '='
                    )
                ),
                $this->post_type
            );
        } else {
            $duplicate = false;
        }

        // Update if duplicate
        if ($duplicate && isset($duplicate->ID)) {
            $post = array_filter($post, function ($key) use ($nonSyncFields) {
                return !in_array($key, $nonSyncFields);
            }, ARRAY_FILTER_USE_KEY);

            $post['ID']     = $duplicate->ID;
            $this->ID       = wp_update_post($post);
        } else {
            // Create if not duplicate
            $this->ID = wp_insert_post($post, true);
        }

        return $this->afterSave();
    }
}
