<?php
defined( 'ABSPATH' ) || exit;

/**
 * Scan for outdated plugins, themes, and WordPress core.
 */
function security_check_run_scans() {
    $results = [];

    // Check WordPress core version.
    if ( ! get_site_option( 'update_core' ) ) {
        require_once ABSPATH . 'wp-admin/includes/update.php';
        wp_version_check();
    }
    
    $theme_updates = get_theme_updates();
        if (is_array($theme_updates) && !empty($theme_updates)) {
            $results['core'] = 'Up to date';
        } else {
            $results['core'] ='Outdated' ;
            error_log('No theme updates found or failed to fetch theme updates.');
        }

    // Check plugins.
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
    $plugins = get_plugins();
    $outdated_plugins = [];
    foreach ( $plugins as $plugin_file => $plugin_data ) {
        if ( is_plugin_active( $plugin_file ) && ! is_plugin_up_to_date( $plugin_file ) ) {
            $outdated_plugins[] = $plugin_data['Name'];
        }
    }
    $results['plugins'] = $outdated_plugins;

    // Check themes.
    require_once ABSPATH . 'wp-admin/includes/theme.php';
    $theme = wp_get_theme();
    if ($theme->exists()) {
        $updates = get_theme_updates();
        if (isset($updates[$theme->get_stylesheet()])) {
            $results['theme'] = 'Up to date';
        } else {
            $results['theme'] = 'Outdated';
            error_log('No updates available for the current theme.');
        }
    } else {
        error_log('The theme does not exist.');
    }

    return $results;
}

/**
 * Check if a plugin is up to date.
 */
function is_plugin_up_to_date( $plugin_file ) {
    $update_plugins = get_site_transient( 'update_plugins' );
    return empty( $update_plugins->response[ $plugin_file ] );
}
