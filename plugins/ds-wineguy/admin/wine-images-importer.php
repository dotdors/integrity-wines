<?php
/**
 * Wine Image & File Importer
 *
 * Scans a server folder for files named with the IW inventory number convention,
 * matches them to wine posts, and attaches them to the correct meta fields.
 *
 * Naming convention (case-insensitive):
 *   IW1111-bottle.jpg/png/webp       → featured image (_thumbnail_id)
 *   IW1111-label.jpg                 → dswg_wine_logo  (front label)
 *   IW1111-label-front.jpg           → dswg_wine_logo  (front label)
 *   IW1111-front-label.jpg           → dswg_wine_logo  (front label)
 *   IW1111 - front label.jpg         → dswg_wine_logo  (front label, spaces tolerated)
 *   IW1111-label-back.jpg            → dswg_wine_label_back
 *   IW1111-back-label.jpg            → dswg_wine_label_back
 *   IW1111 - back label.jpg          → dswg_wine_label_back  (spaces tolerated)
 *   IW1111-techsheet.pdf/jpg/png     → dswg_wine_files (appended)
 *   IW1111 - tech sheet.pdf          → same (spaces around dash tolerated)
 *
 * Default behaviour: SKIP if the field already has a value.
 * Toggle "Overwrite existing" on the page to replace instead.
 * TODO: Long-term, consider per-file overwrite choice in the preview table.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register submenu page
 */
function dswg_add_image_importer_page() {
    add_submenu_page(
        'edit.php?post_type=dswg_producer',
        __( 'Import Wine Images', 'ds-wineguy' ),
        __( 'Import Images', 'ds-wineguy' ),
        'manage_options',
        'dswg-import-images',
        'dswg_render_image_importer_page'
    );
}
add_action( 'admin_menu', 'dswg_add_image_importer_page' );

/**
 * Default folder — relative to ABSPATH. SFTP files here before running.
 */
define( 'DSWG_IMPORT_FOLDER', WP_CONTENT_DIR . '/uploads/wine-imports' );

/**
 * Supported file types per field
 */
$DSWG_IMAGE_TYPES = [ 'jpg', 'jpeg', 'png', 'webp', 'gif' ];
$DSWG_FILE_TYPES  = [ 'pdf', 'jpg', 'jpeg', 'png' ];

/**
 * Parse a filename into [ inv_number, field, extension ] or null if unrecognised.
 *
 * Patterns matched (all case-insensitive):
 *   IW####-bottle.*
 *   IW####-label.*  |  IW####-label-front.*
 *   IW####-label-back.*
 *   IW####-techsheet.*
 */
function dswg_parse_import_filename( $filename ) {
    $name = strtolower( pathinfo( $filename, PATHINFO_FILENAME ) );
    $ext  = strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) );

    // Normalise: collapse spaces around hyphens, then collapse remaining spaces to hyphens.
    // Handles: "IW1170 - tech sheet" → "iw1170-tech-sheet"
    $name = preg_replace( '/\s*-\s*/', '-', $name ); // spaces around dash → single dash
    $name = preg_replace( '/\s+/', '-', $name );      // remaining spaces → dash

    // Must start with iw followed by digits
    if ( ! preg_match( '/^(iw\d+)-(.+)$/', $name, $m ) ) {
        return null;
    }

    $inv_no  = strtoupper( $m[1] ); // e.g. IW1042
    $suffix  = $m[2];               // e.g. bottle, label-front, tech-sheet

    $field_map = [
        'bottle'       => 'bottle',
        'label-front'  => 'label_front',
        'front-label'  => 'label_front',
        'label-back'   => 'label_back',
        'back-label'   => 'label_back',
        'label'        => 'label_front',
        'techsheet'    => 'techsheet',
        'tech-sheet'   => 'techsheet',
    ];

    $matched_field = null;
    foreach ( $field_map as $keyword => $field_key ) {
        if ( $suffix === $keyword || str_starts_with( $suffix, $keyword . '-' ) ) {
            $matched_field = $field_key;
            break;
        }
    }

    if ( ! $matched_field ) {
        return null;
    }

    return [
        'inv_no' => $inv_no,
        'field'  => $matched_field,
        'ext'    => $ext,
    ];
}

/**
 * Get the human-readable label for a field key.
 */
function dswg_import_field_label( $field ) {
    $labels = [
        'bottle'      => 'Bottle Image (Featured)',
        'label_front' => 'Label — Front',
        'label_back'  => 'Label — Back',
        'techsheet'   => 'Tech Sheet / File',
    ];
    return $labels[ $field ] ?? $field;
}

/**
 * Build the attachment title from wine title + field type.
 */
function dswg_import_attachment_title( $wine_title, $field ) {
    $suffixes = [
        'bottle'      => 'Bottle Image',
        'label_front' => 'Label Front',
        'label_back'  => 'Label Back',
        'techsheet'   => 'Tech Sheet',
    ];
    $suffix = $suffixes[ $field ] ?? $field;
    return $wine_title . ' — ' . $suffix;
}

/**
 * Recursively collect all files under $folder.
 * Returns array of [ 'filename' => basename, 'filepath' => full path, 'relpath' => path relative to $folder ]
 * or null on failure.
 */
function dswg_get_import_files( $folder ) {
    $skip    = [ '.DS_Store', 'Thumbs.db', '__MACOSX' ];
    $results = [];

    try {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator( $folder, RecursiveDirectoryIterator::SKIP_DOTS ),
            RecursiveIteratorIterator::LEAVES_ONLY
        );
        foreach ( $iterator as $file ) {
            if ( ! $file->isFile() ) {
                continue;
            }
            $basename = $file->getFilename();
            if ( in_array( $basename, $skip, true ) || str_starts_with( $basename, '.' ) ) {
                continue;
            }
            $full      = $file->getPathname();
            $relpath   = ltrim( str_replace( $folder, '', $full ), '/\\' );
            $results[] = [
                'filename' => $basename,
                'filepath' => $full,
                'relpath'  => $relpath,
            ];
        }
    } catch ( Exception $e ) {
        return null;
    }

    return $results;
}

/**
 * Scan the import folder and build a results array for the preview table.
 * Returns array of rows, each:
 *   filename, relpath, filepath, inv_no, field, wine_id, wine_title, current_value, action, error
 */
function dswg_scan_import_folder( $folder, $overwrite = false ) {
    $rows = [];

    if ( ! is_dir( $folder ) ) {
        return [ 'error' => 'Folder not found: ' . esc_html( $folder ) ];
    }

    $files = dswg_get_import_files( $folder );
    if ( $files === null ) {
        return [ 'error' => 'Could not read folder.' ];
    }

    // Build inv_no → post_id lookup once (cheaper than per-file queries)
    $wine_map = [];
    $wines = get_posts( [
        'post_type'      => 'dswg_wine',
        'posts_per_page' => -1,
        'post_status'    => 'any',
        'fields'         => 'ids',
    ] );
    foreach ( $wines as $wid ) {
        $inv = get_post_meta( $wid, 'dswg_inventory_no', true );
        if ( $inv ) {
            $wine_map[ strtoupper( trim( $inv ) ) ] = $wid;
        }
    }

    foreach ( $files as $file_entry ) {
        $filename = $file_entry['filename'];
        $filepath = $file_entry['filepath'];
        $relpath  = $file_entry['relpath'];

        $parsed = dswg_parse_import_filename( $filename );

        if ( ! $parsed ) {
            $rows[] = [
                'filename'      => $filename,
                'relpath'       => $relpath,
                'filepath'      => $filepath,
                'inv_no'        => '—',
                'field'         => '—',
                'wine_id'       => null,
                'wine_title'    => '—',
                'current_value' => '—',
                'action'        => 'skip',
                'action_label'  => 'Skip — unrecognised filename',
                'error'         => true,
            ];
            continue;
        }

        $inv_no    = $parsed['inv_no'];
        $field     = $parsed['field'];
        $wine_id   = $wine_map[ $inv_no ] ?? null;

        if ( ! $wine_id ) {
            $rows[] = [
                'filename'      => $filename,
                'relpath'       => $relpath,
                'filepath'      => $filepath,
                'inv_no'        => $inv_no,
                'field'         => dswg_import_field_label( $field ),
                'wine_id'       => null,
                'wine_title'    => '—',
                'current_value' => '—',
                'action'        => 'skip',
                'action_label'  => 'Skip — no wine found for ' . esc_html( $inv_no ),
                'error'         => true,
            ];
            continue;
        }

        $wine_title = get_the_title( $wine_id );

        // Determine current value and action
        $current_label = '(none)';
        $action        = 'attach';
        $action_label  = 'Attach (new)';

        if ( $field === 'bottle' ) {
            $thumb = get_post_thumbnail_id( $wine_id );
            if ( $thumb ) {
                $current_label = get_the_title( $thumb ) ?: 'ID ' . $thumb;
                if ( $overwrite ) {
                    $action       = 'overwrite';
                    $action_label = 'Overwrite existing';
                } else {
                    $action       = 'skip';
                    $action_label = 'Skip — already has bottle image';
                }
            }
        } elseif ( $field === 'label_front' ) {
            $existing = get_post_meta( $wine_id, 'dswg_wine_logo', true );
            if ( $existing ) {
                $current_label = get_the_title( $existing ) ?: 'ID ' . $existing;
                if ( $overwrite ) {
                    $action       = 'overwrite';
                    $action_label = 'Overwrite existing';
                } else {
                    $action       = 'skip';
                    $action_label = 'Skip — already has front label';
                }
            }
        } elseif ( $field === 'label_back' ) {
            $existing = get_post_meta( $wine_id, 'dswg_wine_label_back', true );
            if ( $existing ) {
                $current_label = get_the_title( $existing ) ?: 'ID ' . $existing;
                if ( $overwrite ) {
                    $action       = 'overwrite';
                    $action_label = 'Overwrite existing';
                } else {
                    $action       = 'skip';
                    $action_label = 'Skip — already has back label';
                }
            }
        } elseif ( $field === 'techsheet' ) {
            $existing_str   = get_post_meta( $wine_id, 'dswg_wine_files', true );
            $existing_count = $existing_str ? count( array_filter( explode( ',', $existing_str ) ) ) : 0;
            $current_label  = $existing_count > 0 ? $existing_count . ' file(s) already attached' : '(none)';
            if ( $existing_count > 0 ) {
                if ( $overwrite ) {
                    $action       = 'append';
                    $action_label = 'Append to existing file(s)';
                } else {
                    $action       = 'skip';
                    $action_label = 'Skip — already has file(s)';
                }
            } else {
                $action       = 'append';
                $action_label = 'Attach (new)';
            }
        }

        $rows[] = [
            'filename'      => $filename,
            'relpath'       => $relpath,
            'filepath'      => $filepath,
            'inv_no'        => $inv_no,
            'field'         => dswg_import_field_label( $field ),
            'field_key'     => $field,
            'wine_id'       => $wine_id,
            'wine_title'    => $wine_title,
            'current_value' => $current_label,
            'action'        => $action,
            'action_label'  => $action_label,
            'error'         => false,
        ];
    }

    return $rows;
}

/**
 * Commit: sideload files and assign to meta fields.
 * Returns array of result messages.
 */
function dswg_commit_image_import( $folder, $rows ) {
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    $results = [];

    foreach ( $rows as $row ) {
        if ( $row['error'] || ! $row['wine_id'] || $row['action'] === 'skip' ) {
            continue;
        }

        $filepath   = $row['filepath'];
        $wine_id    = $row['wine_id'];
        $field      = $row['field_key'];
        $wine_title = $row['wine_title'];

        if ( ! file_exists( $filepath ) ) {
            $results[] = [ 'status' => 'error', 'msg' => esc_html( $row['filename'] ) . ' — file not found on server.' ];
            continue;
        }

        // Copy to temp location so WP can process it cleanly
        $tmp = wp_tempnam( $row['filename'] );
        if ( ! copy( $filepath, $tmp ) ) {
            $results[] = [ 'status' => 'error', 'msg' => esc_html( $row['filename'] ) . ' — could not copy to temp.' ];
            continue;
        }

        $file_array = [
            'name'     => $row['filename'],
            'tmp_name' => $tmp,
        ];

        // Sideload into Media Library — attach to wine post
        $attachment_id = media_handle_sideload( $file_array, $wine_id );

        @unlink( $tmp );

        if ( is_wp_error( $attachment_id ) ) {
            $results[] = [ 'status' => 'error', 'msg' => esc_html( $row['filename'] ) . ' — ' . $attachment_id->get_error_message() ];
            continue;
        }

        // Set descriptive attachment title
        wp_update_post( [
            'ID'         => $attachment_id,
            'post_title' => dswg_import_attachment_title( $wine_title, $field ),
        ] );

        // Assign to correct field
        if ( $field === 'bottle' ) {
            set_post_thumbnail( $wine_id, $attachment_id );
        } elseif ( $field === 'label_front' ) {
            update_post_meta( $wine_id, 'dswg_wine_logo', $attachment_id );
        } elseif ( $field === 'label_back' ) {
            update_post_meta( $wine_id, 'dswg_wine_label_back', $attachment_id );
        } elseif ( $field === 'techsheet' ) {
            $existing_str = get_post_meta( $wine_id, 'dswg_wine_files', true );
            $existing     = $existing_str ? array_filter( explode( ',', $existing_str ) ) : [];
            $existing[]   = $attachment_id;
            update_post_meta( $wine_id, 'dswg_wine_files', implode( ',', $existing ) );
        }

        $results[] = [
            'status' => 'success',
            'msg'    => esc_html( $row['filename'] ) . ' → <strong>' . esc_html( $wine_title ) . '</strong> (' . esc_html( $row['field'] ) . ')',
        ];
    }

    return $results;
}

/**
 * Render the importer page
 */
function dswg_render_image_importer_page() {
    $folder = DSWG_IMPORT_FOLDER;
    $scan_rows      = null;
    $commit_results = null;
    $overwrite      = ! empty( $_POST['dswg_overwrite_existing'] );

    // Handle scan
    if ( isset( $_POST['dswg_scan_submit'] ) && check_admin_referer( 'dswg_image_import_nonce' ) ) {
        $scan_rows = dswg_scan_import_folder( $folder, $overwrite );
    }

    // Handle commit
    if ( isset( $_POST['dswg_commit_submit'] ) && check_admin_referer( 'dswg_image_import_nonce' ) ) {
        $scan_rows = dswg_scan_import_folder( $folder, $overwrite );
        if ( ! isset( $scan_rows['error'] ) ) {
            $commit_results = dswg_commit_image_import( $folder, $scan_rows );
        }
    }
    ?>
    <div class="wrap">
        <h1><?php _e( 'Import Wine Images &amp; Files', 'ds-wineguy' ); ?></h1>

        <div class="card" style="max-width: 900px; padding: 20px; margin-top: 20px;">

            <h2 style="margin-top: 0;"><?php _e( 'Instructions', 'ds-wineguy' ); ?></h2>
            <ol>
                <li><?php _e( 'SFTP your files into:', 'ds-wineguy' ); ?>
                    <code><?php echo esc_html( $folder ); ?></code>
                    <?php if ( ! is_dir( $folder ) ) : ?>
                        <span style="color:#d63638;"> — ⚠ folder does not exist yet, create it first</span>
                    <?php else : ?>
                        <span style="color:#00a32a;"> ✓</span>
                    <?php endif; ?>
                    <br><small style="color:#646970;">Subdirectories are scanned automatically — organise files however you like.</small>
                </li>
                <li><?php _e( 'Name files using the inventory number convention:', 'ds-wineguy' ); ?>
                    <ul style="margin: 8px 0 0 20px; list-style: disc;">
                        <li><code>IW1111-bottle.jpg</code> — bottle photo (featured image)</li>
                        <li><code>IW1111-label.jpg</code> or <code>IW1111-label-front.jpg</code> — front label</li>
                        <li><code>IW1111-label-back.jpg</code> — back label</li>
                        <li><code>IW1111-techsheet.pdf</code> or <code>IW1111 - tech sheet.pdf</code> — tech sheet / PDF</li>
                        <li>Spaces around the dash and within the suffix are handled automatically.</li>
                    </ul>
                </li>
                <li><?php _e( 'Click <strong>Scan Folder</strong> to preview what will happen — no changes are made yet.', 'ds-wineguy' ); ?></li>
                <li><?php _e( 'Review the table, then click <strong>Commit Import</strong> to attach the files.', 'ds-wineguy' ); ?></li>
            </ol>

            <p class="description" style="margin-top: 12px; color: #646970;">
                ℹ️ <?php _e( 'By default, files are <strong>skipped</strong> if the field already has a value. Use "Overwrite existing" to replace instead. Tech sheets are always appended (never replaced). Files are not deleted from the import folder after processing.', 'ds-wineguy' ); ?>
            </p>

        </div>

        <form method="post" style="margin-top: 20px;">
            <?php wp_nonce_field( 'dswg_image_import_nonce' ); ?>
            <label style="display: inline-flex; align-items: center; gap: 6px; margin-bottom: 12px; font-weight: 600;">
                <input type="checkbox" name="dswg_overwrite_existing" value="1" <?php checked( $overwrite ); ?> />
                <?php _e( 'Overwrite existing (bottle, labels) — leave unchecked to skip if already set', 'ds-wineguy' ); ?>
            </label>
            <br>
            <button type="submit" name="dswg_scan_submit" class="button button-secondary" style="margin-right: 8px;">
                📂 <?php _e( 'Scan Folder', 'ds-wineguy' ); ?>
            </button>
            <?php if ( $scan_rows && ! isset( $scan_rows['error'] ) ) : ?>
                <button type="submit" name="dswg_commit_submit" class="button button-primary">
                    ✅ <?php _e( 'Commit Import', 'ds-wineguy' ); ?>
                </button>
            <?php endif; ?>
        </form>

        <?php if ( $scan_rows && isset( $scan_rows['error'] ) ) : ?>
            <div class="notice notice-error" style="margin-top: 20px;">
                <p><?php echo esc_html( $scan_rows['error'] ); ?></p>
            </div>

        <?php elseif ( $commit_results !== null ) : ?>
            <?php
            $success_count = count( array_filter( $commit_results, fn( $r ) => $r['status'] === 'success' ) );
            $error_count   = count( array_filter( $commit_results, fn( $r ) => $r['status'] === 'error' ) );
            ?>
            <div class="notice notice-<?php echo $error_count ? 'warning' : 'success'; ?>" style="margin-top: 20px;">
                <p><strong><?php printf( __( 'Import complete: %d attached, %d errors.', 'ds-wineguy' ), $success_count, $error_count ); ?></strong></p>
            </div>
            <table class="widefat striped" style="margin-top: 16px;">
                <thead>
                    <tr>
                        <th><?php _e( 'File', 'ds-wineguy' ); ?></th>
                        <th><?php _e( 'Result', 'ds-wineguy' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $commit_results as $r ) : ?>
                    <tr style="<?php echo $r['status'] === 'error' ? 'background:#fce8e8;' : ''; ?>">
                        <td><?php echo $r['status'] === 'success' ? '✅' : '❌'; ?></td>
                        <td><?php echo wp_kses( $r['msg'], [ 'strong' => [] ] ); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        <?php elseif ( $scan_rows !== null ) : ?>
            <?php
            $total     = count( $scan_rows );
            $skipped   = count( array_filter( $scan_rows, fn( $r ) => $r['error'] ) );
            $will_act  = $total - $skipped;
            ?>
            <div class="notice notice-info" style="margin-top: 20px;">
                <p><?php printf( __( 'Found %d file(s): %d will be processed, %d skipped.', 'ds-wineguy' ), $total, $will_act, $skipped ); ?></p>
            </div>

            <table class="widefat striped" style="margin-top: 16px;">
                <thead>
                    <tr>
                        <th><?php _e( 'Filename', 'ds-wineguy' ); ?></th>
                        <th><?php _e( 'Inv #', 'ds-wineguy' ); ?></th>
                        <th><?php _e( 'Wine', 'ds-wineguy' ); ?></th>
                        <th><?php _e( 'Field', 'ds-wineguy' ); ?></th>
                        <th><?php _e( 'Current Value', 'ds-wineguy' ); ?></th>
                        <th><?php _e( 'Action', 'ds-wineguy' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $scan_rows as $row ) : ?>
                    <tr style="<?php echo $row['error'] ? 'background:#fce8e8; color:#888;' : ''; ?>">
                        <td><code><?php echo esc_html( $row['relpath'] ); ?></code></td>
                        <td><?php echo esc_html( $row['inv_no'] ); ?></td>
                        <td><?php echo esc_html( $row['wine_title'] ); ?></td>
                        <td><?php echo esc_html( $row['field'] ); ?></td>
                        <td><?php echo esc_html( $row['current_value'] ); ?></td>
                        <td>
                            <?php if ( $row['error'] ) : ?>
                                <span style="color:#d63638;"><?php echo esc_html( $row['action_label'] ); ?></span>
                            <?php elseif ( $row['action'] === 'skip' ) : ?>
                                <span style="color:#888;">– <?php echo esc_html( $row['action_label'] ); ?></span>
                            <?php elseif ( $row['action'] === 'overwrite' ) : ?>
                                <span style="color:#d63638;">⚠ <?php echo esc_html( $row['action_label'] ); ?></span>
                            <?php elseif ( $row['action'] === 'append' ) : ?>
                                <span style="color:#2271b1;">+ <?php echo esc_html( $row['action_label'] ); ?></span>
                            <?php else : ?>
                                <span style="color:#00a32a;">✓ <?php echo esc_html( $row['action_label'] ); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <form method="post" style="margin-top: 16px;">
                <?php wp_nonce_field( 'dswg_image_import_nonce' ); ?>
                <?php if ( $overwrite ) : ?>
                    <input type="hidden" name="dswg_overwrite_existing" value="1" />
                <?php endif; ?>
                <button type="submit" name="dswg_commit_submit" class="button button-primary">
                    ✅ <?php _e( 'Commit Import', 'ds-wineguy' ); ?>
                </button>
                <span style="margin-left: 12px; color: #646970;"><?php _e( 'This will attach all files shown above.', 'ds-wineguy' ); ?></span>
            </form>

        <?php endif; ?>

    </div>
    <?php
}
