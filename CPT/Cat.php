<?php

/**
 * Class Cat
 *
 * Handles the creation of a "Cat" custom post type
 */
class Cat
{
    /**
     * The key used by the cat post type.
     *
     * @var string
     */
    const POST_TYPE = 'cat';
    const META = [
        'father', 'mother', 'gender'
    ];

    public static function register()
    {
        $instance = new self;
        add_action('init', [$instance, 'registerPostType']);
        add_action('init', [$instance, 'registerTaxonomy']);
        add_action('add_meta_boxes', [$instance, 'registerMetaboxes']);
        add_action('save_post_' . self::POST_TYPE, [$instance, 'save']);

        // Do something else related to "Cat" post type
    }

    public function registerPostType()
    {
        register_post_type( self::POST_TYPE, [
            'labels' => [
                'name' => __( 'Cats' ),
                'singular_name' => __( 'Cat' ),
                'search_items' =>  __( 'Search Cats' ),
                'all_items' => __( 'All Cats' ),
                'parent_item' => __( 'Parent Cat' ),
                'parent_item_colon' => __( 'Parent Cat:' ),
                'edit_item' => __( 'Edit Cat' ),
                'update_item' => __( 'Update Cat' ),
                'add_new_item' => __( 'Add New Cat' ),
                'new_item_name' => __( 'New Cat Name' ),
                'menu_name' => __( 'Cats' ),
            ],
            'public' => true,
            'has_archive' => true,
            'publicly_queryable' => true,
        ]);
    }

    public function registerTaxonomy()
    {
        // Таксономия для разделения котов на пометы
        $labels = array(
            'name' => _x( 'Letters', 'taxonomy general name' ),
            'singular_name' => _x( 'Letter', 'taxonomy singular name' ),
            'search_items' =>  __( 'Search Letters' ),
            'all_items' => __( 'All Letters' ),
            'parent_item' => __( 'Parent Letter' ),
            'parent_item_colon' => __( 'Parent Letter:' ),
            'edit_item' => __( 'Edit Letter' ),
            'update_item' => __( 'Update Letter' ),
            'add_new_item' => __( 'Add New Letter' ),
            'new_item_name' => __( 'New Letter Name' ),
            'menu_name' => __( 'Letters' ),
        );

        // Now register the taxonomy
        register_taxonomy('letters',array(self::POST_TYPE), array(
            'hierarchical' => true,
            'labels' => $labels,
            'show_ui' => true,
            'show_in_rest' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array( 'slug' => 'letter' ),
            'publicly_queryable' => true,
        ));
    }

    public function registerMetaBoxes()
    {
        // Cat metabox
        add_meta_box(
            'cat-info-metabox',
            __('Information', 'perfect-cat'),
            [$this, 'infoMetabox'],
            self::POST_TYPE
        );
    }

    public function infoMetabox()
    {
        load_template(plugin_dir_path(__FILE__) . '/templates/metabox.php');
    }

    public static function save()
    {
        global $post;

        foreach (self::META as $key) {
            if (array_key_exists($key, $_POST)) {
                update_post_meta(
                    $post->ID,
                    self::POST_TYPE . '_' . $key . '_meta_key',
                    $_POST[$key]
                );
            }
        }
    }
}