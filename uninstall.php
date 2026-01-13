<?php
/**
 * Keks Plugin Uninstall
 *
 * Removes all plugin data when uninstalled via WordPress admin.
 * This file is called automatically by WordPress when the plugin is deleted.
 *
 * @package Keks
 */

// Security check: Exit if not called by WordPress uninstall process
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

global $wpdb;

/**
 * Delete all plugin options
 */
$options_to_delete = [
    // Core settings
    'keks_plugin_enabled',
    'keks_banner_text',
    'keks_require_consent',
    'keks_show_block_overlay',
    'keks_require_consent_message',
    'keks_granular_mode',
    'keks_enabled_categories',

    // Page settings
    'keks_privacy_page_id',
    'keks_imprint_page_id',
    'keks_show_imprint_link',
    'keks_excluded_pages',

    // Category names and descriptions
    'keks_category_necessary_name',
    'keks_category_necessary_desc',
    'keks_category_statistics_name',
    'keks_category_statistics_desc',
    'keks_category_marketing_name',
    'keks_category_marketing_desc',

    // Scripts management
    'keks_managed_scripts',

    // Google Consent Mode
    'keks_google_consent_mode',

    // Consent log settings
    'keks_ip_hash_only',
    'keks_consent_version',

    // Database version
    'keks_db_version',
];

foreach ($options_to_delete as $option) {
    delete_option($option);
    // Also delete site options for multisite
    delete_site_option($option);
}

/**
 * Drop custom database table
 */
$table_name = $wpdb->prefix . 'keks_consent_log';
$wpdb->query("DROP TABLE IF EXISTS {$table_name}");

/**
 * Delete all post meta created by the plugin
 */
$wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key = '_keks_hide_banner'");

/**
 * Clear any transients (if used in future versions)
 */
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_keks_%'");
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_keks_%'");

/**
 * For multisite: Clean up each site
 */
if (is_multisite()) {
    $sites = get_sites(['fields' => 'ids']);

    foreach ($sites as $site_id) {
        switch_to_blog($site_id);

        // Delete options for this site
        foreach ($options_to_delete as $option) {
            delete_option($option);
        }

        // Drop table for this site
        $table_name = $wpdb->prefix . 'keks_consent_log';
        $wpdb->query("DROP TABLE IF EXISTS {$table_name}");

        // Delete post meta
        $wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key = '_keks_hide_banner'");

        restore_current_blog();
    }
}
