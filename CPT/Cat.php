<?php


namespace CPT;


/**
 * Abstract class custom post type PostType
 */
class PostType
{
    public static function register()
    {
        $class_name = get_called_class();
        add_action('init', [$class_name, 'post_type']);
        add_action('save_post', [$class_name, 'save'], 1, 2);
    }

    /**
     * Abstract method add post type
     */
    public static function post_type(){}

    /**
     * Abstract method create custom metaboxes
     */
    public static function add_metaboxes(){}

    /**
     * Save the metabox data
     */
    static function save($post_id, $post)
    {
        $class_name = get_called_class();

        // Return if the user doesn't have edit permissions.
        if (!current_user_can('edit_post', $post_id)) {
            return $post_id;
        }

        // Verify this came from the our screen and with proper authorization,
        // because save_post can be triggered at other times.
        //if (!isset($_POST['cat_fields']) || !wp_verify_nonce($_POST['cat_fields'], basename(__FILE__))) {
        //    return $post_id;
        //}

        $meta = [];

        // Now that we're authenticated, time to save the data.
        // This sanitizes the data from the field and saves it into an array $meta.
        foreach ($class_name::META as $key){
            if (isset($_POST[$key]))
                $meta[$key] = esc_textarea($_POST[$key]);
        }

        // Cycle through the $meta array.
        // Note, in this example we just have one item, but this is helpful if you have multiple.
        foreach ($meta as $key => $value) :

            // Don't store custom data twice
            if ('revision' === $post->post_type) {
                return;
            }

            if (get_post_meta($post_id, $key, false)) {
                // If the custom field already has a value, update it.
                update_post_meta($post_id, $key, $value);
            } else {
                // If the custom field doesn't have a value, add it.
                add_post_meta($post_id, $key, $value);
            }

            if (!$value) {
                // Delete the meta key if there's no value
                delete_post_meta($post_id, $key);
            }

        endforeach;

    }

    public static function type(){
        $class_name = get_called_class();
        return $class_name::POST_TYPE;
    }

}

/**
 * Class Cat
 *
 * Handles the creation of a "Cat" custom post type
 */
class Cat extends PostType
{
    const POST_TYPE = 'cat';
    const META = [
        'gender',
        'birthday',
        'father', 'mother',
    ];

    static function post_type()
    {

        $labels = array(
            'name' => __('Cats'),
            'singular_name' => __('Cat'),
            'add_new' => __('Add New Cat'),
            'add_new_item' => __('Add New Cat'),
            'edit_item' => __('Edit Cat'),
            'new_item' => __('Add New Cat'),
            'view_item' => __('View Cat'),
            'search_items' => __('Search Cat'),
            'not_found' => __('No cats found'),
            'not_found_in_trash' => __('No cats found in trash')
        );

        $supports = array(
            'title',
            'editor',
            'thumbnail',
            'revisions',
        );

        $args = array(
            'labels' => $labels,
            'supports' => $supports,
            'public' => true,
            'capability_type' => 'post',
            'rewrite' => array('slug' => 'cats'),
            'has_archive' => true,
            'menu_position' => 30,
            'menu_icon' => 'dashicons-pets',
            'register_meta_box_cb' => [self::class, 'add_metaboxes'],
        );

        register_post_type(self::type(), $args);

    }

    static function add_metaboxes()
    {
        add_meta_box(
            'cat_gender',
            'Cat gender',
            [self::class, 'cat_gender'],
            self::type(),
            'side',
            'default'
        );

        add_meta_box(
            'cat_birthday',
            'Cat birthday',
            [self::class, 'cat_birthday'],
            self::type(),
            'side',
            'default'
        );

        add_meta_box(
            'cat_parents',
            'Cat parents',
            [self::class, 'cat_parents'],
            self::type(),
            'side',
            'default'
        );

        wp_reset_postdata();
    }

    static function cat_birthday()
    {
        global $post;

        // Get the gender data if it's already been entered
        $date = get_post_meta($post->ID, 'birthday', true);

        // Output the field
        echo '<input type="text" class="datepicker" name="birthday" value="' . $date . '"/>';

        self::datepicker_js();
    }

    static function datepicker_js(){
        // подключаем все необходимые скрипты: jQuery, jquery-ui, datepicker
        wp_enqueue_script('jquery-ui-datepicker');

        // подключаем нужные css стили
        wp_enqueue_style('jqueryui', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css', false, null );

        // инициализируем datepicker
        if( is_admin() )
            add_action('admin_footer', [self::class, 'init_datepicker'], 99 ); // для админки
        else
            add_action('wp_footer', [self::class, 'init_datepicker'], 99 ); // для админки

    }

    static function init_datepicker(){
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function($){
                'use strict';
                // настройки по умолчанию. Их можно добавить в имеющийся js файл,
                // если datepicker будет использоваться повсеместно на проекте и предполагается запускать его с разными настройками
                $.datepicker.setDefaults({
                    closeText: 'Закрыть',
                    prevText: '<Пред',
                    nextText: 'След>',
                    currentText: 'Сегодня',
                    monthNames: ['Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'],
                    monthNamesShort: ['Янв','Фев','Мар','Апр','Май','Июн','Июл','Авг','Сен','Окт','Ноя','Дек'],
                    dayNames: ['воскресенье','понедельник','вторник','среда','четверг','пятница','суббота'],
                    dayNamesShort: ['вск','пнд','втр','срд','чтв','птн','сбт'],
                    dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],
                    weekHeader: 'Нед',
                    dateFormat: 'dd-mm-yy',
                    firstDay: 1,
                    showAnim: 'slideDown',
                    isRTL: false,
                    showMonthAfterYear: false,
                    yearSuffix: ''
                } );

                // Инициализация
                $('.datepicker').datepicker({ dateFormat: 'dd/mm/yy' });
                // можно подключить datepicker с доп. настройками так:
                /*
                $('input[name*="date"]').datepicker({
                    dateFormat : 'yy-mm-dd',
                    onSelect : function( dateText, inst ){
            // функцию для поля где указываются еще и секунды: 000-00-00 00:00:00 - оставляет секунды
            var secs = inst.lastVal.match(/^.*?\s([0-9]{2}:[0-9]{2}:[0-9]{2})$/);
            secs = secs ? secs[1] : '00:00:00'; // только чч:мм:сс, оставим часы минуты и секунды как есть, если нет то будет 00:00:00
            $(this).val( dateText +' '+ secs );
                    }
                });
                */
            });
        </script>
        <?php
    }

    /**
     * Output the HTML for the metabox.
     */
    static function cat_gender()
    {
        global $post;

        // Nonce field to validate form request came from current site
        wp_nonce_field(basename(__FILE__), 'cat_fields');

        // Get the gender data if it's already been entered
        $gender = get_post_meta($post->ID, 'gender', true);

        // Output the field
        echo sprintf('<input type="radio" id="male" name="gender" %s value="male">
        <label for="male">Male</label><br>
        <input type="radio" id="female" name="gender" %s value="female">
        <label for="female">Female</label><br><br>', $gender == 'male' ? 'checked' : '', $gender == 'female' ? 'checked' : '');
    }

    static function cat_parents(){
        global $post;

        $father = get_post_meta($post->ID, 'father', true);
        $mother = get_post_meta($post->ID, 'mother', true);

        global $wpdb;

        $q = <<<TEXT
            SELECT post.ID, post.post_title, post.post_type, meta.meta_value as gender FROM $wpdb->posts AS post
            LEFT JOIN $wpdb->postmeta as meta 
            ON meta.post_id = post.id
            WHERE meta.meta_key = 'gender' AND post.id != $post->ID AND post.post_type = 'cat'
TEXT;

        $cats = $wpdb->get_results($q, ARRAY_A);
        $select1 = '';
        $select2 = '';

        if ($cats) {
            foreach ($cats as $cat){
                $selected = '';
                switch ($cat['gender']){
                    case 'male'  : $selected .= selected($father, $cat['ID'], false); break;
                    case 'female': $selected .= selected($mother, $cat['ID'], false); break;
                    default: break;
                }
                $option = '<option value="' . $cat['ID'] . '" ' . $selected . '>' . $cat['post_title'] . '</option>';
                switch ($cat['gender']){
                    case 'male'  : $select1 .= $option; break;
                    case 'female': $select2 .= $option; break;
                    default: break;
                }
            }
        }

        echo '<label for="father">Father</label>
        <select name="father" id="father" class="postbox">
            <option value="">Select parent...</option>' . $select1 . '</select>
        <label for="mother">Mother</label>
        <select name="mother" id="mother" class="postbox">
            <option value="">Select parent...</option>' . $select2 . '</select>';
    }
}

class Litter extends PostType
{

    const POST_TYPE = 'litter';
    const META = ['father', 'mother', 'birthday'];

    static function post_type()
    {

        $labels = array(
            'name' => __('Litters'),
            'singular_name' => __('Litter'),
            'add_new' => __('Add New Litter'),
            'add_new_item' => __('Add New Litter'),
            'edit_item' => __('Edit Litter'),
            'new_item' => __('Add New Litter'),
            'view_item' => __('View Litter'),
            'search_items' => __('Search Litter'),
            'not_found' => __('No litters found'),
            'not_found_in_trash' => __('No litters found in trash')
        );

        $supports = array(
            'title',
            'editor',
            'thumbnail',
            'revisions',
        );

        $args = array(
            'labels' => $labels,
            'supports' => $supports,
            'public' => true,
            'capability_type' => 'post',
            'rewrite' => array('slug' => 'litters'),
            'has_archive' => true,
            'menu_position' => 30,
            'menu_icon' => 'dashicons-awards',
            'register_meta_box_cb' => [self::class, 'add_metaboxes'],
        );

        register_post_type(self::type(), $args);

    }

    static function add_metaboxes()
    {
        add_meta_box(
            'litter_parents',
            'Litter parents',
            [Cat::class, 'cat_parents'],
            self::type(),
            'side',
            'default'
        );

        add_meta_box(
            'litter_birthday',
            'Litter birthday',
            [Cat::class, 'cat_birthday'],
            self::type(),
            'side',
            'default'
        );

        wp_reset_postdata();
    }
}