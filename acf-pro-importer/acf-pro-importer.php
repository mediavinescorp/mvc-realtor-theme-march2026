<?php
/**
 * Plugin Name: ACF Pro Importer
 * Description: Import CSV data into ACF fields — scalar fields, repeaters, taxonomies, and relationships. Supports single-post, bulk update, and create-new-posts modes.
 * Version:     3.0.0
 * Author:      Blayne Pacelli Site
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'ACFPI_VERSION', '3.0.0' );
define( 'ACFPI_SLUG',    'acf-pro-importer' );

// ── Admin menu ───────────────────────────────────────────────────────────────
add_action( 'admin_menu', function () {
    add_menu_page(
        'ACF Pro Importer',
        'ACF Importer',
        'manage_options',
        ACFPI_SLUG,
        'acfpi_render_page',
        'dashicons-database-import',
        28
    );
} );

// ── Styles ───────────────────────────────────────────────────────────────────
add_action( 'admin_head', function () {
    $screen = get_current_screen();
    if ( ! $screen || strpos( $screen->id, ACFPI_SLUG ) === false ) return;
    ?>
    <style>
    /* ── Reset ── */
    #acfpi-wrap * { box-sizing: border-box; }
    #acfpi-wrap {
        font-family: 'Segoe UI', system-ui, sans-serif;
        max-width: 960px;
        margin: 28px 20px;
        color: #1a1a1a;
    }

    /* ── Header ── */
    .pi-header {
        background: #1A1A1A;
        border-radius: 14px 14px 0 0;
        padding: 26px 36px;
        display: flex;
        align-items: center;
        gap: 18px;
    }
    .pi-header-icon {
        width: 48px; height: 48px;
        background: #F2E33B;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 24px; flex-shrink: 0;
    }
    .pi-header h1 { color: #fff; font-size: 20px; font-weight: 800; margin: 0; line-height: 1.2; }
    .pi-header p  { color: #666; font-size: 13px; margin: 3px 0 0; }

    /* ── Tab bar ── */
    .pi-tabs {
        background: #111;
        display: flex;
        border-bottom: none;
        padding: 0 36px;
    }
    .pi-tab {
        padding: 14px 18px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .05em;
        text-transform: uppercase;
        color: #555;
        cursor: pointer;
        border-bottom: 3px solid transparent;
        transition: color .2s, border-color .2s;
        user-select: none;
    }
    .pi-tab:hover { color: #bbb; }
    .pi-tab.active { color: #F2E33B; border-bottom-color: #F2E33B; }

    /* ── Steps bar ── */
    .pi-steps {
        background: #1a1a1a;
        padding: 12px 36px;
        display: flex;
        gap: 6px;
        align-items: center;
    }
    .pi-step-item {
        display: flex; align-items: center; gap: 8px;
        font-size: 12px; font-weight: 600;
        color: #444;
        letter-spacing: .04em;
        text-transform: uppercase;
        white-space: nowrap;
    }
    .pi-step-item .snum {
        width: 22px; height: 22px;
        border-radius: 50%;
        background: #2a2a2a;
        color: #555;
        font-size: 11px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        font-weight: 700;
    }
    .pi-step-item.active { color: #F2E33B; }
    .pi-step-item.active .snum { background: #F2E33B; color: #000; }
    .pi-step-item.done { color: #444; }
    .pi-step-item.done .snum { background: #333; color: #666; }
    .pi-step-arrow { color: #333; font-size: 14px; }

    /* ── Body ── */
    .pi-body {
        background: #f8f8f8;
        border: 1px solid #e2e2e2;
        border-top: none;
        border-radius: 0 0 14px 14px;
        padding: 36px;
    }

    /* ── Mode switcher ── */
    .pi-mode-row {
        display: flex;
        gap: 12px;
        margin-bottom: 28px;
    }
    .pi-mode-card {
        flex: 1;
        display: flex; align-items: flex-start; gap: 14px;
        padding: 18px;
        background: #fff;
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        cursor: pointer;
        transition: border-color .15s, background .15s;
    }
    .pi-mode-card:hover { border-color: #C9BC1F; background: #fffde8; }
    .pi-mode-card.selected { border-color: #F2E33B; background: #fffde0; }
    .pi-mode-card input[type="radio"] { margin-top: 3px; accent-color: #C9BC1F; flex-shrink: 0; }
    .pi-mode-icon { font-size: 22px; flex-shrink: 0; }
    .pi-mode-title { font-size: 14px; font-weight: 700; color: #1a1a1a; margin-bottom: 3px; }
    .pi-mode-desc  { font-size: 12px; color: #777; line-height: 1.5; }

    /* ── Panel / step panels ── */
    .pi-panel { display: none; }
    .pi-panel.active {
        display: block;
        animation: piFadeIn .2s ease;
    }
    @keyframes piFadeIn {
        from { opacity: 0; transform: translateY(5px); }
        to   { opacity: 1; transform: none; }
    }
    .pi-panel h2 { font-size: 18px; font-weight: 700; margin: 0 0 5px; color: #1a1a1a; }
    .pi-panel .pi-sub { font-size: 13px; color: #777; margin: 0 0 22px; }

    /* ── Form elements ── */
    .pi-field { margin-bottom: 18px; }
    .pi-field label {
        display: block; font-size: 11px; font-weight: 700;
        text-transform: uppercase; letter-spacing: .06em;
        color: #555; margin-bottom: 6px;
    }
    .pi-field select,
    .pi-field input[type="text"],
    .pi-field input[type="file"] {
        width: 100%; max-width: 500px;
        padding: 10px 14px;
        border: 2px solid #ddd;
        border-radius: 8px;
        font-size: 14px;
        color: #1a1a1a;
        background: #fff;
        outline: none;
        appearance: none;
        transition: border-color .15s;
    }
    .pi-field select:focus,
    .pi-field input[type="text"]:focus,
    .pi-field input[type="file"]:focus { border-color: #F2E33B; }
    .pi-hint { font-size: 12px; color: #999; margin-top: 5px; }

    /* ── Buttons ── */
    .pi-btn {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 11px 22px;
        border-radius: 8px;
        font-size: 14px; font-weight: 700;
        border: none; cursor: pointer;
        transition: all .15s; text-decoration: none;
    }
    .pi-btn-primary  { background: #F2E33B; color: #1a1a1a; }
    .pi-btn-primary:hover  { background: #C9BC1F; color: #1a1a1a; }
    .pi-btn-secondary { background: #e5e5e5; color: #444; }
    .pi-btn-secondary:hover { background: #d5d5d5; }
    .pi-btn-danger   { background: #e53e3e; color: #fff; }
    .pi-btn-danger:hover { background: #c0392b; }
    .pi-btn:disabled { opacity: .45; cursor: not-allowed; }
    .pi-actions {
        display: flex; gap: 12px; align-items: center;
        margin-top: 28px; padding-top: 24px;
        border-top: 1px solid #e5e5e5;
    }

    /* ── Type selector badges ── */
    .pi-type-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 10px;
        margin-bottom: 24px;
    }
    .pi-type-card {
        padding: 14px 12px;
        background: #fff;
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        cursor: pointer;
        text-align: center;
        transition: border-color .15s, background .15s;
    }
    .pi-type-card:hover { border-color: #C9BC1F; background: #fffde8; }
    .pi-type-card.selected { border-color: #F2E33B; background: #fffde0; }
    .pi-type-card input[type="radio"] { display: none; }
    .pi-type-icon { font-size: 22px; display: block; margin-bottom: 6px; }
    .pi-type-label { font-size: 12px; font-weight: 700; color: #333; }
    .pi-type-desc  { font-size: 11px; color: #999; margin-top: 3px; line-height: 1.4; }

    /* ── Preview table ── */
    .pi-table-wrap {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        overflow-x: auto;
        margin-bottom: 18px;
    }
    .pi-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .pi-table th {
        background: #1a1a1a; color: #F2E33B;
        padding: 9px 14px; text-align: left;
        font-size: 11px; text-transform: uppercase;
        letter-spacing: .06em; font-weight: 700;
        white-space: nowrap;
    }
    .pi-table td {
        padding: 9px 14px;
        border-bottom: 1px solid #f0f0f0;
        max-width: 220px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .pi-table tr:last-child td { border-bottom: none; }
    .pi-table tr:nth-child(even) td { background: #f6f6f6; }

    /* ── Post list ── */
    .pi-post-list {
        max-height: 300px; overflow-y: auto;
        border: 2px solid #ddd; border-radius: 8px;
        background: #fff; margin-bottom: 14px;
    }
    .pi-post-item {
        display: flex; align-items: center; gap: 12px;
        padding: 11px 16px; border-bottom: 1px solid #f0f0f0;
        cursor: pointer; transition: background .12s;
    }
    .pi-post-item:last-child { border-bottom: none; }
    .pi-post-item:hover { background: #fffde0; }
    .pi-post-item.selected { background: #fffbe0; border-left: 3px solid #F2E33B; }
    .pi-post-item input[type="radio"] { accent-color: #C9BC1F; flex-shrink: 0; }
    .pi-post-title { font-size: 13px; font-weight: 600; }
    .pi-post-meta  { font-size: 11px; color: #999; margin-top: 2px; }

    /* ── Search box ── */
    .pi-search {
        width: 100%; max-width: 500px;
        padding: 9px 14px; border: 2px solid #ddd; border-radius: 8px;
        font-size: 14px; outline: none; margin-bottom: 12px;
    }
    .pi-search:focus { border-color: #F2E33B; }

    /* ── Mapping ── */
    .pi-map-table { width: 100%; border-collapse: collapse; }
    .pi-map-table thead tr { background: #1a1a1a; }
    .pi-map-table th {
        padding: 9px 14px; color: #F2E33B;
        font-size: 11px; text-transform: uppercase;
        letter-spacing: .06em; text-align: left; font-weight: 700;
    }
    .pi-map-table td {
        padding: 8px 10px;
        border-bottom: 1px solid #eee;
        vertical-align: middle;
    }
    .pi-map-table tr:last-child td { border-bottom: none; }
    .pi-map-table tr:nth-child(even) td { background: #f7f7f7; }
    .pi-map-col-name {
        font-size: 13px; font-weight: 600;
        font-family: monospace; color: #333;
        background: #f0f0f0; padding: 5px 10px;
        border-radius: 5px; white-space: nowrap;
    }
    .pi-map-select {
        width: 100%;
        padding: 7px 10px;
        border: 2px solid #ddd; border-radius: 6px;
        font-size: 13px; background: #fff; outline: none;
    }
    .pi-map-select:focus { border-color: #F2E33B; }
    .pi-map-sample { font-size: 12px; color: #999; font-style: italic; }

    /* ── Notice ── */
    .pi-notice {
        padding: 13px 16px; border-radius: 8px;
        font-size: 13px; margin-bottom: 18px;
        display: flex; align-items: flex-start; gap: 10px;
        line-height: 1.5;
    }
    .pi-notice-success { background: #e8f5e9; border: 1px solid #66bb6a; color: #1b5e20; }
    .pi-notice-error   { background: #ffebee; border: 1px solid #ef5350; color: #7f0000; }
    .pi-notice-info    { background: #fffde7; border: 1px solid #F2E33B; color: #555; }
    .pi-notice-warn    { background: #fff3e0; border: 1px solid #ffa726; color: #6d3a00; }

    /* ── Summary ── */
    .pi-summary {
        display: grid; grid-template-columns: repeat(4,1fr);
        gap: 12px; margin-bottom: 22px;
    }
    .pi-summary-card {
        background: #fff; border: 1px solid #e0e0e0;
        border-radius: 10px; padding: 16px; text-align: center;
    }
    .pi-summary-card .num { font-size: 28px; font-weight: 800; color: #1a1a1a; line-height: 1; }
    .pi-summary-card .lbl { font-size: 11px; color: #999; margin-top: 4px; text-transform: uppercase; letter-spacing: .05em; }

    /* ── Progress ── */
    .pi-progress { display: none; margin-bottom: 20px; }
    .pi-progress-label { font-size: 13px; color: #666; margin-bottom: 8px; }
    .pi-progress-outer {
        background: #e0e0e0; border-radius: 99px;
        height: 8px; overflow: hidden;
    }
    .pi-progress-inner {
        height: 100%; background: #F2E33B;
        border-radius: 99px; width: 0;
        transition: width .3s ease;
    }

    /* ── Loader ── */
    .pi-loader {
        display: none; text-align: center;
        padding: 36px; color: #999; font-size: 13px;
    }
    .pi-spinner {
        width: 36px; height: 36px;
        border: 3px solid #eee;
        border-top-color: #F2E33B;
        border-radius: 50%;
        animation: piSpin .7s linear infinite;
        margin: 0 auto 14px;
    }
    @keyframes piSpin { to { transform: rotate(360deg); } }

    /* ── Badge ── */
    .pi-badge {
        display: inline-block;
        background: #1a1a1a; color: #F2E33B;
        font-size: 11px; font-weight: 700;
        padding: 3px 9px; border-radius: 4px; letter-spacing: .04em;
    }
    .pi-badge-green {
        background: #e8f5e9; color: #2e7d32;
        border: 1px solid #a5d6a7;
    }
    .pi-badge-yellow {
        background: #fffde7; color: #f57f17;
        border: 1px solid #ffe082;
    }

    /* ── Bulk results ── */
    .pi-bulk-results { max-height: 300px; overflow-y: auto; }
    .pi-bulk-results .pi-table td.ok  { color: #2e7d32; font-weight: 600; }
    .pi-bulk-results .pi-table td.err { color: #c62828; }

    /* ── Divider ── */
    .pi-divider {
        border: none; border-top: 1px solid #e5e5e5;
        margin: 24px 0;
    }

    /* ── Optgroup ── */
    optgroup { font-weight: 700; color: #333; }
    </style>
    <?php
} );

// ────────────────────────────────────────────────────────────────────────────
// AJAX HANDLERS
// ────────────────────────────────────────────────────────────────────────────

// ── Get all ACF fields for a CPT (grouped by type) ──────────────────────────
add_action( 'wp_ajax_acfpi_get_fields', function () {
    check_ajax_referer( 'acfpi_nonce', 'nonce' );
    $post_type = sanitize_key( $_POST['post_type'] ?? '' );
    if ( ! $post_type ) wp_send_json_error( 'No post type.' );

    if ( ! function_exists( 'acf_get_field_groups' ) ) {
        wp_send_json_error( 'ACF not active.' );
    }

    $groups  = acf_get_field_groups( [ 'post_type' => $post_type ] );
    $scalars = [];
    $repeaters = [];
    $taxfields = [];
    $relfields = [];

    foreach ( $groups as $group ) {
        $fields = acf_get_fields( $group['key'] );
        if ( ! $fields ) continue;
        foreach ( $fields as $f ) {
            $item = [ 'key' => $f['name'], 'label' => $f['label'], 'type' => $f['type'] ];
            if ( $f['type'] === 'repeater' ) {
                $subs = [];
                foreach ( $f['sub_fields'] as $sf ) {
                    $subs[] = [ 'key' => $sf['name'], 'label' => $sf['label'], 'type' => $sf['type'] ];
                }
                $item['sub_fields'] = $subs;
                $repeaters[] = $item;
            } elseif ( $f['type'] === 'taxonomy' ) {
                $item['taxonomy'] = $f['taxonomy'] ?? 'category';
                $item['field_type'] = $f['field_type'] ?? 'checkbox';
                $taxfields[] = $item;
            } elseif ( $f['type'] === 'relationship' || $f['type'] === 'post_object' ) {
                $item['post_type'] = $f['post_type'] ?? [];
                $relfields[] = $item;
            } else {
                $scalars[] = $item;
            }
        }
    }

    // also get registered taxonomies for the CPT
    $taxonomies = get_object_taxonomies( $post_type, 'objects' );
    $tax_list = [];
    foreach ( $taxonomies as $slug => $obj ) {
        $tax_list[] = [ 'slug' => $slug, 'label' => $obj->label ];
    }

    wp_send_json_success( compact( 'scalars', 'repeaters', 'taxfields', 'relfields', 'tax_list' ) );
} );

// ── Get posts ────────────────────────────────────────────────────────────────
add_action( 'wp_ajax_acfpi_get_posts', function () {
    check_ajax_referer( 'acfpi_nonce', 'nonce' );
    $pt = sanitize_key( $_POST['post_type'] ?? '' );
    if ( ! $pt ) wp_send_json_error();
    $posts = get_posts( [ 'post_type' => $pt, 'posts_per_page' => -1,
                          'post_status' => 'any', 'orderby' => 'title', 'order' => 'ASC' ] );
    wp_send_json_success( array_map( fn($p) => [
        'id' => $p->ID, 'title' => $p->post_title,
        'slug' => $p->post_name, 'status' => $p->post_status,
    ], $posts ) );
} );

// ── Parse CSV ────────────────────────────────────────────────────────────────
add_action( 'wp_ajax_acfpi_parse_csv', function () {
    check_ajax_referer( 'acfpi_nonce', 'nonce' );
    if ( empty( $_FILES['file'] ) || $_FILES['file']['error'] !== UPLOAD_ERR_OK ) {
        wp_send_json_error( 'Upload error.' );
    }
    $ext = strtolower( pathinfo( $_FILES['file']['name'], PATHINFO_EXTENSION ) );
    if ( ! in_array( $ext, [ 'csv', 'txt' ] ) ) {
        wp_send_json_error( 'Only CSV files are accepted. Please export your spreadsheet as CSV first.' );
    }
    $h = fopen( $_FILES['file']['tmp_name'], 'r' );
    if ( ! $h ) wp_send_json_error( 'Cannot read file.' );

    $headers = null; $rows = [];
    while ( ( $row = fgetcsv( $h ) ) !== false ) {
        $row = array_map( 'trim', $row );
        if ( $headers === null ) { $headers = $row; continue; }
        if ( array_filter( $row ) ) {
            $rows[] = array_combine( $headers, array_pad( $row, count( $headers ), '' ) );
        }
    }
    fclose( $h );
    if ( ! $headers ) wp_send_json_error( 'File is empty.' );
    wp_send_json_success( [ 'headers' => $headers, 'rows' => $rows, 'count' => count( $rows ) ] );
} );

// ── Do import ────────────────────────────────────────────────────────────────
add_action( 'wp_ajax_acfpi_import', function () {
    check_ajax_referer( 'acfpi_nonce', 'nonce' );

    $import_type = sanitize_key( $_POST['import_type'] ?? '' ); // scalar | repeater | taxonomy | relationship
    $mode        = sanitize_key( $_POST['mode']        ?? 'single' ); // single | bulk
    $post_id     = intval( $_POST['post_id'] ?? 0 );
    $mapping     = json_decode( stripslashes( $_POST['mapping'] ?? '{}' ), true );
    $rows        = json_decode( stripslashes( $_POST['rows']    ?? '[]' ), true );
    $options     = json_decode( stripslashes( $_POST['options'] ?? '{}' ), true );

    if ( ! function_exists( 'update_field' ) ) {
        wp_send_json_error( 'ACF not active.' );
    }
    if ( ! $rows ) wp_send_json_error( 'No data to import.' );

    $results = [];

    if ( $mode === 'bulk' ) {
        // ── Bulk: each CSV row = one post ──
        $id_col    = $options['id_col']    ?? '';
        $title_col = $options['title_col'] ?? '';
        $group_col = $options['group_col'] ?? ''; // for grouped repeater imports

        // ── Grouped repeater: group rows by identifier column first ──────────
        if ( $import_type === 'repeater' && $group_col ) {
            $groups = [];
            foreach ( $rows as $row ) {
                $key = trim( $row[ $group_col ] ?? '' );
                if ( $key !== '' ) {
                    $groups[ $key ][] = $row;
                }
            }

            $post_type_slug = sanitize_key( $_POST['post_type'] ?? 'any' );
            foreach ( $groups as $identifier => $grouped_rows ) {
                // find post by title or ID
                $pid = 0;
                if ( is_numeric( $identifier ) && intval( $identifier ) > 0 ) {
                    $pid = intval( $identifier );
                } else {
                    $found = get_posts( [
                        'post_type'   => $post_type_slug,
                        'title'       => $identifier,
                        'numberposts' => 1,
                        'post_status' => 'any',
                    ] );
                    if ( $found ) $pid = $found[0]->ID;
                }

                if ( ! $pid ) {
                    $results[] = [
                        'row'    => '—',
                        'post'   => $identifier,
                        'status' => 'error',
                        'msg'    => 'Post not found (' . count( $grouped_rows ) . ' rows skipped)',
                    ];
                    continue;
                }

                $err = acfpi_apply_row( $pid, 'repeater', $mapping, $grouped_rows, $options );
                $results[] = [
                    'row'    => count( $grouped_rows ) . ' rows',
                    'post'   => get_the_title( $pid ),
                    'id'     => $pid,
                    'status' => $err ? 'error' : 'ok',
                    'msg'    => $err ?: count( $grouped_rows ) . ' row(s) imported',
                ];
            }

            $ok  = count( array_filter( $results, fn($r) => $r['status'] === 'ok' ) );
            $err = count( $results ) - $ok;
            wp_send_json_success( [ 'mode' => 'bulk', 'results' => $results, 'ok' => $ok, 'errors' => $err ] );
        }

        // ── Standard bulk: one row = one post ────────────────────────────────
        foreach ( $rows as $idx => $row ) {
            $pid = 0;
            if ( $id_col && ! empty( $row[ $id_col ] ) ) {
                $pid = intval( $row[ $id_col ] );
            } elseif ( $title_col && ! empty( $row[ $title_col ] ) ) {
                $found = get_posts( [
                    'post_type'   => sanitize_key( $_POST['post_type'] ?? 'any' ),
                    'title'       => $row[ $title_col ],
                    'numberposts' => 1,
                    'post_status' => 'any',
                ] );
                if ( $found ) $pid = $found[0]->ID;
            }

            if ( ! $pid ) {
                $results[] = [ 'row' => $idx + 2, 'post' => $row[ $title_col ?? $id_col ] ?? '?', 'status' => 'error', 'msg' => 'Post not found' ];
                continue;
            }

            $err = acfpi_apply_row( $pid, $import_type, $mapping, [ $row ], $options );
            $results[] = [ 'row' => $idx + 2, 'post' => get_the_title( $pid ), 'id' => $pid, 'status' => $err ? 'error' : 'ok', 'msg' => $err ?: 'Imported' ];
        }

        $ok  = count( array_filter( $results, fn($r) => $r['status'] === 'ok' ) );
        $err = count( $results ) - $ok;
        wp_send_json_success( [ 'mode' => 'bulk', 'results' => $results, 'ok' => $ok, 'errors' => $err ] );

    } else {
        // ── Single post ──
        if ( ! $post_id ) wp_send_json_error( 'No post selected.' );
        $err = acfpi_apply_row( $post_id, $import_type, $mapping, $rows, $options );
        if ( $err ) wp_send_json_error( $err );
        wp_send_json_success( [
            'mode'       => 'single',
            'post_title' => get_the_title( $post_id ),
            'post_id'    => $post_id,
            'rows'       => count( $rows ),
        ] );
    }
} );

// ── Create new posts from CSV ────────────────────────────────────────────────
add_action( 'wp_ajax_acfpi_create_posts', function () {
    check_ajax_referer( 'acfpi_nonce', 'nonce' );

    $post_type   = sanitize_key( $_POST['post_type']   ?? '' );
    $post_status = sanitize_key( $_POST['post_status'] ?? 'publish' );
    $title_col   = sanitize_text_field( $_POST['title_col'] ?? '' );
    $slug_col    = sanitize_text_field( $_POST['slug_col']  ?? '' );
    $mapping     = json_decode( stripslashes( $_POST['mapping'] ?? '{}' ), true );
    $rows        = json_decode( stripslashes( $_POST['rows']    ?? '[]' ), true );
    $import_type = sanitize_key( $_POST['import_type'] ?? 'scalar' );
    $options     = json_decode( stripslashes( $_POST['options'] ?? '{}' ), true );
    $skip_dupes  = ! empty( $_POST['skip_dupes'] );
    $update_dupes = ! empty( $_POST['update_dupes'] );

    if ( ! function_exists( 'update_field' ) ) wp_send_json_error( 'ACF not active.' );
    if ( ! $post_type )  wp_send_json_error( 'No post type specified.' );
    if ( ! $title_col )  wp_send_json_error( 'No title column specified.' );
    if ( ! $rows )       wp_send_json_error( 'No rows to import.' );

    $results  = [];
    $created  = 0;
    $updated  = 0;
    $skipped  = 0;
    $errors   = 0;

    foreach ( $rows as $idx => $row ) {
        $title = trim( $row[ $title_col ] ?? '' );
        if ( ! $title ) {
            $results[] = [ 'row' => $idx + 2, 'title' => '(empty)', 'status' => 'skip', 'msg' => 'Empty title — skipped' ];
            $skipped++;
            continue;
        }

        // check for existing post with same title
        $existing = get_posts( [
            'post_type'   => $post_type,
            'title'       => $title,
            'numberposts' => 1,
            'post_status' => 'any',
            'fields'      => 'ids',
        ] );

        $post_id = 0;

        if ( $existing ) {
            if ( $skip_dupes ) {
                $results[] = [ 'row' => $idx + 2, 'title' => $title, 'status' => 'skip', 'msg' => 'Already exists — skipped' ];
                $skipped++;
                continue;
            } elseif ( $update_dupes ) {
                $post_id = $existing[0];
            }
        }

        if ( ! $post_id ) {
            // build post args
            $post_args = [
                'post_title'   => $title,
                'post_type'    => $post_type,
                'post_status'  => $post_status,
                'post_content' => '',
            ];
            if ( $slug_col && ! empty( $row[ $slug_col ] ) ) {
                $post_args['post_name'] = sanitize_title( $row[ $slug_col ] );
            }
            $post_id = wp_insert_post( $post_args, true );
            if ( is_wp_error( $post_id ) ) {
                $results[] = [ 'row' => $idx + 2, 'title' => $title, 'status' => 'error', 'msg' => $post_id->get_error_message() ];
                $errors++;
                continue;
            }
            $created++;
        } else {
            $updated++;
        }

        // now apply fields to the new/updated post
        $err = acfpi_apply_row( $post_id, $import_type, $mapping, [ $row ], $options );
        $status = $err ? 'error' : ( $existing && $update_dupes ? 'updated' : 'created' );
        $msg    = $err ?: ( $status === 'updated' ? 'Updated' : 'Created (ID: ' . $post_id . ')' );
        if ( $err ) $errors++;

        $results[] = [
            'row'    => $idx + 2,
            'title'  => $title,
            'id'     => $post_id,
            'status' => $status,
            'msg'    => $msg,
            'link'   => get_edit_post_link( $post_id ),
        ];
    }

    wp_send_json_success( compact( 'results', 'created', 'updated', 'skipped', 'errors' ) );
} );

// ── Core import logic ────────────────────────────────────────────────────────
function acfpi_apply_row( $post_id, $import_type, $mapping, $rows, $options ) {
    if ( ! get_post( $post_id ) ) return 'Post ID ' . $post_id . ' not found.';

    switch ( $import_type ) {

        case 'scalar':
            // one row → one post, each mapped column → one ACF field
            $row = $rows[0] ?? [];
            foreach ( $mapping as $csv_col => $acf_key ) {
                if ( ! $acf_key || $acf_key === '__skip__' ) continue;
                $val = $row[ $csv_col ] ?? '';
                update_field( $acf_key, $val, $post_id );
            }
            break;

        case 'repeater':
            $repeater_key = $options['repeater_key'] ?? '';
            if ( ! $repeater_key ) return 'No repeater key specified.';
            $replace = ( $options['repeat_mode'] ?? 'append' ) === 'replace';

            $new_rows = [];
            foreach ( $rows as $row ) {
                $acf_row = [];
                foreach ( $mapping as $csv_col => $acf_sub ) {
                    if ( $acf_sub && $acf_sub !== '__skip__' ) {
                        $acf_row[ $acf_sub ] = $row[ $csv_col ] ?? '';
                    }
                }
                if ( array_filter( $acf_row ) ) $new_rows[] = $acf_row;
            }

            if ( $replace ) {
                update_field( $repeater_key, $new_rows, $post_id );
            } else {
                $existing = get_field( $repeater_key, $post_id ) ?: [];
                update_field( $repeater_key, array_merge( $existing, $new_rows ), $post_id );
            }
            break;

        case 'taxonomy':
            $tax         = $options['taxonomy'] ?? '';
            $csv_col     = $options['csv_col']  ?? '';
            $acf_key     = $options['acf_key']  ?? '';
            $use_acf     = ! empty( $acf_key );
            $append_mode = ( $options['tax_mode'] ?? 'replace' ) === 'append';

            if ( ! $tax ) return 'No taxonomy specified.';

            $row = $rows[0] ?? [];
            // support multi-row bulk: take the value from the mapped column
            $raw_val = '';
            foreach ( $rows as $r ) {
                if ( $csv_col && isset( $r[ $csv_col ] ) ) { $raw_val = $r[ $csv_col ]; break; }
            }

            $term_names = array_filter( array_map( 'trim', preg_split('/[,;|]+/', $raw_val ) ) );
            $term_ids   = [];
            foreach ( $term_names as $name ) {
                $term = get_term_by( 'name', $name, $tax );
                if ( ! $term ) {
                    $ins = wp_insert_term( $name, $tax );
                    if ( ! is_wp_error( $ins ) ) $term_ids[] = $ins['term_id'];
                } else {
                    $term_ids[] = $term->term_id;
                }
            }

            if ( $use_acf ) {
                update_field( $acf_key, $term_ids, $post_id );
            } else {
                wp_set_object_terms( $post_id, $term_ids, $tax, $append_mode );
            }
            break;

        case 'relationship':
            $acf_key   = $options['acf_key']  ?? '';
            $csv_col   = $options['csv_col']  ?? '';
            $match_by  = $options['match_by'] ?? 'title'; // title | id
            $rel_pt    = $options['rel_pt']   ?? '';
            $rep_mode  = ( $options['rel_mode'] ?? 'replace' ) === 'replace';

            if ( ! $acf_key ) return 'No ACF field key specified.';

            $raw_val = '';
            foreach ( $rows as $r ) {
                if ( $csv_col && isset( $r[ $csv_col ] ) ) { $raw_val = $r[ $csv_col ]; break; }
            }

            $values = array_filter( array_map( 'trim', preg_split( '/[,;|]+/', $raw_val ) ) );
            $post_ids = [];
            foreach ( $values as $v ) {
                if ( $match_by === 'id' ) {
                    $post_ids[] = intval( $v );
                } else {
                    $found = get_posts( [
                        'post_type' => $rel_pt ?: 'any',
                        'title'     => $v,
                        'numberposts' => 1,
                        'post_status' => 'any',
                    ] );
                    if ( $found ) $post_ids[] = $found[0]->ID;
                }
            }

            $final = $post_ids;
            if ( ! $rep_mode ) {
                $existing = get_field( $acf_key, $post_id ) ?: [];
                $existing_ids = array_map( fn($p) => is_object($p) ? $p->ID : intval($p), $existing );
                $final = array_unique( array_merge( $existing_ids, $post_ids ) );
            }

            update_field( $acf_key, $final, $post_id );
            break;

        default:
            return 'Unknown import type: ' . $import_type;
    }

    return null; // no error
}

// ────────────────────────────────────────────────────────────────────────────
// PAGE RENDER
// ────────────────────────────────────────────────────────────────────────────
function acfpi_render_page() {
    if ( ! current_user_can( 'manage_options' ) ) return;

    $cpts = get_post_types( [ 'public' => true ], 'objects' );
    unset( $cpts['attachment'] );
    $nonce = wp_create_nonce( 'acfpi_nonce' );
    $ajax  = admin_url( 'admin-ajax.php' );
    ?>
    <div id="acfpi-wrap">

        <!-- Header -->
        <div class="pi-header">
            <div class="pi-header-icon">⚡</div>
            <div>
                <h1>ACF Pro Importer</h1>
                <p>Import scalar fields, repeaters, taxonomies, and relationships from CSV — single post or bulk</p>
            </div>
        </div>

        <!-- Tabs -->
        <div class="pi-tabs" id="pi-tabs">
            <div class="pi-tab active" data-tab="import">Import Wizard</div>
            <div class="pi-tab" data-tab="log">Import Log</div>
            <div class="pi-tab" data-tab="help">Help &amp; Guide</div>
        </div>

        <!-- ── TAB: Import Wizard ── -->
        <div class="pi-body" id="tab-import">

            <!-- Steps indicator -->
            <div class="pi-steps" id="pi-steps-bar" style="margin: -36px -36px 36px; border-radius: 0;">
                <div class="pi-step-item active" data-step="1"><span class="snum">1</span> Setup</div>
                <div class="pi-step-arrow">›</div>
                <div class="pi-step-item" data-step="2"><span class="snum">2</span> Upload</div>
                <div class="pi-step-arrow">›</div>
                <div class="pi-step-item" data-step="3"><span class="snum">3</span> Target Post</div>
                <div class="pi-step-arrow">›</div>
                <div class="pi-step-item" data-step="4"><span class="snum">4</span> Map Fields</div>
                <div class="pi-step-arrow">›</div>
                <div class="pi-step-item" data-step="5"><span class="snum">5</span> Import</div>
            </div>

            <!-- ═══ STEP 1: Setup ═══ -->
            <div class="pi-panel active" id="step-1">
                <h2>Configure Import</h2>
                <p class="pi-sub">Choose a post type, the type of data you're importing, and whether this is a single-post or bulk (multi-post) import.</p>

                <!-- Post Type -->
                <div class="pi-field">
                    <label>Post Type</label>
                    <select id="pi-cpt">
                        <option value="">— Select a post type —</option>
                        <?php foreach ( $cpts as $slug => $obj ) : ?>
                            <option value="<?php echo esc_attr($slug); ?>">
                                <?php echo esc_html($obj->labels->singular_name); ?> (<?php echo esc_html($slug); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Import type -->
                <div class="pi-field">
                    <label>What are you importing?</label>
                    <div class="pi-type-grid">
                        <label class="pi-type-card selected">
                            <input type="radio" name="pi_import_type" value="scalar" checked>
                            <span class="pi-type-icon">📝</span>
                            <div class="pi-type-label">Main Fields</div>
                            <div class="pi-type-desc">Text, images, numbers, URLs and other scalar ACF fields</div>
                        </label>
                        <label class="pi-type-card">
                            <input type="radio" name="pi_import_type" value="repeater">
                            <span class="pi-type-icon">🔁</span>
                            <div class="pi-type-label">Repeater</div>
                            <div class="pi-type-desc">One CSV row = one repeater row</div>
                        </label>
                        <label class="pi-type-card">
                            <input type="radio" name="pi_import_type" value="taxonomy">
                            <span class="pi-type-icon">🏷️</span>
                            <div class="pi-type-label">Taxonomy</div>
                            <div class="pi-type-desc">Assign tags, categories, or custom terms</div>
                        </label>
                        <label class="pi-type-card">
                            <input type="radio" name="pi_import_type" value="relationship">
                            <span class="pi-type-icon">🔗</span>
                            <div class="pi-type-label">Relationship</div>
                            <div class="pi-type-desc">Link posts to other posts by title or ID</div>
                        </label>
                    </div>
                </div>

                <!-- Mode -->
                <div class="pi-field">
                    <label>Import Mode</label>
                    <div class="pi-mode-row" style="flex-direction:column;gap:10px;">
                        <label class="pi-mode-card selected" id="mode-single-card">
                            <input type="radio" name="pi_mode" value="single" checked>
                            <div class="pi-mode-icon">🎯</div>
                            <div>
                                <div class="pi-mode-title">Single Post — Update</div>
                                <div class="pi-mode-desc">Import all rows into one selected post. Best for repeater data.</div>
                            </div>
                        </label>
                        <label class="pi-mode-card" id="mode-bulk-card">
                            <input type="radio" name="pi_mode" value="bulk">
                            <div class="pi-mode-icon">📦</div>
                            <div>
                                <div class="pi-mode-title">Bulk Update — Existing Posts</div>
                                <div class="pi-mode-desc">One CSV row = one post. Updates existing posts matched by title or ID column.</div>
                            </div>
                        </label>
                        <label class="pi-mode-card" id="mode-create-card">
                            <input type="radio" name="pi_mode" value="create">
                            <div class="pi-mode-icon">✨</div>
                            <div>
                                <div class="pi-mode-title">Create New Posts</div>
                                <div class="pi-mode-desc">One CSV row = one new post. Creates posts and populates all ACF fields in one step. Optionally skip or update duplicates.</div>
                            </div>
                        </label>
                    </div>
                </div>

                <div id="pi-step1-error" class="pi-notice pi-notice-error" style="display:none;"></div>
                <div class="pi-actions">
                    <button class="pi-btn pi-btn-primary" id="btn-step1-next" disabled>Next: Upload CSV →</button>
                </div>
            </div>

            <!-- ═══ STEP 2: Upload ═══ -->
            <div class="pi-panel" id="step-2">
                <h2>Upload CSV File</h2>
                <p class="pi-sub">Upload a .csv file. The first row must be column headers. Save Excel files as CSV first via <em>File → Save As → CSV</em>.</p>

                <div class="pi-notice pi-notice-info" id="pi-upload-hint">
                    📋 <span id="pi-upload-hint-text">Loading hints…</span>
                </div>

                <div class="pi-field">
                    <label>Choose CSV File</label>
                    <input type="file" id="pi-file" accept=".csv,.txt">
                    <div class="pi-hint">Accepts .csv files · Max size: <?php echo ini_get('upload_max_filesize'); ?></div>
                </div>

                <div id="pi-upload-error"  class="pi-notice pi-notice-error"  style="display:none;"></div>
                <div id="pi-upload-loader" class="pi-loader"><div class="pi-spinner"></div>Reading file…</div>

                <div id="pi-csv-preview" style="display:none;">
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
                        <span class="pi-badge" id="pi-csv-row-badge"></span>
                        <span class="pi-badge pi-badge-green" id="pi-csv-col-badge"></span>
                        <span style="font-size:12px;color:#999;" id="pi-csv-filename"></span>
                    </div>
                    <div class="pi-table-wrap">
                        <table class="pi-table" id="pi-csv-table"></table>
                    </div>
                    <div class="pi-hint">Showing first 5 rows</div>
                </div>

                <div class="pi-actions">
                    <button class="pi-btn pi-btn-secondary" onclick="piGo(1)">← Back</button>
                    <button class="pi-btn pi-btn-primary" id="btn-step2-next" disabled>Next: Select Post →</button>
                </div>
            </div>

            <!-- ═══ STEP 3: Target Post ═══ -->
            <div class="pi-panel" id="step-3">
                <h2 id="step3-title">Select Target Post</h2>
                <p class="pi-sub" id="step3-sub">Choose which post to import data into.</p>

                <!-- Single post mode -->
                <div id="step3-single">
                    <input type="text" class="pi-search" id="pi-post-search" placeholder="🔍  Filter by title…">
                    <div id="pi-post-loader" class="pi-loader"><div class="pi-spinner"></div>Loading posts…</div>
                    <div class="pi-post-list" id="pi-post-list"></div>
                    <div id="pi-no-posts" class="pi-notice pi-notice-info" style="display:none;">ℹ️ No posts found for this post type.</div>
                </div>

                <!-- Bulk mode -->
                <div id="step3-bulk" style="display:none;">
                    <div class="pi-notice pi-notice-info">
                        📦 In bulk mode, each CSV row is matched to a post. You'll configure the matching column in the next step.
                    </div>
                    <div style="background:#fff;border:1px solid #e0e0e0;border-radius:8px;padding:18px;">
                        <div style="font-size:13px;font-weight:700;margin-bottom:12px;">CSV Rows to be Imported</div>
                        <div id="bulk-preview-list" style="font-size:13px;color:#555;"></div>
                    </div>
                </div>

                <!-- Create mode -->
                <div id="step3-create" style="display:none;">
                    <div class="pi-notice pi-notice-info">
                        ✨ Each CSV row will create a new post. Configure the options below.
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:18px;">
                        <div class="pi-field">
                            <label>Post Title Column <span style="color:#e53e3e;">*</span></label>
                            <select id="pi-create-title-col">
                                <option value="">— select column —</option>
                            </select>
                            <div class="pi-hint">The CSV column that will become the post title</div>
                        </div>
                        <div class="pi-field">
                            <label>Post Slug Column <span style="color:#999;">(optional)</span></label>
                            <select id="pi-create-slug-col">
                                <option value="">— auto-generate from title —</option>
                            </select>
                            <div class="pi-hint">Leave blank to auto-generate from title</div>
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:18px;">
                        <div class="pi-field">
                            <label>Post Status</label>
                            <select id="pi-create-status">
                                <option value="publish">Published</option>
                                <option value="draft">Draft</option>
                                <option value="private">Private</option>
                            </select>
                        </div>
                        <div class="pi-field">
                            <label>If Post Already Exists</label>
                            <select id="pi-create-dupe">
                                <option value="skip">Skip (don't create duplicate)</option>
                                <option value="update">Update existing post's fields</option>
                                <option value="create">Create anyway (allow duplicates)</option>
                            </select>
                        </div>
                    </div>

                    <div style="background:#fff;border:1px solid #e0e0e0;border-radius:8px;padding:18px;">
                        <div style="font-size:13px;font-weight:700;margin-bottom:10px;">Preview — first 5 rows</div>
                        <div id="create-preview-list" style="font-size:13px;color:#555;"></div>
                    </div>
                </div>

                <div class="pi-actions">
                    <button class="pi-btn pi-btn-secondary" onclick="piGo(2)">← Back</button>
                    <button class="pi-btn pi-btn-primary" id="btn-step3-next" disabled>Next: Map Fields →</button>
                </div>
            </div>

            <!-- ═══ STEP 4: Map Fields ═══ -->
            <div class="pi-panel" id="step-4">
                <h2>Map CSV Columns to Fields</h2>
                <p class="pi-sub" id="step4-sub">Match each CSV column to the correct ACF field or option.</p>

                <!-- Extra options (shown per import type) -->
                <div id="pi-extra-opts"></div>

                <div id="pi-fields-loader" class="pi-loader"><div class="pi-spinner"></div>Loading fields…</div>
                <div id="pi-map-wrap">
                    <div class="pi-table-wrap">
                        <table class="pi-map-table">
                            <thead><tr>
                                <th style="width:30%">CSV Column</th>
                                <th style="width:40%">Maps To</th>
                                <th style="width:30%">Sample Value</th>
                            </tr></thead>
                            <tbody id="pi-map-body"></tbody>
                        </table>
                    </div>
                </div>

                <div class="pi-actions">
                    <button class="pi-btn pi-btn-secondary" onclick="piGo(3)">← Back</button>
                    <button class="pi-btn pi-btn-primary" id="btn-step4-next">Next: Review & Import →</button>
                </div>
            </div>

            <!-- ═══ STEP 5: Import ═══ -->
            <div class="pi-panel" id="step-5">
                <h2>Review &amp; Run Import</h2>
                <p class="pi-sub">Everything looks good? Check the summary below then click Run Import.</p>

                <!-- Summary -->
                <div class="pi-summary" id="pi-summary">
                    <div class="pi-summary-card"><div class="num" id="sum-type">—</div><div class="lbl">Import Type</div></div>
                    <div class="pi-summary-card"><div class="num" id="sum-rows">—</div><div class="lbl">Rows</div></div>
                    <div class="pi-summary-card"><div class="num" id="sum-mapped">—</div><div class="lbl">Mapped Columns</div></div>
                    <div class="pi-summary-card"><div class="num" id="sum-target">—</div><div class="lbl">Target</div></div>
                </div>

                <!-- Write mode (for repeater) -->
                <div id="pi-write-mode-wrap" style="display:none;margin-bottom:22px;">
                    <div style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#555;margin-bottom:10px;">Write Mode (Repeater)</div>
                    <label class="pi-mode-card selected" style="margin-bottom:8px;" id="wm-append-card">
                        <input type="radio" name="pi_write_mode" value="append" checked>
                        <div><div class="pi-mode-title">➕ Append rows</div><div class="pi-mode-desc">Add to existing repeater data</div></div>
                    </label>
                    <label class="pi-mode-card" id="wm-replace-card">
                        <input type="radio" name="pi_write_mode" value="replace">
                        <div><div class="pi-mode-title">🔄 Replace all rows</div><div class="pi-mode-desc">Delete existing data and replace with CSV</div></div>
                    </label>
                </div>

                <!-- Taxonomy / relationship write mode -->
                <div id="pi-taxrel-mode-wrap" style="display:none;margin-bottom:22px;">
                    <div style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#555;margin-bottom:10px;">Write Mode</div>
                    <label class="pi-mode-card selected" style="margin-bottom:8px;" id="trm-replace-card">
                        <input type="radio" name="pi_taxrel_mode" value="replace" checked>
                        <div><div class="pi-mode-title">🔄 Replace</div><div class="pi-mode-desc">Overwrite existing terms / linked posts</div></div>
                    </label>
                    <label class="pi-mode-card" id="trm-append-card">
                        <input type="radio" name="pi_taxrel_mode" value="append">
                        <div><div class="pi-mode-title">➕ Append</div><div class="pi-mode-desc">Add to existing terms / linked posts</div></div>
                    </label>
                </div>

                <!-- Progress -->
                <div class="pi-progress" id="pi-progress">
                    <div class="pi-progress-label" id="pi-progress-label">Importing…</div>
                    <div class="pi-progress-outer"><div class="pi-progress-inner" id="pi-progress-inner"></div></div>
                </div>

                <!-- Result -->
                <div id="pi-result" style="display:none;"></div>

                <!-- Bulk result table -->
                <div id="pi-bulk-result" style="display:none;">
                    <div class="pi-table-wrap pi-bulk-results">
                        <table class="pi-table" id="pi-bulk-table"></table>
                    </div>
                </div>

                <div class="pi-actions">
                    <button class="pi-btn pi-btn-secondary" id="btn-step5-back" onclick="piGo(4)">← Back</button>
                    <button class="pi-btn pi-btn-primary"   id="btn-run-import">⚡ Run Import</button>
                    <button class="pi-btn pi-btn-secondary" id="btn-start-over" style="display:none;" onclick="location.reload()">Start Over</button>
                </div>
            </div>

        </div><!-- /#tab-import -->

        <!-- ── TAB: Log ── -->
        <div class="pi-body" id="tab-log" style="display:none;">
            <h2 style="margin:0 0 18px;">Import History</h2>
            <div id="pi-log-list">
                <div class="pi-notice pi-notice-info">No imports have been run yet in this session.</div>
            </div>
        </div>

        <!-- ── TAB: Help ── -->
        <div class="pi-body" id="tab-help" style="display:none;">
            <h2 style="margin:0 0 20px;">How to Use This Importer</h2>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">

                <div style="background:#fff;border:1px solid #e0e0e0;border-radius:10px;padding:20px;">
                    <div style="font-size:18px;margin-bottom:8px;">📝 Main Fields (Scalar)</div>
                    <div style="font-size:13px;color:#555;line-height:1.7;">
                        Use this for text, textarea, number, image URL, etc. fields.<br><br>
                        <strong>Single post mode:</strong> CSV must have one row of data. Each column maps to one ACF field.<br><br>
                        <strong>Bulk mode:</strong> Each CSV row updates one post. Include a <code>post_title</code> or <code>post_id</code> column so the importer knows which post to update.<br><br>
                        <strong>Create mode:</strong> Each CSV row creates a new post and populates all mapped ACF fields at once.
                    </div>
                </div>

                <div style="background:#fff;border:1px solid #e0e0e0;border-radius:10px;padding:20px;">
                    <div style="font-size:18px;margin-bottom:8px;">🔁 Repeater Fields</div>
                    <div style="font-size:13px;color:#555;line-height:1.7;">
                        Use this to populate rows in an ACF repeater.<br><br>
                        <strong>Single post mode:</strong> Every CSV row becomes one repeater row in the selected post.<br><br>
                        <strong>Bulk mode:</strong> Include a <code>post_title</code> column. All rows sharing the same post title will be added as repeater rows for that post.<br><br>
                        <strong>Create mode:</strong> Creates a new post and adds the first CSV row as a repeater row.
                    </div>
                </div>

                <div style="background:#fff;border:1px solid #e0e0e0;border-radius:10px;padding:20px;">
                    <div style="font-size:18px;margin-bottom:8px;">🏷️ Taxonomy</div>
                    <div style="font-size:13px;color:#555;line-height:1.7;">
                        Assigns taxonomy terms to posts.<br><br>
                        Your CSV column should contain term names separated by commas, e.g.: <code>Buying, Selling</code><br><br>
                        Terms that don't exist will be <strong>created automatically</strong>. You can use an ACF taxonomy field or assign directly to the post's taxonomy.
                    </div>
                </div>

                <div style="background:#fff;border:1px solid #e0e0e0;border-radius:10px;padding:20px;">
                    <div style="font-size:18px;margin-bottom:8px;">🔗 Relationship</div>
                    <div style="font-size:13px;color:#555;line-height:1.7;">
                        Links posts to other posts via ACF relationship or post_object fields.<br><br>
                        Your CSV column should contain post titles or IDs separated by commas, e.g.: <code>Sherman Oaks, Beverly Hills</code><br><br>
                        Match by <strong>Title</strong> (default) or <strong>Post ID</strong>.
                    </div>
                </div>

                <div style="background:#fff;border:1px solid #e0e0e0;border-radius:10px;padding:20px;grid-column:1/-1;">
                    <div style="font-size:18px;margin-bottom:8px;">✨ Create New Posts Mode</div>
                    <div style="font-size:13px;color:#555;line-height:1.7;">
                        The most powerful mode — creates brand new posts from scratch and populates all fields in one shot.<br><br>
                        <strong>How it works:</strong><br>
                        1. Choose your CPT and field type<br>
                        2. Upload your CSV (e.g. Sheet 1 — city main data)<br>
                        3. Set the <strong>title column</strong> (e.g. <code>post_title</code>), optional slug column, post status, and duplicate handling<br>
                        4. Map columns to ACF fields<br>
                        5. Run — posts are created and fields populated instantly<br><br>
                        <strong>Duplicate handling options:</strong><br>
                        · <em>Skip</em> — if a post with the same title already exists, skip that row<br>
                        · <em>Update</em> — if it already exists, update its ACF fields instead of creating a new one<br>
                        · <em>Allow</em> — create a new post regardless (may create duplicates)
                    </div>
                </div>

            </div>

            <div style="margin-top:20px;background:#1a1a1a;color:#F2E33B;border-radius:10px;padding:20px;">
                <div style="font-weight:700;margin-bottom:10px;">💡 Pro Tips</div>
                <ul style="margin:0;padding-left:18px;font-size:13px;line-height:2;color:#ccc;">
                    <li>Column headers in your CSV don't need to match ACF field names — you map them manually in step 4.</li>
                    <li>Columns set to <em>Skip</em> are safely ignored.</li>
                    <li>For images, import the full URL. The field must be set to return a URL in ACF settings.</li>
                    <li>For bulk repeater imports (e.g. all schools for all 30 cities), include a <code>city_name</code> column and use Bulk mode.</li>
                    <li>All imports are logged in the Import Log tab during your session.</li>
                </ul>
            </div>
        </div>

    </div><!-- /#acfpi-wrap -->

    <script>
    (function($){

    var STATE = {
        step:        1,
        cpt:         '',
        import_type: 'scalar',
        mode:        'single',
        csv:         null,
        post:        null,
        fields:      null,
        mapping:     {},
        options:     {},
        nonce:       '<?php echo $nonce; ?>',
        ajaxUrl:     '<?php echo $ajax; ?>',
    };
    var _allPosts = [];
    var _importLog = [];

    // ── Tabs ────────────────────────────────────────────────────────────────
    $('.pi-tab').on('click', function(){
        var tab = $(this).data('tab');
        $('.pi-tab').removeClass('active');
        $(this).addClass('active');
        $('#tab-import,#tab-log,#tab-help').hide();
        $('#tab-' + tab).show();
    });

    // ── Steps ───────────────────────────────────────────────────────────────
    window.piGo = function(step) {
        $('.pi-panel').removeClass('active');
        $('#step-' + step).addClass('active');
        $('.pi-step-item').each(function(){
            var s = parseInt($(this).data('step'));
            $(this).removeClass('active done');
            if (s === step) $(this).addClass('active');
            if (s < step)   $(this).addClass('done');
        });
        STATE.step = step;
    };

    // ── Step 1: Setup ────────────────────────────────────────────────────────
    $('#pi-cpt').on('change', function(){
        STATE.cpt = $(this).val();
        checkStep1();
    });

    $(document).on('change', 'input[name="pi_import_type"]', function(){
        STATE.import_type = $(this).val();
        $('.pi-type-card').removeClass('selected');
        $(this).closest('.pi-type-card').addClass('selected');
        checkStep1();
    });

    $(document).on('change', 'input[name="pi_mode"]', function(){
        STATE.mode = $(this).val();
        $('#mode-single-card,#mode-bulk-card,#mode-create-card').removeClass('selected');
        $(this).closest('.pi-mode-card').addClass('selected');
        checkStep1();
    });

    function checkStep1() {
        $('#btn-step1-next').prop('disabled', !STATE.cpt);
    }

    $('#btn-step1-next').on('click', function(){
        piGo(2);
        updateUploadHint();
    });

    function updateUploadHint() {
        var hints = {
            scalar:       'Each <strong>column</strong> maps to one ACF field. Use one data row for Single Post mode.',
            repeater:     'Each <strong>row</strong> becomes one repeater row. The first row is always the header.',
            taxonomy:     'Include a column with comma-separated term names, e.g. <code>Buying, Selling</code>',
            relationship: 'Include a column with comma-separated post titles or IDs, e.g. <code>Sherman Oaks, Beverly Hills</code>',
        };
        $('#pi-upload-hint-text').html( hints[STATE.import_type] || '' );
    }

    // ── Step 2: Upload ───────────────────────────────────────────────────────
    $('#pi-file').on('change', function(){
        var file = this.files[0];
        if (!file) return;
        $('#pi-upload-error').hide();
        $('#pi-csv-preview').hide();
        $('#btn-step2-next').prop('disabled', true);
        $('#pi-upload-loader').show();

        var fd = new FormData();
        fd.append('action', 'acfpi_parse_csv');
        fd.append('nonce',  STATE.nonce);
        fd.append('file',   file);

        $.ajax({ url: STATE.ajaxUrl, type: 'POST', data: fd,
            processData: false, contentType: false,
            success: function(res){
                $('#pi-upload-loader').hide();
                if (!res.success) {
                    $('#pi-upload-error').text('❌ ' + res.data).show();
                    return;
                }
                STATE.csv = res.data;
                renderCsvPreview(file.name);
                $('#btn-step2-next').prop('disabled', false);
            },
            error: function(){ $('#pi-upload-loader').hide(); $('#pi-upload-error').text('❌ Upload failed.').show(); }
        });
    });

    function renderCsvPreview(filename) {
        var d = STATE.csv;
        $('#pi-csv-row-badge').text(d.count + ' rows');
        $('#pi-csv-col-badge').text(d.headers.length + ' columns');
        $('#pi-csv-filename').text(filename);

        var th = '<thead><tr>' + d.headers.map(function(h){ return '<th>'+h+'</th>'; }).join('') + '</tr></thead>';
        var tb = '<tbody>';
        d.rows.slice(0,5).forEach(function(row){
            tb += '<tr>' + d.headers.map(function(h){ return '<td>'+(row[h]||'')+'</td>'; }).join('') + '</tr>';
        });
        if (d.count > 5) tb += '<tr><td colspan="'+d.headers.length+'" style="text-align:center;color:#aaa;font-style:italic;">… '+( d.count-5)+' more rows</td></tr>';
        tb += '</tbody>';
        $('#pi-csv-table').html(th+tb);
        $('#pi-csv-preview').show();
    }

    $('#btn-step2-next').on('click', function(){
        if (STATE.mode === 'bulk') {
            piGo(3);
            renderBulkPreview();
            $('#btn-step3-next').prop('disabled', false);
        } else if (STATE.mode === 'create') {
            piGo(3);
            renderCreatePanel();
            // enable next once title col is chosen
            $('#btn-step3-next').prop('disabled', true);
        } else {
            piGo(3);
            loadPosts();
        }
    });

    // ── Step 3: Select post / bulk preview ──────────────────────────────────
    function renderBulkPreview() {
        $('#step3-title').text('Bulk Import Preview');
        $('#step3-sub').text('All ' + STATE.csv.count + ' CSV rows will be processed. You\'ll map the post identifier column in the next step.');
        $('#step3-single').hide();
        $('#step3-bulk').show();

        var html = '';
        STATE.csv.rows.slice(0,10).forEach(function(row, i){
            html += '<div style="padding:6px 0;border-bottom:1px solid #f0f0f0;font-family:monospace;font-size:12px;">' +
                '<span style="color:#999;margin-right:8px;">'+(i+1)+'.</span>';
            var vals = STATE.csv.headers.slice(0,4).map(function(h){ return '<strong>'+h+':</strong> '+(row[h]||'—'); });
            html += vals.join(' &nbsp;·&nbsp; ');
            html += '</div>';
        });
        if (STATE.csv.count > 10) html += '<div style="padding:8px 0;color:#aaa;font-size:12px;">…and '+(STATE.csv.count-10)+' more rows</div>';
        $('#bulk-preview-list').html(html);
    }

    function renderCreatePanel() {
        $('#step3-title').text('Configure New Post Creation');
        $('#step3-sub').text('Each CSV row will become a new post of type "' + STATE.cpt + '".');
        $('#step3-single').hide();
        $('#step3-bulk').hide();
        $('#step3-create').show();

        // populate column selects
        var colOpts = STATE.csv.headers.map(function(h){
            return '<option value="'+h+'">'+h+'</option>';
        }).join('');
        $('#pi-create-title-col').html('<option value="">— select column —</option>' + colOpts);
        $('#pi-create-slug-col').html('<option value="">— auto-generate from title —</option>' + colOpts);

        // preview
        var html = '';
        STATE.csv.rows.slice(0,5).forEach(function(row, i){
            html += '<div style="padding:6px 0;border-bottom:1px solid #f0f0f0;font-family:monospace;font-size:12px;">' +
                '<span style="color:#999;margin-right:8px;">'+(i+1)+'.</span>';
            var vals = STATE.csv.headers.slice(0,4).map(function(h){
                return '<strong>'+h+':</strong> '+(row[h]||'—');
            });
            html += vals.join(' &nbsp;·&nbsp; ') + '</div>';
        });
        if (STATE.csv.count > 5) html += '<div style="padding:8px 0;color:#aaa;font-size:12px;">…and '+(STATE.csv.count-5)+' more</div>';
        $('#create-preview-list').html(html);

        // enable next only when title col chosen
        $('#pi-create-title-col').off('change').on('change', function(){
            $('#btn-step3-next').prop('disabled', !$(this).val());
        });
    }

    function loadPosts() {
        $('#step3-single').show();
        $('#step3-bulk').hide();
        $('#step3-title').text('Select Target Post');
        $('#step3-sub').text('Choose which post to import data into.');
        $('#pi-post-loader').show();
        $('#pi-post-list').html('');
        $('#pi-no-posts').hide();
        $('#btn-step3-next').prop('disabled', true);
        STATE.post = null;

        $.post(STATE.ajaxUrl, { action: 'acfpi_get_posts', nonce: STATE.nonce, post_type: STATE.cpt },
        function(res){
            $('#pi-post-loader').hide();
            if (!res.success || !res.data.length) { $('#pi-no-posts').show(); return; }
            _allPosts = res.data;
            renderPostList(_allPosts);
        });
    }

    function renderPostList(posts) {
        if (!posts.length) {
            $('#pi-post-list').html('<div style="padding:16px;text-align:center;color:#999;">No results.</div>');
            return;
        }
        var html = '';
        posts.forEach(function(p){
            html += '<div class="pi-post-item" data-id="'+p.id+'" data-title="'+p.title+'">' +
                '<input type="radio" name="pi_post" value="'+p.id+'">' +
                '<div><div class="pi-post-title">'+p.title+'</div>' +
                '<div class="pi-post-meta">ID: '+p.id+' · '+p.status+'</div></div>' +
            '</div>';
        });
        $('#pi-post-list').html(html);

        $('#pi-post-list').off('click','.pi-post-item').on('click','.pi-post-item', function(){
            $(this).find('input').prop('checked',true);
            $('.pi-post-item').removeClass('selected');
            $(this).addClass('selected');
            STATE.post = { id: $(this).data('id'), title: $(this).data('title') };
            $('#btn-step3-next').prop('disabled', false);
        });
    }

    $('#pi-post-search').on('input', function(){
        var q = $(this).val().toLowerCase();
        renderPostList(_allPosts.filter(function(p){ return p.title.toLowerCase().indexOf(q) !== -1; }));
    });

    $('#btn-step3-next').on('click', function(){
        // store create options before moving on
        if (STATE.mode === 'create') {
            STATE.options = STATE.options || {};
            STATE.options.title_col    = $('#pi-create-title-col').val();
            STATE.options.slug_col     = $('#pi-create-slug-col').val();
            STATE.options.post_status  = $('#pi-create-status').val();
            STATE.options.dupe_mode    = $('#pi-create-dupe').val();
        }
        piGo(4);
        loadFieldsAndBuildMap();
    });

    // ── Step 4: Map fields ────────────────────────────────────────────────────
    function loadFieldsAndBuildMap() {
        $('#pi-map-wrap').hide();
        $('#pi-fields-loader').show();
        $('#pi-extra-opts').html('');

        // update sub title
        var subs = {
            scalar:       'Each CSV column maps to a scalar ACF field (text, image, URL, etc.)',
            repeater:     'Each CSV column maps to a sub-field inside the repeater.',
            taxonomy:     'Choose which CSV column contains the term names and which taxonomy/field to assign them to.',
            relationship: 'Choose which CSV column contains the related post titles/IDs.',
        };
        $('#step4-sub').text(subs[STATE.import_type] || '');

        $.post(STATE.ajaxUrl, { action: 'acfpi_get_fields', nonce: STATE.nonce, post_type: STATE.cpt },
        function(res){
            $('#pi-fields-loader').hide();
            if (!res.success) return;
            STATE.fields = res.data;
            buildMapping();
        });
    }

    function buildMapping() {
        var type    = STATE.import_type;
        var headers = STATE.csv.headers;
        var fields  = STATE.fields;
        var sample  = STATE.csv.rows[0] || {};

        $('#pi-extra-opts').html('');
        $('#pi-map-body').html('');

        // ── Scalar ──
        if (type === 'scalar') {
            var allScalarFields = fields.scalars.concat( fields.taxfields ).concat( fields.relfields );
            var rows = '';
            headers.forEach(function(col){
                var auto = autoMatch(col, allScalarFields);
                rows += mapRow(col, sample[col], buildFieldSelect(col, allScalarFields, auto));
            });
            $('#pi-map-body').html(rows);
            if (STATE.mode === 'bulk') addBulkIdOpts(headers);
        }

        // ── Repeater ──
        else if (type === 'repeater') {
            // extra: choose repeater key
            var repOpts = '<option value="">— choose repeater —</option>';
            fields.repeaters.forEach(function(r){
                repOpts += '<option value="'+r.key+'">'+r.label+' ('+r.key+')</option>';
            });

            $('#pi-extra-opts').html(
                '<div class="pi-field" style="margin-bottom:22px;">' +
                '<label>Repeater Field</label>' +
                '<select id="pi-rep-select">'+repOpts+'</select>' +
                '</div>' +
                '<div id="pi-rep-subfields-hint" class="pi-notice pi-notice-info" style="display:none;"></div>'
            );

            $('#pi-rep-select').on('change', function(){
                var key = $(this).val();
                STATE.options.repeater_key = key;
                var rep = fields.repeaters.find(function(r){ return r.key === key; });
                if (!rep) { $('#pi-map-body').html(''); return; }
                $('#pi-rep-subfields-hint')
                    .html('Sub-fields: ' + rep.sub_fields.map(function(sf){ return '<strong>'+sf.key+'</strong>'; }).join(', '))
                    .show();
                var rows = '';
                headers.forEach(function(col){
                    var auto = autoMatch(col, rep.sub_fields);
                    rows += mapRow(col, sample[col], buildFieldSelect(col, rep.sub_fields, auto));
                });
                $('#pi-map-body').html(rows);
            });

            if (fields.repeaters.length === 1) {
                $('#pi-rep-select').val(fields.repeaters[0].key).trigger('change');
            }

            if (STATE.mode === 'bulk') addBulkIdOpts(headers);
        }

        // ── Taxonomy ──
        else if (type === 'taxonomy') {
            var taxOpts = '<option value="">— Select taxonomy —</option>';
            // native taxonomies
            fields.tax_list.forEach(function(t){ taxOpts += '<option value="native:'+t.slug+'">'+t.label+' (native wp)</option>'; });
            // ACF taxonomy fields
            fields.taxfields.forEach(function(f){ taxOpts += '<option value="acf:'+f.key+'">'+f.label+' (ACF field)</option>'; });

            var colOpts = headers.map(function(h){ return '<option value="'+h+'">'+h+'</option>'; }).join('');

            $('#pi-extra-opts').html(
                '<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:22px;">' +
                '<div class="pi-field"><label>CSV Column with Terms</label>' +
                '<select id="pi-tax-col">'+colOpts+'</select></div>' +
                '<div class="pi-field"><label>Assign To</label>' +
                '<select id="pi-tax-target">'+taxOpts+'</select></div>' +
                '</div>'
            );

            $('#pi-tax-col,#pi-tax-target').on('change', updateTaxOptions);
            $('#pi-map-body').html('<tr><td colspan="3" style="padding:16px;text-align:center;color:#aaa;">Select options above to continue.</td></tr>');

            if (STATE.mode === 'bulk') addBulkIdOpts(headers);
        }

        // ── Relationship ──
        else if (type === 'relationship') {
            var relFieldOpts = '<option value="">— Select ACF field —</option>';
            fields.relfields.forEach(function(f){ relFieldOpts += '<option value="'+f.key+'">'+f.label+' ('+f.key+')</option>'; });

            var colOpts = headers.map(function(h){ return '<option value="'+h+'">'+h+'</option>'; }).join('');

            $('#pi-extra-opts').html(
                '<div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;margin-bottom:22px;">' +
                '<div class="pi-field"><label>CSV Column</label><select id="pi-rel-col">'+colOpts+'</select></div>' +
                '<div class="pi-field"><label>ACF Field</label><select id="pi-rel-field">'+relFieldOpts+'</select></div>' +
                '<div class="pi-field"><label>Match Posts By</label>' +
                '<select id="pi-rel-match">' +
                '<option value="title">Post Title</option>' +
                '<option value="id">Post ID</option>' +
                '</select></div>' +
                '</div>'
            );

            if (STATE.mode === 'bulk') addBulkIdOpts(headers);
            $('#pi-map-body').html('<tr><td colspan="3" style="padding:16px;text-align:center;color:#aaa;">Select options above.</td></tr>');
        }

        $('#pi-map-wrap').show();
    }

    function addBulkIdOpts(headers) {
        var colOpts = '<option value="">— None —</option>' + headers.map(function(h){ return '<option value="'+h+'">'+h+'</option>'; }).join('');

        var isRepeater = STATE.import_type === 'repeater';

        var extra = '<hr class="pi-divider">';

        if (isRepeater) {
            // Grouped repeater mode
            extra +=
                '<div style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#555;margin-bottom:12px;">Bulk Repeater: Group Rows by Post</div>' +
                '<div class="pi-notice pi-notice-info">' +
                '🔁 All CSV rows sharing the same value in the group column will be imported as repeater rows into that post.<br>' +
                'e.g. all rows where <code>city_name = "Agoura Hills"</code> go into the Agoura Hills city post.' +
                '</div>' +
                '<div class="pi-field">' +
                '<label>Group By Column <span style="color:#e53e3e;">*</span></label>' +
                '<select id="pi-bulk-group-col"><option value="">— select column —</option>' +
                headers.map(function(h){ return '<option value="'+h+'">'+h+'</option>'; }).join('') +
                '</select>' +
                '<div class="pi-hint">The column that identifies which post each row belongs to (e.g. <code>city_name</code>). Values must match post titles exactly.</div>' +
                '</div>';
        } else {
            // Standard bulk: match by title or ID
            extra +=
                '<div style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#555;margin-bottom:12px;">Bulk: Post Identification</div>' +
                '<div class="pi-notice pi-notice-warn">⚠️ Choose how each CSV row is matched to a post. At least one identifier is required.</div>' +
                '<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:4px;">' +
                '<div class="pi-field"><label>Post Title Column</label><select id="pi-bulk-title-col">'+colOpts+'</select><div class="pi-hint">Exact title match</div></div>' +
                '<div class="pi-field"><label>Post ID Column</label><select id="pi-bulk-id-col">'+colOpts+'</select><div class="pi-hint">Takes priority over title</div></div>' +
                '</div>';
        }

        $('#pi-extra-opts').append(extra);
    }

    function autoMatch(col, fields) {
        var c = col.toLowerCase().replace(/[\s\-]+/g,'_');
        for (var i=0; i<fields.length; i++) {
            var f = fields[i];
            var k = (f.key||'').toLowerCase();
            var l = (f.label||'').toLowerCase().replace(/[\s\-]+/g,'_');
            if (k === c || l === c) return f.key;
        }
        return '__skip__';
    }

    function buildFieldSelect(col, fields, autoVal) {
        var opts = '<option value="__skip__">— Skip —</option>';
        fields.forEach(function(f){
            var sel = (f.key === autoVal) ? 'selected' : '';
            opts += '<option value="'+f.key+'" '+sel+'>'+f.label+' ('+f.key+')</option>';
        });
        return '<select class="pi-map-select" data-csv-col="'+col+'">'+opts+'</select>';
    }

    function mapRow(col, sample, selectHtml) {
        return '<tr>' +
            '<td><span class="pi-map-col-name">'+col+'</span></td>' +
            '<td>'+selectHtml+'</td>' +
            '<td class="pi-map-sample">'+(sample||'—')+'</td>' +
        '</tr>';
    }

    function updateTaxOptions() { /* just store state */ }

    $('#btn-step4-next').on('click', function(){
        // collect mapping
        STATE.mapping = {};
        $('#pi-map-body select.pi-map-select').each(function(){
            STATE.mapping[$(this).data('csv-col')] = $(this).val();
        });

        // collect extra options
        STATE.options = STATE.options || {};

        if (STATE.import_type === 'taxonomy') {
            STATE.options.csv_col  = $('#pi-tax-col').val();
            var target = $('#pi-tax-target').val() || '';
            if (target.startsWith('acf:')) {
                STATE.options.acf_key  = target.replace('acf:','');
                STATE.options.taxonomy = '';
            } else {
                STATE.options.taxonomy = target.replace('native:','');
                STATE.options.acf_key  = '';
            }
        }
        if (STATE.import_type === 'relationship') {
            STATE.options.csv_col  = $('#pi-rel-col').val();
            STATE.options.acf_key  = $('#pi-rel-field').val();
            STATE.options.match_by = $('#pi-rel-match').val();
        }
        if (STATE.mode === 'bulk') {
            STATE.options.title_col = $('#pi-bulk-title-col').val();
            STATE.options.id_col    = $('#pi-bulk-id-col').val();
            STATE.options.group_col = $('#pi-bulk-group-col').val();
        }

        piGo(5);
        buildSummary();
    });

    // ── Step 5: Summary & Import ─────────────────────────────────────────────
    function buildSummary() {
        var typeLabels = { scalar: 'Scalar', repeater: 'Repeater', taxonomy: 'Taxonomy', relationship: 'Relationship' };
        var mapped = Object.values(STATE.mapping).filter(function(v){ return v !== '__skip__'; }).length;
        var modeLabel = { single: 'Single Post', bulk: 'Bulk Update', create: 'Create New' };
        var target;
        if (STATE.mode === 'create') {
            target = '✨ ' + STATE.csv.count + ' new posts';
        } else if (STATE.mode === 'bulk' && STATE.import_type === 'repeater' && STATE.options.group_col) {
            // count unique group values
            var groups = {};
            STATE.csv.rows.forEach(function(r){ if (r[STATE.options.group_col]) groups[r[STATE.options.group_col]] = 1; });
            target = Object.keys(groups).length + ' posts (grouped)';
        } else if (STATE.mode === 'bulk') {
            target = 'Bulk (' + STATE.csv.count + ' rows)';
        } else {
            target = STATE.post ? STATE.post.title : '—';
        }

        $('#sum-type').text(typeLabels[STATE.import_type] || '—');
        $('#sum-rows').text(STATE.csv.count);
        $('#sum-mapped').text( STATE.import_type === 'taxonomy' || STATE.import_type === 'relationship' ? 'N/A' : mapped);
        $('#sum-target').text(target);

        // show write mode panels
        $('#pi-write-mode-wrap').toggle(STATE.import_type === 'repeater' && STATE.mode !== 'create');
        $('#pi-taxrel-mode-wrap').toggle(
            (STATE.import_type === 'taxonomy' || STATE.import_type === 'relationship') && STATE.mode !== 'create'
        );

        // show create info box if in create mode
        if (STATE.mode === 'create') {
            var opts = STATE.options;
            var dupeLabels = { skip: 'Skip duplicates', update: 'Update duplicates', create: 'Allow duplicates' };
            $('#pi-create-info-box').remove();
            $('#pi-summary').after(
                '<div id="pi-create-info-box" class="pi-notice pi-notice-info" style="margin-bottom:18px;">' +
                '✨ <strong>Create Mode:</strong> ' + STATE.csv.count + ' rows · ' +
                'Title from <code>' + opts.title_col + '</code> · ' +
                'Status: <strong>' + (opts.post_status||'publish') + '</strong> · ' +
                (dupeLabels[opts.dupe_mode] || 'Skip duplicates') +
                '</div>'
            );
        } else {
            $('#pi-create-info-box').remove();
        }

        $('#pi-result').hide();
        $('#pi-bulk-result').hide();
        $('#pi-progress').hide();
        $('#btn-run-import').show().prop('disabled', false).text('⚡ Run Import');
        $('#btn-start-over').hide();
        $('#btn-step5-back').show();
    }

    // Mode card toggles
    $(document).on('change', 'input[name="pi_write_mode"]', function(){
        $('#wm-append-card,#wm-replace-card').removeClass('selected');
        $(this).closest('.pi-mode-card').addClass('selected');
    });
    $(document).on('change', 'input[name="pi_taxrel_mode"]', function(){
        $('#trm-replace-card,#trm-append-card').removeClass('selected');
        $(this).closest('.pi-mode-card').addClass('selected');
    });

    $('#btn-run-import').on('click', function(){
        // build options
        var opts = $.extend({}, STATE.options);

        if (STATE.import_type === 'repeater') {
            opts.repeat_mode = $('input[name="pi_write_mode"]:checked').val() || 'append';
            if (!opts.repeater_key && STATE.mode !== 'create') { alert('Please select a repeater field in step 4.'); piGo(4); return; }
        }
        if (STATE.import_type === 'taxonomy') {
            opts.tax_mode = $('input[name="pi_taxrel_mode"]:checked').val() || 'replace';
            if (!opts.taxonomy && !opts.acf_key) { alert('Please select a taxonomy target in step 4.'); piGo(4); return; }
            if (!opts.csv_col) { alert('Please select a CSV column in step 4.'); piGo(4); return; }
        }
        if (STATE.import_type === 'relationship') {
            opts.rel_mode = $('input[name="pi_taxrel_mode"]:checked').val() || 'replace';
            if (!opts.acf_key) { alert('Please select an ACF relationship field in step 4.'); piGo(4); return; }
        }
        if (STATE.mode === 'bulk') {
            var isGroupedRepeater = STATE.import_type === 'repeater' && opts.group_col;
            var hasStandardId = opts.title_col || opts.id_col;
            if (!isGroupedRepeater && !hasStandardId) {
                alert('Please set a post identifier column (title or ID) in step 4.');
                piGo(4);
                return;
            }
        }
        if (STATE.mode === 'create') {
            if (!opts.title_col) { alert('Please set a title column in step 3.'); piGo(3); return; }
        }
        if (STATE.mode === 'single' && !STATE.post) { alert('No post selected.'); piGo(3); return; }

        // confirm replace
        if ((opts.repeat_mode === 'replace' || opts.rel_mode === 'replace' || opts.tax_mode === 'replace') && STATE.mode === 'single') {
            var rep = STATE.import_type === 'repeater' ? '"' + (opts.repeater_key||'') + '"' : 'this field';
            if (!confirm('⚠️ Replace mode will overwrite existing data in ' + rep + ' for "' + STATE.post.title + '". Proceed?')) return;
        }

        // disable UI
        $('#btn-run-import').prop('disabled', true).text('Importing…');
        $('#btn-step5-back').hide();
        $('#pi-result').hide();
        $('#pi-bulk-result').hide();
        $('#pi-progress').show();
        animateProgress();

        // ── CREATE MODE: different action ──
        if (STATE.mode === 'create') {
            $.post(STATE.ajaxUrl, {
                action:       'acfpi_create_posts',
                nonce:        STATE.nonce,
                post_type:    STATE.cpt,
                import_type:  STATE.import_type,
                title_col:    opts.title_col,
                slug_col:     opts.slug_col || '',
                post_status:  opts.post_status || 'publish',
                skip_dupes:   opts.dupe_mode === 'skip'   ? 1 : 0,
                update_dupes: opts.dupe_mode === 'update' ? 1 : 0,
                mapping:      JSON.stringify(STATE.mapping),
                rows:         JSON.stringify(STATE.csv.rows),
                options:      JSON.stringify(opts),
            }, function(res){
                $('#pi-progress-inner').css('width','100%');
                setTimeout(function(){
                    $('#pi-progress').hide();
                    $('#btn-run-import').hide();
                    $('#btn-start-over').show();

                    if (res.success) {
                        var d = res.data;
                        logImport(d, true);
                        var parts = [];
                        if (d.created)  parts.push('<strong>'+d.created+' created</strong>');
                        if (d.updated)  parts.push('<strong>'+d.updated+' updated</strong>');
                        if (d.skipped)  parts.push(d.skipped+' skipped');
                        if (d.errors)   parts.push('<span style="color:#c62828">'+d.errors+' errors</span>');

                        $('#pi-result').html(
                            '<div class="pi-notice pi-notice-success">✅ Import complete! ' + parts.join(' · ') + '</div>'
                        ).show();

                        // results table
                        var th = '<thead><tr><th>#</th><th>Post Title</th><th>Status</th><th>Details</th></tr></thead>';
                        var tb = '<tbody>';
                        d.results.forEach(function(r){
                            var cls = r.status === 'error' ? 'err' : (r.status === 'skip' ? '' : 'ok');
                            var link = r.id ? ' <a href="'+r.link+'" target="_blank" style="font-size:11px;">[edit]</a>' : '';
                            tb += '<tr><td>'+r.row+'</td><td>'+r.title+link+'</td>' +
                                '<td class="'+cls+'">'+r.status+'</td>' +
                                '<td>'+r.msg+'</td></tr>';
                        });
                        tb += '</tbody>';
                        $('#pi-bulk-table').html(th+tb);
                        $('#pi-bulk-result').show();
                    } else {
                        $('#pi-result').html('<div class="pi-notice pi-notice-error">❌ ' + (res.data||'Unknown error') + '</div>').show();
                        $('#btn-run-import').prop('disabled',false).text('⚡ Run Import').show();
                        $('#btn-step5-back').show();
                        $('#btn-start-over').hide();
                    }
                }, 400);
            });
            return; // don't fall through to regular import
        }

        // ── REGULAR IMPORT (single / bulk) ──
        $.post(STATE.ajaxUrl, {
            action:      'acfpi_import',
            nonce:       STATE.nonce,
            import_type: STATE.import_type,
            mode:        STATE.mode,
            post_id:     STATE.post ? STATE.post.id : 0,
            post_type:   STATE.cpt,
            mapping:     JSON.stringify(STATE.mapping),
            rows:        JSON.stringify(STATE.csv.rows),
            options:     JSON.stringify(opts),
        }, function(res){
            $('#pi-progress-inner').css('width','100%');
            setTimeout(function(){
                $('#pi-progress').hide();
                $('#btn-run-import').hide();
                $('#btn-start-over').show();

                if (res.success) {
                    var d = res.data;
                    logImport(d, true);

                    if (d.mode === 'bulk') {
                        var ok  = d.ok;
                        var err = d.errors;
                        $('#pi-result').html(
                            '<div class="pi-notice pi-notice-success">✅ Bulk import complete: <strong>'+ok+' posts updated</strong>' + (err ? ', <strong>'+err+' errors</strong>' : '') + '.</div>'
                        ).show();

                        var th = '<thead><tr><th>#</th><th>Post</th><th>Status</th><th>Message</th></tr></thead>';
                        var tb = '<tbody>';
                        d.results.forEach(function(r){
                            tb += '<tr><td>'+r.row+'</td><td>'+r.post+'</td>' +
                                '<td class="'+(r.status==='ok'?'ok':'err')+'">'+r.status+'</td>' +
                                '<td>'+r.msg+'</td></tr>';
                        });
                        tb += '</tbody>';
                        $('#pi-bulk-table').html(th+tb);
                        $('#pi-bulk-result').show();
                    } else {
                        var editUrl = '<?php echo admin_url("post.php"); ?>?post='+d.post_id+'&action=edit';
                        $('#pi-result').html(
                            '<div class="pi-notice pi-notice-success">'+
                            '✅ <strong>Import complete!</strong> '+d.rows+' row(s) imported into <strong>'+d.post_title+'</strong>.'+
                            '<br><br><a href="'+editUrl+'" target="_blank" class="pi-btn pi-btn-primary" style="margin-top:6px;">View Post in Editor ↗</a>'+
                            '</div>'
                        ).show();
                        logImport(d, true);
                    }
                } else {
                    $('#pi-result').html('<div class="pi-notice pi-notice-error">❌ Import failed: ' + (res.data||'Unknown error') + '</div>').show();
                    $('#btn-run-import').prop('disabled',false).text('⚡ Run Import').show();
                    $('#btn-step5-back').show();
                    $('#btn-start-over').hide();
                    logImport({ error: res.data }, false);
                }
            }, 400);
        });
    });

    function animateProgress() {
        var pct = 0;
        var iv = setInterval(function(){
            pct += Math.random() * 12;
            if (pct >= 88) { clearInterval(iv); pct = 88; }
            $('#pi-progress-inner').css('width', pct + '%');
        }, 200);
    }

    function logImport(data, success) {
        var now = new Date().toLocaleTimeString();
        var entry = { time: now, type: STATE.import_type, mode: STATE.mode, data: data, success: success, cpt: STATE.cpt };
        _importLog.unshift(entry);

        var html = '';
        _importLog.forEach(function(e){
            var cls  = e.success ? 'pi-notice-success' : 'pi-notice-error';
            var icon = e.success ? '✅' : '❌';
            var msg  = e.success
                ? (e.mode === 'bulk' ? e.data.ok + ' posts updated' : e.data.rows + ' rows into "' + e.data.post_title + '"')
                : (e.data.error || 'Error');
            html += '<div class="pi-notice '+cls+'" style="margin-bottom:10px;">' +
                icon + ' <strong>'+e.time+'</strong> · ' +
                e.type + ' · ' + e.cpt + ' · ' + msg +
            '</div>';
        });
        $('#pi-log-list').html(html);
    }

    })(jQuery);
    </script>
    <?php
}
