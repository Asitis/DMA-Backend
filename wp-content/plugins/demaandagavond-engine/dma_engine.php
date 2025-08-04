<?php

/**
 * @package DMA_ENGINE
 * @version 1.0
 */
/*
Plugin Name: DeMaandagavond Engine
Plugin URI: https://www.demaandagavond.nl
Description: Dit is een geknutsel om te kijken of ik custom post types en crap in een plugin kan doen, ipv een theme.
Author: Alex Timmer
Version: 1.0
Author URI: http://www.alextimmer.net


	TOC
	---------------------
	1. Post Type: Alba
	   Thumbnails
	2. Taxonomy: Artiest
	3. Taxonomy: Genre
	4. Taxonomy: Jaar
	5. Taxonomy: Label
	6. Init Custom Fields
	7. CSS Custom Fields
	8. Admin cleanup

*/

if (!function_exists('write_custom_log')) {
    function write_custom_log($message)
    {
        $timestamp = date("Y-m-d H:i:s");
        $log_line = "$timestamp - $message\n";
        file_put_contents(WP_CONTENT_DIR . '/debug.log', $log_line, FILE_APPEND);
    }
}
//write_custom_log('THIS IS THE START OF MY CUSTOM DEBUG');

// Caching API
function dma_add_cache_control_header()
{
    header("Cache-Control: public, max-age=86400");
}

add_action('rest_pre_serve_request', 'dma_add_cache_control_header', 10);

/* -----------------------------------------------------------
----- 1. Post Type: Alba
-------------------------------------------------------------- */
add_action('init', 'dma_create_posttype');
function dma_create_posttype()
{

    $labels = array(
        'name' => 'Alba',
        'singular_name' => 'Album',
        'add_new' => 'Nieuw album',
        'all_items' => 'Alle alba',
        'add_new_item' => 'Voeg nieuw album toe',
        'edit_item' => 'Wijzig album',
        'new_item' => 'Nieuw album',
        'view_item' => 'Bekijk album',
        'search_items' => 'Doorzoek alba',
        'not_found' =>  'Geen alba gevonden',
        'not_found_in_trash' => 'Geen alba gevonden',
        'parent_item_colon' => 'Parent album:',
        'menu_name' => 'Alba'
    );
    $args = array(
        'labels' => $labels,
        'description' => "Alba is het meervoud van Album",
        'public' => true,
        'exclude_from_search' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_rest' => true,
        'show_in_nav_menus' => true,
        'show_in_menu' => true,
        'show_in_admin_bar' => true,
        'menu_position' => 5,
        'menu_icon' => null,
        'capability_type' => 'post',
        'hierarchical' => false,
        'supports' => array('title', 'editor', 'thumbnail'),
        'has_archive' => true,
        'rewrite' => array('slug' => 'album', 'with_front' => true),
        'query_var' => true,
        'can_export' => true,
        'taxonomies' => array('post_tag')
    );
    register_post_type('dma_alba', $args);
}

// Thumbs
add_theme_support('post-thumbnails');
set_post_thumbnail_size(350, 350, true);

/* -----------------------------------------------------------
----- 1a. Post Type: Film
-------------------------------------------------------------- */
add_action('init', 'dma_create_posttype_films');
function dma_create_posttype_films()
{

    $labels = array(
        'name' => 'Films',
        'singular_name' => 'Film',
        'add_new' => 'Nieuwe film',
        'all_items' => 'Alle films',
        'add_new_item' => 'Voeg nieuwe film toe',
        'edit_item' => 'Wijzig film',
        'new_item' => 'Nieuwe film',
        'view_item' => 'Bekijk film',
        'search_items' => 'Doorzoek films',
        'not_found' =>  'Geen films gevonden',
        'not_found_in_trash' => 'Geen films gevonden',
        'parent_item_colon' => 'Parent film:',
        'menu_name' => 'Films'
    );
    $args = array(
        'labels' => $labels,
        'description' => "Films is het meervoud van film",
        'public' => true,
        'exclude_from_search' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_nav_menus' => true,
        'show_in_menu' => true,
        'show_in_admin_bar' => true,
        'menu_position' => 5,
        'menu_icon' => null,
        'capability_type' => 'post',
        'hierarchical' => false,
        'supports' => array('title', 'editor', 'thumbnail'),
        'has_archive' => true,
        'rewrite' => array('slug' => 'film', 'with_front' => true),
        'query_var' => true,
        'can_export' => true,
        'taxonomies' => array('post_tag')
    );
    register_post_type('dma_films', $args);
}

/* -----------------------------------------------------------
----- 1b. Post Type: Game
-------------------------------------------------------------- */
add_action('init', 'dma_create_posttype_games');
function dma_create_posttype_games()
{

    $labels = array(
        'name' => 'Games',
        'singular_name' => 'Game',
        'add_new' => 'Nieuwe game',
        'all_items' => 'Alle games',
        'add_new_item' => 'Voeg nieuwe game toe',
        'edit_item' => 'Wijzig game',
        'new_item' => 'Nieuwe game',
        'view_item' => 'Bekijk game',
        'search_items' => 'Doorzoek games',
        'not_found' =>  'Geen games gevonden',
        'not_found_in_trash' => 'Geen games gevonden',
        'parent_item_colon' => 'Parent game:',
        'menu_name' => 'Games'
    );
    $args = array(
        'labels' => $labels,
        'description' => "Games is het meervoud van game",
        'public' => true,
        'exclude_from_search' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_nav_menus' => true,
        'show_in_menu' => true,
        'show_in_admin_bar' => true,
        'menu_position' => 5,
        'menu_icon' => null,
        'capability_type' => 'post',
        'hierarchical' => false,
        'supports' => array('title', 'editor', 'thumbnail'),
        'has_archive' => true,
        'rewrite' => array('slug' => 'game', 'with_front' => true),
        'query_var' => true,
        'can_export' => true,
        'taxonomies' => array('post_tag')
    );
    register_post_type('dma_games', $args);
}

/* -----------------------------------------------------------
----- 2. Taxonomy: Artiest
-------------------------------------------------------------- */
function create_dma_artiest()
{
    $labels = array(
        'name'                       => _x('Artiesten', 'Taxonomy General Name', 'text_domain'),
        'singular_name'              => _x('Artiest', 'Taxonomy Singular Name', 'text_domain'),
        'menu_name'                  => __('Artiesten', 'text_domain'),
        'all_items'                  => __('Alle artiesten', 'text_domain'),
        'parent_item'                => __('Parent artiest', 'text_domain'),
        'parent_item_colon'          => __('Parent artiest:', 'text_domain'),
        'new_item_name'              => __('Nieuwe artiest', 'text_domain'),
        'add_new_item'               => __('Voeg nieuwe artiest toe', 'text_domain'),
        'edit_item'                  => __('Edit artiest', 'text_domain'),
        'update_item'                => __('Update artiest', 'text_domain'),
        'view_item'                  => __('Bekijk artiest', 'text_domain'),
        'separate_items_with_commas' => __('Scheid artiesten met een komma', 'text_domain'),
        'add_or_remove_items'        => __('Verwijder of voeg artiesten toe', 'text_domain'),
        'choose_from_most_used'      => __('Kies uit veelgebruikte genres', 'text_domain'),
        'popular_items'              => __('Populaire artiesten', 'text_domain'),
        'search_items'               => __('Doorzoek artiesten', 'text_domain'),
        'not_found'                  => __('Niets gevonden', 'text_domain'),
    );
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true,
        'public'                     => true,
        'show_in_rest'                  => true,
        'rest_base'                  => 'artist',
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
    );
    register_taxonomy('artiest', array('dma_alba'), $args);
}
add_action('init', 'create_dma_artiest');

/* -----------------------------------------------------------
----- 3. Taxonomy: Genre
-------------------------------------------------------------- */
function create_dma_genre()
{
    $labels = array(
        'name'                       => _x('Genres', 'Taxonomy General Name', 'text_domain'),
        'singular_name'              => _x('Genre', 'Taxonomy Singular Name', 'text_domain'),
        'menu_name'                  => __('Genres', 'text_domain'),
        'all_items'                  => __('Alle genres', 'text_domain'),
        'parent_item'                => __('Parent genre', 'text_domain'),
        'parent_item_colon'          => __('Parent genre:', 'text_domain'),
        'new_item_name'              => __('Nieuw genre', 'text_domain'),
        'add_new_item'               => __('Voeg nieuw genre toe', 'text_domain'),
        'edit_item'                  => __('Edit genre', 'text_domain'),
        'update_item'                => __('Update genre', 'text_domain'),
        'view_item'                  => __('Bekijk genre', 'text_domain'),
        'separate_items_with_commas' => __('Scheid genres met een komma', 'text_domain'),
        'add_or_remove_items'        => __('Verwijder of voeg genres toe', 'text_domain'),
        'choose_from_most_used'      => __('Kies uit veelgebruikte genres', 'text_domain'),
        'popular_items'              => __('Populaire genres', 'text_domain'),
        'search_items'               => __('Doorzoek genres', 'text_domain'),
        'not_found'                  => __('Niets gevonden', 'text_domain'),
    );
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true,
        'public'                     => true,
        'show_in_rest'                  => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
    );
    register_taxonomy('genre', array('dma_alba'), $args);
}
add_action('init', 'create_dma_genre');

/* -----------------------------------------------------------
----- 4. Taxonomy: Jaar
-------------------------------------------------------------- */
function create_dma_jaar()
{
    $labels = array(
        'name'                       => _x('Jaren', 'Taxonomy General Name', 'text_domain'),
        'singular_name'              => _x('Jaar', 'Taxonomy Singular Name', 'text_domain'),
        'menu_name'                  => __('Jaren', 'text_domain'),
        'all_items'                  => __('Alle jaren', 'text_domain'),
        'parent_item'                => __('Parent jaar', 'text_domain'),
        'parent_item_colon'          => __('Parent jaar:', 'text_domain'),
        'new_item_name'              => __('Nieuw jaar', 'text_domain'),
        'add_new_item'               => __('Voeg nieuw jaar toe', 'text_domain'),
        'edit_item'                  => __('Edit jaar', 'text_domain'),
        'update_item'                => __('Update jaar', 'text_domain'),
        'view_item'                  => __('Bekijk jaar', 'text_domain'),
        'separate_items_with_commas' => __('Scheid jaren met een komma', 'text_domain'),
        'add_or_remove_items'        => __('Verwijder of voeg jaren toe', 'text_domain'),
        'choose_from_most_used'      => __('Kies uit veelgebruikte jaren', 'text_domain'),
        'popular_items'              => __('Populaire jaren', 'text_domain'),
        'search_items'               => __('Doorzoek jaren', 'text_domain'),
        'not_found'                  => __('Niets gevonden', 'text_domain'),
    );
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => false,
        'public'                     => true,
        'show_in_rest'                  => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
    );
    register_taxonomy('jaren', array('dma_alba'), $args);
}
add_action('init', 'create_dma_jaar');

/* -----------------------------------------------------------
----- 5. Taxonomy: Label
-------------------------------------------------------------- */
function create_dma_label()
{
    $labels = array(
        'name'                       => _x('Labels', 'Taxonomy General Name', 'text_domain'),
        'singular_name'              => _x('Label', 'Taxonomy Singular Name', 'text_domain'),
        'menu_name'                  => __('Labels', 'text_domain'),
        'all_items'                  => __('Alle labels', 'text_domain'),
        'parent_item'                => __('Parent label', 'text_domain'),
        'parent_item_colon'          => __('Parent label:', 'text_domain'),
        'new_item_name'              => __('Nieuw label', 'text_domain'),
        'add_new_item'               => __('Voeg nieuw label toe', 'text_domain'),
        'edit_item'                  => __('Edit label', 'text_domain'),
        'update_item'                => __('Update label', 'text_domain'),
        'view_item'                  => __('Bekijk label', 'text_domain'),
        'separate_items_with_commas' => __('Scheid labels met een komma', 'text_domain'),
        'add_or_remove_items'        => __('Verwijder of voeg labels toe', 'text_domain'),
        'choose_from_most_used'      => __('Kies uit veelgebruikte labels', 'text_domain'),
        'popular_items'              => __('Populaire labels', 'text_domain'),
        'search_items'               => __('Doorzoek labels', 'text_domain'),
        'not_found'                  => __('Niets labels', 'text_domain'),
    );
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => false,
        'public'                     => true,
        'show_in_rest'                  => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
    );
    register_taxonomy('labels', array('dma_alba'), $args);
}
add_action('init', 'create_dma_label');

/* -----------------------------------------------------------
----- 8. Admin cleanup
-------------------------------------------------------------- */
/*
function dma_admin_cleanup() {
    remove_menu_page('users.php');
    remove_menu_page('edit-comments.php');
    remove_submenu_page( 'themes.php', 'customize.php' );

}
add_action( 'admin_menu', 'dma_admin_cleanup' );

function dma_admin_css() {
    wp_enqueue_style('my-admin-theme', plugins_url('admin.css', __FILE__));
}
add_action('admin_enqueue_scripts', 'dma_admin_css');
add_action('login_enqueue_scripts', 'dma_admin_css');
*/

/* -----------------------------------------------------------
----- 6. Init Custom Fields
-------------------------------------------------------------- */

// function album_data_add_meta_box() {
// 	add_meta_box(
// 		'album_data-album-data',
// 		__( 'Discogs shortcut', 'album_data' ),
// 		'album_data_html',
// 		'dma_alba',
// 		'advanced',
// 		'default'
// 	);
// }
// add_action( 'add_meta_boxes', 'album_data_add_meta_box' );

// function album_data_html( $post) {
// 	echo 'x';
// }

// Load external images as featured images
function upload_external_image_with_acf($post_id)
{
    write_custom_log("Function upload_external_image_with_acf triggered for post ID: $post_id");

    // If it's a WordPress autosave or a revision, skip saving our custom data
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    // Don't run on WP's post save, only ACF's save.
    if ($post_id === 'options') return;

    // Fetch the field
    $external_image_url = get_field('external_image_url', $post_id);

    write_custom_log("External image URL: " . $external_image_url);

    if (!empty($external_image_url)) {
        // Download image data
        $response = wp_remote_get($external_image_url);

        if (is_array($response)) {
            $image_data = wp_remote_retrieve_body($response); // Get image data

            // Create an upload file
            $upload = wp_upload_bits(basename($external_image_url), null, $image_data);

            if (!$upload['error']) {
                $file_path = $upload['file']; // Path to the uploaded file
                $file_name = basename($file_path);
                $file_type = wp_check_filetype($file_name, null);

                // Prepare the attachment post data
                $attachment = [
                    'post_mime_type' => $file_type['type'],
                    'post_title'     => preg_replace('/\.[^.]+$/', '', $file_name),
                    'post_content'   => '',
                    'post_status'    => 'inherit'
                ];

                // Insert the attachment
                $attach_id = wp_insert_attachment($attachment, $file_path, $post_id);

                // Include the image.php file for wp_generate_attachment_metadata()
                require_once(ABSPATH . 'wp-admin/includes/image.php');

                $attach_data = wp_generate_attachment_metadata($attach_id, $file_path);

                // Update attachment metadata
                wp_update_attachment_metadata($attach_id, $attach_data);

                // Set as featured image
                set_post_thumbnail($post_id, $attach_id);
                update_field('external_image_url', '', $post_id);

                write_custom_log("Image uploaded and set as featured image.");
            } else {
                write_custom_log("Upload error: " . $upload['error']);
            }
        } else {
            write_custom_log("Failed to download image.");
        }
    } else {
        write_custom_log("External image URL is empty.");
    }
}
add_action('acf/save_post', 'upload_external_image_with_acf', 20); // run after ACF saves the custom fields

// Allow for inline triggering of the function
function acp_editing_saved_usage( AC\Column $column, $id, $value ) {
    upload_external_image_with_acf($id, $value);
}
add_action( 'acp/editing/saved', 'acp_editing_saved_usage', 10, 3 );
/* -----------------------------------------------------------
----- 9. Filter by genre
-------------------------------------------------------------- */
// GET GENRES
function tags_filter()
{
    $tax = 'genre';
    $terms = get_terms($tax,  array(
        'orderby'    => 'count',
        'hide_empty' => 1,
        'order'      => 'DESC'
    ));

    $count = count($terms);
    if ($count > 0) : ?>

        <div class="post-tags filter-tags">
            <ul>
                <?php
                foreach ($terms as $term) {
                    $term_link = get_term_link($term, $tax);
                    echo '<li><a href="' . $term_link . '" class="tax-filter" title="' . $term->slug . '">' . $term->name . '</a></li>';
                } ?>
            </ul>
        </div>

    <?php endif;
}

// GET ARTIESTEN
function artiest_filter()
{
    $tax = 'artiest';
    $terms = get_terms($tax,  array(
        'orderby'    => 'name',
        'hide_empty' => 1,
        'order'      => 'ASC'
    ));

    $count = count($terms);
    if ($count > 0) : ?>

        <div class="post-tags filter-artiest">
            <ul>
                <?php
                foreach ($terms as $term) {
                    $term_link = get_term_link($term, $tax);
                    echo '<li><a href="' . $term_link . '" class="tax-filter" title="' . $term->slug . '">' . $term->name . '</a></li>';
                } ?>
            </ul>
        </div>

    <?php endif;
}

// GET JAREN
function jaren_filter()
{
    $tax = 'jaren';
    $terms = get_terms($tax,  array(
        'orderby'    => 'name',
        'hide_empty' => 1,
        'order'      => 'ASC'
    ));

    $count = count($terms);
    if ($count > 0) : ?>

        <div class="post-tags filter-jaar">
            <ul>
                <?php
                foreach ($terms as $term) {
                    $term_link = get_term_link($term, $tax);
                    echo '<li><a href="' . $term_link . '" class="tax-filter" title="' . $term->slug . '">' . $term->name . '</a></li>';
                } ?>
            </ul>
        </div>

    <?php endif;
}

// GET LABELS
function label_filter()
{
    $tax = 'label';
    $terms = get_terms($tax,  array(
        'orderby'    => 'name',
        'hide_empty' => 1,
        'order'      => 'ASC'
    ));

    $count = count($terms);
    if ($count > 0) : ?>

        <div class="post-tags filter-label">
            <ul>
                <?php
                foreach ($terms as $term) {
                    $term_link = get_term_link($term, $tax);
                    echo '<li><a href="' . $term_link . '" class="tax-filter" title="' . $term->slug . '">' . $term->name . '</a></li>';
                } ?>
            </ul>
        </div>

<?php endif;
}

?>