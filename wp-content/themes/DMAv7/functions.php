<?php
// Register scripts
function register_scripts() {
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'visible', get_template_directory_uri() . '/assets/js/jquery-visible-min.js', array('jquery'), 1, true);
    wp_enqueue_script( 'unveiljs', get_template_directory_uri() . '/assets/js/jquery.unveil.js', array('jquery'), 1, true);
    wp_enqueue_script( 'listfilter', get_template_directory_uri() . '/assets/js/bootstrap-list-filter.src.js', array('jquery'), 1, true);
    wp_enqueue_script( 'ajax-pagination',  get_template_directory_uri() . '/assets/js/ajax-pagination.js', array( 'jquery' ), '1.0', true );
    wp_enqueue_script( 'bootstrapjs', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js', array( 'jquery'), '1.0', true);
    wp_enqueue_script( 'mainjs', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), 2, true);

    wp_localize_script( 'ajax-pagination', 'ajaxpagination', array(
      'ajaxurl' => admin_url( 'admin-ajax.php' )
    ));

}
add_action('wp_enqueue_scripts', 'register_scripts');

foreach ( array( 'pre_term_description' ) as $filter ) {
    remove_filter( $filter, 'wp_filter_kses' );
}
 
foreach ( array( 'term_description' ) as $filter ) {
    remove_filter( $filter, 'wp_kses_data' );
}

// QUICK EDIT DESC TAX
global $the_target_tax;
$the_target_tax = 'artiest';

add_filter( "manage_edit-{$the_target_tax}_columns", function( $columns ) {
    $columns['_description'] = '';
    return $columns;
});

add_filter( "manage_{$the_target_tax}_custom_column", function( $e, $column, $term_id ) {
    if ( $column === '_description' ) return '';
}, 10, 3 );

add_filter( "get_user_option_manageedit-{$the_target_tax}columnshidden", function( $r ) {
    $r[] = '_description';
    return $r;
});
add_action( 'quick_edit_custom_box', function( $column, $screen, $tax ) {
    if ( $screen !== 'edit-tags' ) return;
    $taxonomy = get_taxonomy( $tax );
    if ( ! current_user_can( $taxonomy->cap->edit_terms ) ) return;
    global $the_target_tax;
    if ( $tax !== $the_target_tax || $column !== '_description' ) return;
    ?>
    <fieldset>
        <div class="inline-edit-col">
        <label>
            <span class="title"><?php _e('Description'); ?></span>
            <span class="input-text-wrap">
            <textarea id="inline-desc" name="description" rows="3" class="ptitle"></textarea>
            </span>
        </label>
        </div>
    </fieldset>
    <script>
    jQuery('#the-list').on('click', 'a.editinline', function(){
        var now = jQuery(this).closest('tr').find('td.column-description').text();
        jQuery('#inline-desc').text( now );
    });
    </script>
    <?php
}, 10, 3 );
function save_inline_description( $term_id ) {
    global $the_target_tax;
    $tax = get_taxonomy( $the_target_tax );
    if (
        current_filter() === "edited_{$the_target_tax}"
        && current_user_can( $tax->cap->edit_terms )
    ) {
        $description = filter_input( INPUT_POST, 'description', FILTER_SANITIZE_STRING );
        // removing action to avoid recursion
        remove_action( current_filter(), __FUNCTION__ );
        wp_update_term( $term_id, 'artiest', array( 'description' => $description ) );
    }
}
add_action( "edited_{$the_target_tax}", 'save_inline_description' );
?>