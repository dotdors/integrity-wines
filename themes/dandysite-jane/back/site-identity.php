<?php
/**
 * Site Identity Settings
 * Standalone options page for managing logo variants.
 * dandysite-jane theme
 *
 * Logo variants:
 *   full   — Full color logo (light backgrounds, footer)
 *   white  — Single-color white/light logo (dark backgrounds, overlay hero, dark header)
 *   words  — Wordmark only, no icon (optional, for compact contexts)
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


// =====================================================================
// ADMIN MENU
// =====================================================================

function dsp_add_site_identity_page() {
    add_theme_page(
        __( 'Site Identity', 'dandysite-jane' ),
        __( 'Site Identity', 'dandysite-jane' ),
        'manage_options',
        'dsp-site-identity',
        'dsp_site_identity_page'
    );
}
add_action( 'admin_menu', 'dsp_add_site_identity_page' );


// =====================================================================
// REGISTER SETTINGS
// =====================================================================

function dsp_register_site_identity_settings() {

    $logo_options = [
        'dsp_logo_full'  => '',   // Attachment ID — full color logo
        'dsp_logo_white' => '',   // Attachment ID — white/1-color logo
        'dsp_logo_words' => '',   // Attachment ID — wordmark only
    ];

    foreach ( $logo_options as $key => $default ) {
        register_setting( 'dsp_site_identity', $key, [
            'sanitize_callback' => 'absint',
            'default'           => $default,
        ] );
    }

    add_settings_section(
        'dsp_logos_section',
        __( 'Logo Variants', 'dandysite-jane' ),
        'dsp_logos_section_callback',
        'dsp-site-identity'
    );

    add_settings_field(
        'dsp_logo_full',
        __( 'Full Color Logo', 'dandysite-jane' ),
        'dsp_logo_field_callback',
        'dsp-site-identity',
        'dsp_logos_section',
        [ 'option' => 'dsp_logo_full', 'label' => __( 'Used on light backgrounds, footer, solid header.', 'dandysite-jane' ) ]
    );

    add_settings_field(
        'dsp_logo_white',
        __( 'White / 1-Color Logo', 'dandysite-jane' ),
        'dsp_logo_field_callback',
        'dsp-site-identity',
        'dsp_logos_section',
        [ 'option' => 'dsp_logo_white', 'label' => __( 'Used on dark backgrounds, overlay hero, dark header. Must be PNG with transparent background.', 'dandysite-jane' ) ]
    );

    add_settings_field(
        'dsp_logo_words',
        __( 'Wordmark Only (optional)', 'dandysite-jane' ),
        'dsp_logo_field_callback',
        'dsp-site-identity',
        'dsp_logos_section',
        [ 'option' => 'dsp_logo_words', 'label' => __( 'Text/wordmark without icon. For compact contexts if needed.', 'dandysite-jane' ) ]
    );
}
add_action( 'admin_init', 'dsp_register_site_identity_settings' );


// =====================================================================
// SECTION & FIELD CALLBACKS
// =====================================================================

function dsp_logos_section_callback() {
    echo '<p>' . esc_html__( 'Upload logo variants for different display contexts. PNG with transparent background recommended for all variants.', 'dandysite-jane' ) . '</p>';
    echo '<p>' . esc_html__( 'SVG files are supported and preferred for sharpness at all sizes.', 'dandysite-jane' ) . '</p>';
}

function dsp_logo_field_callback( $args ) {
    $option    = $args['option'];
    $label     = $args['label'];
    $attach_id = (int) get_option( $option, 0 );
    $img_url   = $attach_id ? wp_get_attachment_image_url( $attach_id, 'medium' ) : '';
    $input_id  = 'dsp-logo-' . sanitize_key( $option );
    ?>
    <div class="dsp-logo-field" id="<?php echo esc_attr( $input_id ); ?>-wrap" style="display: flex; align-items: flex-start; gap: 16px;">

        <div class="dsp-logo-preview" style="width: 200px; min-height: 80px; background: <?php echo ( $option === 'dsp_logo_white' ) ? '#2A2420' : '#f0f0f0'; ?>; border: 1px solid #ddd; border-radius: 4px; display: flex; align-items: center; justify-content: center; padding: 12px; flex-shrink: 0;">
            <?php if ( $img_url ) : ?>
                <img id="<?php echo esc_attr( $input_id ); ?>-preview"
                     src="<?php echo esc_url( $img_url ); ?>"
                     style="max-width: 100%; max-height: 80px; object-fit: contain;"
                     alt="">
            <?php else : ?>
                <img id="<?php echo esc_attr( $input_id ); ?>-preview"
                     src=""
                     style="max-width: 100%; max-height: 80px; object-fit: contain; display: none;"
                     alt="">
                <span id="<?php echo esc_attr( $input_id ); ?>-empty" style="color: #999; font-size: 12px; text-align: center;"><?php esc_html_e( 'No logo uploaded', 'dandysite-jane' ); ?></span>
            <?php endif; ?>
        </div>

        <div>
            <input type="hidden"
                   id="<?php echo esc_attr( $input_id ); ?>"
                   name="<?php echo esc_attr( $option ); ?>"
                   value="<?php echo esc_attr( $attach_id ?: '' ); ?>">

            <button type="button"
                    class="button dsp-logo-upload"
                    data-input-id="<?php echo esc_attr( $input_id ); ?>"
                    data-preview-id="<?php echo esc_attr( $input_id ); ?>-preview"
                    data-empty-id="<?php echo esc_attr( $input_id ); ?>-empty"
                    data-title="<?php esc_attr_e( 'Select Logo', 'dandysite-jane' ); ?>"
                    data-button="<?php esc_attr_e( 'Use This Logo', 'dandysite-jane' ); ?>">
                <?php echo $attach_id ? esc_html__( 'Change Logo', 'dandysite-jane' ) : esc_html__( 'Upload Logo', 'dandysite-jane' ); ?>
            </button>

            <?php if ( $attach_id ) : ?>
                <button type="button"
                        class="button dsp-logo-remove"
                        data-input-id="<?php echo esc_attr( $input_id ); ?>"
                        data-preview-id="<?php echo esc_attr( $input_id ); ?>-preview"
                        data-empty-id="<?php echo esc_attr( $input_id ); ?>-empty"
                        style="margin-left: 6px; color: #b32d2e;">
                    <?php esc_html_e( 'Remove', 'dandysite-jane' ); ?>
                </button>
            <?php else : ?>
                <button type="button"
                        class="button dsp-logo-remove"
                        data-input-id="<?php echo esc_attr( $input_id ); ?>"
                        data-preview-id="<?php echo esc_attr( $input_id ); ?>-preview"
                        data-empty-id="<?php echo esc_attr( $input_id ); ?>-empty"
                        style="margin-left: 6px; color: #b32d2e; display: none;">
                    <?php esc_html_e( 'Remove', 'dandysite-jane' ); ?>
                </button>
            <?php endif; ?>

            <p class="description" style="margin-top: 8px; max-width: 300px;"><?php echo esc_html( $label ); ?></p>
        </div>
    </div>
    <?php
}


// =====================================================================
// PAGE OUTPUT
// =====================================================================

function dsp_site_identity_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <p style="color: #666;"><?php esc_html_e( 'Manage logo files used across the site. Each context pulls the appropriate variant automatically.', 'dandysite-jane' ); ?></p>

        <form method="post" action="options.php">
            <?php
            settings_fields( 'dsp_site_identity' );
            do_settings_sections( 'dsp-site-identity' );
            submit_button( __( 'Save Logos', 'dandysite-jane' ) );
            ?>
        </form>
    </div>
    <?php
}


// =====================================================================
// ADMIN ASSETS — media uploader
// =====================================================================

function dsp_site_identity_admin_assets( $hook ) {
    if ( $hook !== 'appearance_page_dsp-site-identity' ) {
        return;
    }
    wp_enqueue_media();
    // Inline JS — simple media uploader, no separate file needed
    wp_add_inline_script( 'jquery', dsp_site_identity_uploader_js() );
}
add_action( 'admin_enqueue_scripts', 'dsp_site_identity_admin_assets' );

function dsp_site_identity_uploader_js() {
    ob_start();
    ?>
    jQuery(function($) {
        // Upload
        $(document).on('click', '.dsp-logo-upload', function(e) {
            e.preventDefault();
            var btn        = $(this);
            var inputId    = btn.data('input-id');
            var previewId  = btn.data('preview-id');
            var emptyId    = btn.data('empty-id');

            var frame = wp.media({
                title:    btn.data('title'),
                button:   { text: btn.data('button') },
                multiple: false,
                library:  { type: ['image'] }
            });

            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                $('#' + inputId).val(attachment.id);
                $('#' + previewId).attr('src', attachment.url).show();
                $('#' + emptyId).hide();
                btn.text('<?php echo esc_js( __( 'Change Logo', 'dandysite-jane' ) ); ?>');
                btn.next('.dsp-logo-remove').show();
            });

            frame.open();
        });

        // Remove
        $(document).on('click', '.dsp-logo-remove', function(e) {
            e.preventDefault();
            var btn       = $(this);
            var inputId   = btn.data('input-id');
            var previewId = btn.data('preview-id');
            var emptyId   = btn.data('empty-id');

            $('#' + inputId).val('');
            $('#' + previewId).attr('src', '').hide();
            $('#' + emptyId).show();
            btn.prev('.dsp-logo-upload').text('<?php echo esc_js( __( 'Upload Logo', 'dandysite-jane' ) ); ?>');
            btn.hide();
        });
    });
    <?php
    return ob_get_clean();
}


// =====================================================================
// PUBLIC HELPER — dsp_get_logo_url( $variant )
// =====================================================================

/**
 * Get the URL for a logo variant.
 *
 * @param string $variant  'full' | 'white' | 'words'
 * @return string|false    URL string or false if not set.
 */
function dsp_get_logo_url( $variant = 'full' ) {
    $option_map = [
        'full'  => 'dsp_logo_full',
        'white' => 'dsp_logo_white',
        'words' => 'dsp_logo_words',
    ];

    if ( ! isset( $option_map[ $variant ] ) ) {
        return false;
    }

    $attach_id = (int) get_option( $option_map[ $variant ], 0 );
    if ( ! $attach_id ) {
        return false;
    }

    return wp_get_attachment_image_url( $attach_id, 'full' ) ?: false;
}

/**
 * Output a logo img tag for a given variant.
 *
 * @param string $variant   'full' | 'white' | 'words'
 * @param string $class     Extra CSS classes.
 * @param string $size      WP image size. Default 'full'.
 */
function dsp_logo_img( $variant = 'full', $class = 'site-logo', $size = 'full' ) {
    $option_map = [
        'full'  => 'dsp_logo_full',
        'white' => 'dsp_logo_white',
        'words' => 'dsp_logo_words',
    ];

    if ( ! isset( $option_map[ $variant ] ) ) {
        return;
    }

    $attach_id = (int) get_option( $option_map[ $variant ], 0 );
    $site_name = get_bloginfo( 'name' );

    if ( $attach_id ) {
        $url = wp_get_attachment_image_url( $attach_id, $size );
        $alt = get_post_meta( $attach_id, '_wp_attachment_image_alt', true ) ?: $site_name;
        if ( $url ) {
            printf(
                '<img src="%s" alt="%s" class="%s" loading="eager">',
                esc_url( $url ),
                esc_attr( $alt ),
                esc_attr( $class )
            );
            return;
        }
    }

    // Fallback to WP custom logo
    if ( has_custom_logo() ) {
        $logo_id = get_theme_mod( 'custom_logo' );
        $url     = wp_get_attachment_image_url( $logo_id, $size );
        $alt     = get_post_meta( $logo_id, '_wp_attachment_image_alt', true ) ?: $site_name;
        if ( $url ) {
            printf(
                '<img src="%s" alt="%s" class="%s" loading="eager">',
                esc_url( $url ),
                esc_attr( $alt ),
                esc_attr( $class )
            );
            return;
        }
    }

    // Last resort: site name text
    printf( '<span class="site-logo-text %s">%s</span>', esc_attr( $class ), esc_html( $site_name ) );
}
