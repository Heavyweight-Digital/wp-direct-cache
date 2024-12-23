<?php
/*
 * Plugin Name:       WP Direct Cache
 * Plugin URI:        https://heavyweightdigital.co.za
 * Description:       Serve cached HTML files directly if they exist, bypassing PHP and WordPress processing. Includes a tool to check cache status for pages.
 * Version:           1.0
 * Requires at least: 4.8
 * Requires PHP:      7.4
 * Author:            Byron Jacobs
 * Author URI:        https://heavyweightdigital.co.za
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wp-direct-cache
 * Domain Path:       /languages
 *  */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class WPDirectCacheServe {

    const CACHE_RULE_START = '# BEGIN Direct Cache Serve';
    const CACHE_RULE_END = '# END Direct Cache Serve';

    public function __construct() {
        add_action('admin_init', [$this, 'maybe_update_htaccess']);
        add_action('admin_menu', [$this, 'add_admin_page']);
    }

    /**
     * Check and update .htaccess if needed
     */
    public function maybe_update_htaccess() {
        if (!current_user_can('manage_options')) {
            return;
        }

        $this->update_htaccess();
    }

    /**
     * Update .htaccess file to add cache serving rules.
     */
    private function update_htaccess() {
        $htaccess_file = get_home_path() . '.htaccess';

        if (!is_writable($htaccess_file)) {
            add_action('admin_notices', function () {
                echo '<div class="error"><p>The .htaccess file is not writable. Please update its permissions to enable direct cache serving.</p></div>';
            });
            return;
        }

        $rules = $this->generate_cache_rules();
        $current_rules = @file_get_contents($htaccess_file);

        if (strpos($current_rules, self::CACHE_RULE_START) !== false) {
            $current_rules = preg_replace('/' . preg_quote(self::CACHE_RULE_START, '/') . '.*?' . preg_quote(self::CACHE_RULE_END, '/') . '/s', $rules, $current_rules);
        } else {
            $current_rules .= "\n\n" . $rules;
        }

        file_put_contents($htaccess_file, $current_rules);
    }

    /**
     * Generate the cache serving rules for .htaccess
     *
     * @return string
     */
    private function generate_cache_rules() {
        return self::CACHE_RULE_START . "\n" . 
               '<IfModule mod_rewrite.c>' . "\n" .
               'RewriteEngine On' . "\n" .
               'RewriteBase /' . "\n\n" .
               '# Serve cached HTML files directly if they exist' . "\n" .
               'RewriteCond %{HTTP_COOKIE} !wordpress_logged_in' . "\n" .
               'RewriteCond %{REQUEST_URI} !^/wp-admin/' . "\n" .
               'RewriteCond %{REQUEST_URI} !^/wp-login.php' . "\n" .
               'RewriteCond %{DOCUMENT_ROOT}/wp-content/cache/wp-rocket/%{HTTP_HOST}%{REQUEST_URI}/index-https.html -f [OR]' . "\n" .
               'RewriteCond %{DOCUMENT_ROOT}/wp-content/cache/wp-rocket/%{HTTP_HOST}%{REQUEST_URI}/index.html -f' . "\n" .
               'RewriteRule ^ /wp-content/cache/wp-rocket/%{HTTP_HOST}%{REQUEST_URI}/index.html [L]' . "\n" .
               '</IfModule>' . "\n" .
               self::CACHE_RULE_END . "\n";
    }

    /**
     * Add a menu page for cache status
     */
    public function add_admin_page() {
        add_menu_page(
            'Cache Status',
            'Cache Status',
            'manage_options',
            'cache-status',
            [$this, 'render_admin_page'],
            'dashicons-chart-bar',
            100
        );
    }

    /**
     * Render the admin page
     */
    public function render_admin_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        $cache_path = ABSPATH . 'wp-content/cache/wp-rocket/';
        $home_url = get_home_url();

        echo '<div class="wrap">';
        echo '<h1>Cache Status</h1>';
        echo '<p>Check if pages are cached and served directly.</p>';

        echo '<table class="widefat fixed" style="width:100%;margin-top:20px;">';
        echo '<thead>';
        echo '<tr><th>URL</th><th>Status</th></tr>';
        echo '</thead>';
        echo '<tbody>';

        // List pages and their cache status
        $pages = get_pages();
        foreach ($pages as $page) {
            $url = get_permalink($page->ID);
            $relative_path = str_replace($home_url, '', $url);
            $cache_file_http = $cache_path . $_SERVER['HTTP_HOST'] . $relative_path . 'index.html';
            $cache_file_https = $cache_path . $_SERVER['HTTP_HOST'] . $relative_path . 'index-https.html';

            $status = file_exists($cache_file_https) ? 'Cached (HTTPS)' : (file_exists($cache_file_http) ? 'Cached (HTTP)' : 'Not Cached');

            echo '<tr>';
            echo '<td><a href="' . esc_url($url) . '" target="_blank">' . esc_html($url) . '</a></td>';
            echo '<td>' . esc_html($status) . '</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
        echo '</div>';
    }
}

new WPDirectCacheServe();