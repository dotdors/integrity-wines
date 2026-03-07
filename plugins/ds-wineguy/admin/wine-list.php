<?php
/**
 * Wine List View Enhancements
 *
 * - Custom columns: Inv Number, Producer, Active status
 * - Filter dropdowns: by Producer, by Active status
 * - Bulk actions: Mark Active, Mark Inactive
 */

if (!defined('ABSPATH')) {
    exit;
}

// -----------------------------------------------------------------------
// Custom columns
// -----------------------------------------------------------------------

/**
 * Register custom columns for the wine list view
 */
function dswg_wine_list_columns($columns) {
    // Rebuild column order: cb, title, inv number, producer, active, wine type, date
    $new_columns = [];
    $new_columns['cb']               = $columns['cb'];
    $new_columns['title']            = $columns['title'];
    $new_columns['dswg_inv_number']  = __('Inv Number', 'ds-wineguy');
    $new_columns['dswg_producer']    = __('Producer', 'ds-wineguy');
    $new_columns['dswg_active']      = __('Active', 'ds-wineguy');
    // Keep taxonomy column if present
    if (isset($columns['taxonomy-dswg_wine_type'])) {
        $new_columns['taxonomy-dswg_wine_type'] = $columns['taxonomy-dswg_wine_type'];
    }
    $new_columns['date'] = $columns['date'];
    return $new_columns;
}
add_filter('manage_dswg_wine_posts_columns', 'dswg_wine_list_columns');

/**
 * Populate custom column values
 */
function dswg_wine_list_column_content($column, $post_id) {
    switch ($column) {

        case 'dswg_inv_number':
            $inv = get_post_meta($post_id, 'dswg_inventory_no', true);
            echo $inv ? esc_html($inv) : '<span style="color:#aaa;">—</span>';
            break;

        case 'dswg_producer':
            $producer_id = get_post_meta($post_id, 'dswg_producer_id', true);
            if ($producer_id) {
                $producer = get_post($producer_id);
                if ($producer) {
                    $edit_url = get_edit_post_link($producer_id);
                    echo '<a href="' . esc_url($edit_url) . '">' . esc_html($producer->post_title) . '</a>';
                } else {
                    echo '<span style="color:#aaa;">—</span>';
                }
            } else {
                echo '<span style="color:#aaa;">—</span>';
            }
            break;

        case 'dswg_active':
            // Default active for wines that predate this meta key
            $active_raw = get_post_meta($post_id, 'dswg_wine_active', true);
            $is_active  = ($active_raw === '') ? 1 : (int) $active_raw;
            if ($is_active) {
                echo '<span style="color:#46b450;" title="Active">●</span> ' . __('Active', 'ds-wineguy');
            } else {
                echo '<span style="color:#dc3232;" title="Inactive">●</span> ' . __('Inactive', 'ds-wineguy');
            }
            break;
    }
}
add_action('manage_dswg_wine_posts_custom_column', 'dswg_wine_list_column_content', 10, 2);

/**
 * Make Inv Number column sortable
 */
function dswg_wine_sortable_columns($columns) {
    $columns['dswg_inv_number'] = 'dswg_inv_number';
    return $columns;
}
add_filter('manage_edit-dswg_wine_sortable_columns', 'dswg_wine_sortable_columns');

/**
 * Handle sorting by Inv Number
 */
function dswg_wine_orderby($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }
    if ($query->get('post_type') !== 'dswg_wine') {
        return;
    }
    if ($query->get('orderby') === 'dswg_inv_number') {
        $query->set('meta_key', 'dswg_inventory_no');
        $query->set('orderby', 'meta_value');
    }
}
add_action('pre_get_posts', 'dswg_wine_orderby');


// -----------------------------------------------------------------------
// Filter dropdowns
// -----------------------------------------------------------------------

/**
 * Add Producer and Active Status filter dropdowns above the wine list
 */
function dswg_wine_list_filters($post_type) {
    if ($post_type !== 'dswg_wine') {
        return;
    }

    // --- Producer filter ---
    $selected_producer = isset($_GET['dswg_filter_producer']) ? (int) $_GET['dswg_filter_producer'] : 0;

    $producers = get_posts([
        'post_type'      => 'dswg_producer',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
        'fields'         => 'ids',
        'no_found_rows'  => true,
    ]);

    echo '<select name="dswg_filter_producer">';
    echo '<option value="0">' . __('All Producers', 'ds-wineguy') . '</option>';
    foreach ($producers as $pid) {
        printf(
            '<option value="%d" %s>%s</option>',
            $pid,
            selected($selected_producer, $pid, false),
            esc_html(get_the_title($pid))
        );
    }
    echo '</select>';

    // --- Active status filter ---
    $selected_active = isset($_GET['dswg_filter_active']) ? $_GET['dswg_filter_active'] : '';

    echo '<select name="dswg_filter_active">';
    echo '<option value="">'    . __('Active + Inactive', 'ds-wineguy') . '</option>';
    echo '<option value="1" '   . selected($selected_active, '1', false)  . '>' . __('Active only', 'ds-wineguy')   . '</option>';
    echo '<option value="0" '   . selected($selected_active, '0', false)  . '>' . __('Inactive only', 'ds-wineguy') . '</option>';
    echo '</select>';
}
add_action('restrict_manage_posts', 'dswg_wine_list_filters');

/**
 * Apply filter dropdown values to the query
 */
function dswg_wine_list_filter_query($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }
    if ($query->get('post_type') !== 'dswg_wine') {
        return;
    }

    $meta_query = (array) $query->get('meta_query');

    // Producer filter
    $producer_id = isset($_GET['dswg_filter_producer']) ? (int) $_GET['dswg_filter_producer'] : 0;
    if ($producer_id) {
        $meta_query[] = [
            'key'   => 'dswg_producer_id',
            'value' => $producer_id,
        ];
    }

    // Active status filter
    if (isset($_GET['dswg_filter_active']) && $_GET['dswg_filter_active'] !== '') {
        $active_val = (int) $_GET['dswg_filter_active'];
        if ($active_val === 1) {
            // Active = 1 OR meta not yet set (legacy wines)
            $meta_query[] = [
                'relation' => 'OR',
                [
                    'key'     => 'dswg_wine_active',
                    'value'   => '1',
                    'compare' => '=',
                ],
                [
                    'key'     => 'dswg_wine_active',
                    'compare' => 'NOT EXISTS',
                ],
            ];
        } else {
            $meta_query[] = [
                'key'   => 'dswg_wine_active',
                'value' => '0',
            ];
        }
    }

    if (!empty($meta_query)) {
        $query->set('meta_query', $meta_query);
    }
}
add_action('pre_get_posts', 'dswg_wine_list_filter_query');


// -----------------------------------------------------------------------
// Bulk actions
// -----------------------------------------------------------------------

/**
 * Register Mark Active / Mark Inactive bulk actions
 */
function dswg_wine_bulk_actions($actions) {
    $actions['dswg_mark_active']   = __('Mark Active', 'ds-wineguy');
    $actions['dswg_mark_inactive'] = __('Mark Inactive', 'ds-wineguy');
    return $actions;
}
add_filter('bulk_actions-edit-dswg_wine', 'dswg_wine_bulk_actions');

/**
 * Handle the bulk actions
 */
function dswg_wine_handle_bulk_actions($redirect_url, $action, $post_ids) {
    if (!in_array($action, ['dswg_mark_active', 'dswg_mark_inactive'], true)) {
        return $redirect_url;
    }

    $active_value = ($action === 'dswg_mark_active') ? 1 : 0;

    foreach ($post_ids as $post_id) {
        if (get_post_type($post_id) === 'dswg_wine') {
            update_post_meta((int) $post_id, 'dswg_wine_active', $active_value);
        }
    }

    $redirect_url = add_query_arg(
        'dswg_bulk_updated',
        count($post_ids),
        $redirect_url
    );

    return $redirect_url;
}
add_filter('handle_bulk_actions-edit-dswg_wine', 'dswg_wine_handle_bulk_actions', 10, 3);

/**
 * Show admin notice after bulk action
 */
function dswg_wine_bulk_action_notice() {
    $screen = get_current_screen();
    if (!$screen || $screen->id !== 'edit-dswg_wine') {
        return;
    }
    if (!empty($_GET['dswg_bulk_updated'])) {
        $count = (int) $_GET['dswg_bulk_updated'];
        printf(
            '<div class="notice notice-success is-dismissible"><p>%s</p></div>',
            sprintf(
                _n('%d wine updated.', '%d wines updated.', $count, 'ds-wineguy'),
                $count
            )
        );
    }
}
add_action('admin_notices', 'dswg_wine_bulk_action_notice');
