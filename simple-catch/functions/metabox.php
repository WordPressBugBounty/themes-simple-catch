<?php
/**
 * Simple Catch Custom meta box
 *
 * @package Catch Themes
 * @subpackage Simple_Catch
 * @since Simple Catch 1.0
 */

 // Add the Meta Box
function simplecatch_add_custom_box() {
    add_meta_box(
        'simple-catch-metabox-options',
        esc_html__( 'Simple Catch Options', 'simple-catch' ),
        'simplecatch_sidebar_layout',
        array( 'page', 'post' ),
        'side',
        'high'
    );
}
add_action( 'add_meta_boxes', 'simplecatch_add_custom_box' );

/**
 * @renders metabox to for sidebar layout
 */
function simplecatch_sidebar_layout() {
    global $post;
    // Use nonce for verification
    wp_nonce_field( basename( __FILE__ ), 'custom_meta_box_nonce' );

    $options = array(
        'simplecatch-sidebarlayout' => array(
            'label'   => esc_html__( 'Select Sidebar', 'simple-catch' ),
            'options' => array(
                'no-sidebar'            => esc_html__( 'No sidebar', 'simple-catch' ),
                'no-sidebar-full-width' => esc_html__( 'No sidebar, Full Width', 'simple-catch' ),
                'left-sidebar'          => esc_html__( 'Left sidebar', 'simple-catch' ),
                'right-sidebar'         => esc_html__( 'Right sidebar', 'simple-catch' ),
            ),
        ),
    );

    foreach ( $options as $key => $value ) : ?>
        <p class="post-attributes-label-wrapper"><label class="post-attributes-label" for="<?php echo esc_attr( $key ); ?>"><?php echo $value['label']; ?></label></p>
        <select class="widefat" name="<?php echo esc_attr( $key ); ?>" id="<?php echo esc_attr( $key ); ?>">
             <?php
                $meta_value = get_post_meta( $post->ID, $key, true );
                
                if ( empty( $meta_value ) ){
                    $meta_value = 'default';
                }
                
                foreach ( $value['options'] as $field => $label ) {  
                ?>
                    <option value="<?php echo esc_attr( $field ); ?>" <?php selected( $meta_value, $field ); ?>><?php echo esc_html( $label ); ?></option>
                <?php
                } // end foreach
            ?>
        </select>
    <?php
    endforeach;
}


/**
 * save the custom metabox data
 * @hooked to save_post hook
 */
function simplecatch_save_custom_meta( $post_id ) {
    global $post;

    // Verify the nonce before proceeding.
    if ( ! isset( $_POST[ 'custom_meta_box_nonce' ] ) || ! wp_verify_nonce( $_POST[ 'custom_meta_box_nonce' ], basename( __FILE__ ) ) ) {
        return;
    }

    // Stop WP from clearing custom fields on autosave
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( 'page' == $_POST['post_type'] ) {
        if ( ! current_user_can( 'edit_page', $post_id ) ) {
            return $post_id;
        }
    } elseif ( ! current_user_can( 'edit_post', $post_id ) ) {
            return $post_id;
    }

    $value = $_POST['simplecatch-sidebarlayout'];
    if ( '' != $value ) {
        if ( ! update_post_meta( $post_id, 'simplecatch-sidebarlayout', sanitize_text_field( $value ) ) ) {
            add_post_meta( $post_id, 'simplecatch-sidebarlayout', sanitize_text_field( $value ), true );
        }
    }
}
add_action( 'save_post', 'simplecatch_save_custom_meta' );
