<?php 
/**
 * Plugin Name: Security Check Plugin
 * Description: A plugin to scan WordPress sites for common security vulnerabilities.
 * Version: 1.0.0
 * Author: Your Name
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

defined( 'ABSPATH' ) || exit; // Prevent direct access.

// Include files and define constants.
define( 'SECURITY_CHECK_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

// Include necessary files.
require_once SECURITY_CHECK_PLUGIN_DIR . 'includes/security-scans.php';
require_once SECURITY_CHECK_PLUGIN_DIR . 'includes/admin-dashboard.php';

// Include additional files.
require_once SECURITY_CHECK_PLUGIN_DIR . 'includes/two-factor-auth.php';

// Initialize the plugin.
add_action( 'plugins_loaded', 'security_check_init' );

function security_check_init() {
    // Run initialization tasks, if any.
}