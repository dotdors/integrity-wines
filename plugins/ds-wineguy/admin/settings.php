<?php
/**
 * Admin Settings Page
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add settings page to admin menu
 */
function dswg_add_settings_page() {
    add_submenu_page(
        'edit.php?post_type=dswg_producer',
        __('Wine Guy Settings', 'ds-wineguy'),
        __('Settings', 'ds-wineguy'),
        'manage_options',
        'dswg-settings',
        'dswg_render_settings_page'
    );
}
add_action('admin_menu', 'dswg_add_settings_page');

/**
 * Handle settings save
 */
add_action( 'admin_init', function() {
    if (
        ! isset( $_POST['dswg_settings_nonce'] ) ||
        ! isset( $_GET['page'] ) || $_GET['page'] !== 'dswg-settings'
    ) {
        return;
    }

    if (
        ! wp_verify_nonce( $_POST['dswg_settings_nonce'], 'dswg_save_settings' ) ||
        ! current_user_can( 'manage_options' )
    ) {
        return;
    }

    // Archive hero image ID (int, 0 to clear)
    $hero_id = isset( $_POST['dswg_archive_hero_id'] ) && $_POST['dswg_archive_hero_id'] !== ''
        ? absint( $_POST['dswg_archive_hero_id'] )
        : 0;
    update_option( 'dswg_archive_hero_id', $hero_id );

    // Archive intro text (plain text, no HTML)
    $intro_text = isset( $_POST['dswg_archive_intro_text'] )
        ? sanitize_textarea_field( wp_unslash( $_POST['dswg_archive_intro_text'] ) )
        : '';
    update_option( 'dswg_archive_intro_text', $intro_text );
} );

/**
 * Render settings page
 */
function dswg_render_settings_page() {
    $saved = isset( $_POST['dswg_settings_nonce'] ) && wp_verify_nonce( $_POST['dswg_settings_nonce'], 'dswg_save_settings' );

    // Current values
    $hero_id    = (int) get_option( 'dswg_archive_hero_id', 0 );
    $intro_text = get_option( 'dswg_archive_intro_text', '' );
    $hero_url   = $hero_id ? wp_get_attachment_image_url( $hero_id, 'large' ) : '';
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

        <?php if ( $saved ) : ?>
            <div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Settings saved.', 'ds-wineguy' ); ?></p></div>
        <?php endif; ?>

        <form method="post" action="">
            <?php wp_nonce_field( 'dswg_save_settings', 'dswg_settings_nonce' ); ?>

            <h2><?php esc_html_e( 'Producer Archive Page', 'ds-wineguy' ); ?></h2>
            <p class="description"><?php esc_html_e( 'Controls the hero section of the /producers/ archive page.', 'ds-wineguy' ); ?></p>

            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row">
                        <label for="dswg_archive_hero_id"><?php esc_html_e( 'Hero Background Image', 'ds-wineguy' ); ?></label>
                    </th>
                    <td>
                        <div class="dswg-media-field">
                            <input type="hidden"
                                   id="dswg_archive_hero_id"
                                   name="dswg_archive_hero_id"
                                   value="<?php echo esc_attr( $hero_id ?: '' ); ?>">

                            <div id="dswg-archive-hero-preview" style="margin-bottom: 8px;">
                                <?php if ( $hero_url ) : ?>
                                    <img src="<?php echo esc_url( $hero_url ); ?>"
                                         alt=""
                                         style="max-width: 400px; max-height: 200px; display: block; object-fit: cover;">
                                <?php endif; ?>
                            </div>

                            <button type="button" class="button" id="dswg-archive-hero-upload">
                                <?php echo $hero_id
                                    ? esc_html__( 'Change Image', 'ds-wineguy' )
                                    : esc_html__( 'Choose Image', 'ds-wineguy' ); ?>
                            </button>

                            <?php if ( $hero_id ) : ?>
                                <button type="button"
                                        class="button button-link-delete"
                                        id="dswg-archive-hero-remove"
                                        style="margin-left: 8px;">
                                    <?php esc_html_e( 'Remove', 'ds-wineguy' ); ?>
                                </button>
                            <?php endif; ?>
                        </div>
                        <p class="description"><?php esc_html_e( 'Full-width background photo for the archive hero. Recommended: 1600px wide or larger, landscape orientation.', 'ds-wineguy' ); ?></p>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="dswg_archive_intro_text"><?php esc_html_e( 'Intro Text', 'ds-wineguy' ); ?></label>
                    </th>
                    <td>
                        <textarea id="dswg_archive_intro_text"
                                  name="dswg_archive_intro_text"
                                  rows="3"
                                  class="large-text"><?php echo esc_textarea( $intro_text ); ?></textarea>
                        <p class="description"><?php esc_html_e( 'Italic subheading shown below "Our Producers". Leave blank to hide.', 'ds-wineguy' ); ?></p>
                    </td>
                </tr>
            </table>

            <?php submit_button( __( 'Save Settings', 'ds-wineguy' ) ); ?>
        </form>

        <hr>
        <h2><?php esc_html_e( 'Quick Stats', 'ds-wineguy' ); ?></h2>
        <ul>
            <li><?php
                $producer_count = wp_count_posts('dswg_producer');
                printf( esc_html__( 'Producers: %d', 'ds-wineguy' ), $producer_count->publish );
            ?></li>
            <li><?php
                $wine_count = wp_count_posts('dswg_wine');
                printf( esc_html__( 'Wines: %d', 'ds-wineguy' ), $wine_count->publish );
            ?></li>
            <li><?php
                $country_count = wp_count_terms(['taxonomy' => 'dswg_country']);
                printf( esc_html__( 'Countries: %d', 'ds-wineguy' ), $country_count );
            ?></li>
        </ul>
    </div>

    <script>
    (function($) {
        var mediaFrame;

        $('#dswg-archive-hero-upload').on('click', function(e) {
            e.preventDefault();

            if (mediaFrame) {
                mediaFrame.open();
                return;
            }

            mediaFrame = wp.media({
                title:    'Choose Archive Hero Image',
                button:   { text: 'Use this image' },
                multiple: false,
                library:  { type: 'image' }
            });

            mediaFrame.on('select', function() {
                var attachment = mediaFrame.state().get('selection').first().toJSON();
                var url = attachment.sizes && attachment.sizes.large
                    ? attachment.sizes.large.url
                    : attachment.url;

                $('#dswg_archive_hero_id').val(attachment.id);
                $('#dswg-archive-hero-preview').html(
                    '<img src="' + url + '" alt="" style="max-width:400px;max-height:200px;display:block;object-fit:cover;">'
                );
                $('#dswg-archive-hero-upload').text('Change Image');

                if (!$('#dswg-archive-hero-remove').length) {
                    $('#dswg-archive-hero-upload').after(
                        ' <button type="button" class="button button-link-delete" id="dswg-archive-hero-remove" style="margin-left:8px;">Remove</button>'
                    );
                    bindRemove();
                }
            });

            mediaFrame.open();
        });

        function bindRemove() {
            $(document).on('click', '#dswg-archive-hero-remove', function(e) {
                e.preventDefault();
                $('#dswg_archive_hero_id').val('');
                $('#dswg-archive-hero-preview').html('');
                $('#dswg-archive-hero-upload').text('Choose Image');
                $(this).remove();
            });
        }
        bindRemove();

    }(jQuery));
    </script>
    <?php
}
