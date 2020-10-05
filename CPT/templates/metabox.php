<?php
/**
 * The template for displaying the metabox cat post type
 *
 * Contains the closing of the #content div and all content after.
 *
 * @package WordPress
 * @subpackage perfectcat
 */
$gender = get_post_meta($post->ID, '_cat_gender_meta_key', true);
$father = get_post_meta($post->ID, '_cat_father_meta_key', true);
$mother = get_post_meta($post->ID, '_cat_mother_meta_key', true);
$args = array(
'meta_key'     	=> '_cat_gender_meta_key',
'meta_value'   	=> 'male',
'meta_compare' 	=> '=',
'post_type'		=> 'cat',
'post__not_in'	=> [$post->ID]
);
$males_query = new WP_Query( $args );
$args['meta_value'] = 'female';
$females_query = new WP_Query( $args );
?>
<input type="radio" id="male" name="gender" <?= $gender == 'male' ? 'checked' : '' ?> value="male">
<label for="male">Male</label><br>
<input type="radio" id="female" name="gender" <?= $gender == 'female' ? 'checked' : '' ?> value="female">
<label for="female">Female</label><br><br>

<label for="father">Father</label>
<select name="father" id="father" class="postbox">
    <option value="">Select parent...</option>
    <?php
    if ( $males_query->have_posts() ) {
        while ( $males_query->have_posts() ) {
            $males_query->the_post();
            echo '<option value="' . get_the_id() . '"' . selected($father, get_the_id()) . '>' . get_the_title() . '</option>';
        }
    }
    ?>
</select>

<label for="mother">Mother</label>
<select name="mother" id="mother" class="postbox">
    <option value="">Select parent...</option>
    <?php
    if ( $females_query->have_posts() ) {
        while ( $females_query->have_posts() ) {
            $females_query->the_post();
            echo '<option value="' . get_the_id() . '"' . selected($mother, get_the_id()) . '>' . get_the_title() . '</option>';
        }
    }
    ?>
</select>