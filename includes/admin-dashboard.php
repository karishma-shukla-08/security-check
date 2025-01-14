<?php
defined( 'ABSPATH' ) || exit;

/**
 * Add a menu page for the Security Check plugin.
 */
function security_check_add_admin_menu() {
    add_menu_page(
        __( 'Security Check', 'security-check' ),
        __( 'Security Check', 'security-check' ),
        'manage_options',
        'security-check',
        'security_check_dashboard_page',
        'dashicons-shield-alt',
        75
    );
}
add_action( 'admin_menu', 'security_check_add_admin_menu' );

/**
 * Display the plugin's admin page.
 */
function security_check_dashboard_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $results = security_check_run_scans();
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Security Check Results', 'security-check' ); ?></h1>
        <table class="widefat">
            <thead>
                <tr>
                    <th><?php esc_html_e( 'Item', 'security-check' ); ?></th>
                    <th><?php esc_html_e( 'Status', 'security-check' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php esc_html_e( 'WordPress Core', 'security-check' ); ?></td>
                    <td><?php echo esc_html( $results['core'] ); ?></td>
                </tr>
                <tr>
                    <td><?php esc_html_e( 'Outdated Plugins', 'security-check' ); ?></td>
                    <td><?php echo empty( $results['plugins'] ) ? esc_html__( 'None', 'security-check' ) : implode( ', ', array_map( 'esc_html', $results['plugins'] ) ); ?></td>
                </tr>
                <tr>
                    <td><?php esc_html_e( 'Theme', 'security-check' ); ?></td>
                    <td><?php echo esc_html( $results['theme'] ); ?></td>
                </tr>
            </tbody>
        </table>
    </div>
    <?php
}
