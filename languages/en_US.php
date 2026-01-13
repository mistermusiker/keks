<?php
/**
 * English (en_US) translations for Keks Cookie Banner
 * This is the default language file
 */

return [
    // ===========================================
    // Plugin Header
    // ===========================================
    'plugin_name' => 'Keks - GDPR Cookie Banner',
    'plugin_description' => 'Simple, GDPR-compliant cookie banner without dark patterns.',

    // ===========================================
    // Cookie Categories - Names
    // ===========================================
    'category_necessary_name' => 'Necessary',
    'category_statistics_name' => 'Statistics',
    'category_marketing_name' => 'Marketing',

    // ===========================================
    // Cookie Categories - Descriptions
    // ===========================================
    'category_necessary_desc' => 'These cookies are required for basic website functionality and cannot be disabled.',
    'category_statistics_desc' => 'These cookies help us understand how visitors interact with the website.',
    'category_marketing_desc' => 'These cookies are used to show visitors relevant advertisements.',

    // ===========================================
    // Known Services - Labels
    // ===========================================
    'service_measurement_id' => 'Measurement ID',
    'service_container_id' => 'Container ID',
    'service_matomo_url' => 'Matomo URL (with /)',
    'service_site_id' => 'Site ID',
    'service_project_id' => 'Project ID',
    'service_domain' => 'Domain',
    'service_secure_code' => 'Secure Code',
    'service_website_id' => 'Website ID',
    'service_project_token' => 'Project Token',
    'service_pixel_id' => 'Pixel ID',
    'service_conversion_id' => 'Conversion ID',
    'service_partner_id' => 'Partner ID',
    'service_tag_id' => 'Tag ID',
    'service_uet_tag_id' => 'UET Tag ID',
    'service_account_id' => 'Account ID',
    'service_marketer_id' => 'Marketer ID',
    'service_app_id' => 'App ID',
    'service_hub_id' => 'Hub ID',
    'service_chat_key' => 'Chat Key',
    'service_license_id' => 'License ID',
    'service_property_widget_id' => 'Property ID/Widget ID',
    'service_public_key' => 'Public Key',
    'service_site_key' => 'Site Key',
    'service_video_id' => 'Video ID',
    'service_embed_code' => 'Embed Code (Place ID or Query)',
    'service_youtube_tracking' => 'YouTube (with tracking)',

    // ===========================================
    // Shortcode
    // ===========================================
    'shortcode_default_text' => 'Change cookie settings',

    // ===========================================
    // Meta Box
    // ===========================================
    'metabox_title' => 'Cookie Banner',
    'metabox_hide_banner' => 'Hide banner on this page',
    'metabox_excluded_note' => 'This page is configured as an exception in the Keks settings.',

    // ===========================================
    // Default Messages
    // ===========================================
    'default_require_message' => 'Before we continue: Please choose your preferred settings. Your privacy matters to us.',
    'default_banner_text' => 'We use cookies to operate this website reliably, improve content, and show you relevant offers. You decide which cookies to allow – transparent, fair, and adjustable at any time.',

    // ===========================================
    // Admin Menu
    // ===========================================
    'menu_main_title' => 'Keks Cookie Banner',
    'menu_main' => 'Keks',
    'menu_settings' => 'Settings',
    'menu_pages' => 'Pages',
    'menu_pages_title' => 'Page Exceptions',
    'menu_scripts' => 'Scripts',
    'menu_consent_log' => 'Consent Log',

    // ===========================================
    // Admin Settings Page
    // ===========================================
    'settings_page_title' => 'Keks - Settings',
    'settings_tip' => 'Tip:',
    'settings_tip_text' => 'Settings are split across multiple pages:',
    'settings_enable_banner' => 'Enable cookie banner',
    'settings_status_active' => 'Active',
    'settings_status_inactive' => 'Inactive',
    'settings_enable_desc' => 'Shows the cookie banner on your website and manages consent for tracking scripts.',

    // Cookie Categories Section
    'settings_categories_title' => 'Cookie Categories',
    'settings_categories_desc' => 'Configure the cookie categories shown in the banner.',
    'settings_granular_mode' => 'Granular Mode',
    'settings_granular_mode_label' => 'Users can select individual categories',
    'settings_granular_mode_desc' => 'When disabled, only "Accept All" or "Reject All" options are available.',
    'settings_active_categories' => 'Active Categories',
    'settings_display_name' => 'Display name:',
    'settings_description' => 'Description:',
    'settings_always_active' => '(always active)',

    // Banner Content Section
    'settings_banner_content' => 'Banner Content',
    'settings_banner_text' => 'Banner Text',
    'settings_banner_text_desc' => 'The text shown in the cookie banner.',

    // Consent Required Section
    'settings_consent_required' => 'Consent Required',
    'settings_block_mode' => 'Blocking Mode',
    'settings_block_mode_label' => 'Block page without cookie consent',
    'settings_block_mode_desc' => 'When enabled, the page can only be used after accepting necessary cookies.',
    'settings_block_mode_note' => 'The "Reject All" button will be hidden.',
    'settings_block_overlay' => 'Blocking Overlay',
    'settings_block_overlay_label' => 'Show additional overlay with message',
    'settings_block_overlay_desc' => 'Shows a dark overlay with a message above the page content.',
    'settings_block_overlay_warning' => 'Note:',
    'settings_block_overlay_warning_text' => 'This option ("Cookie Wall") is legally controversial under GDPR.',
    'settings_overlay_message' => 'Overlay Message',
    'settings_overlay_message_desc' => 'Text shown in the overlay (only when overlay is enabled).',

    // Google Consent Mode Section
    'settings_gcm_title' => 'Google Consent Mode v2',
    'settings_gcm_desc' => 'Required for Google Ads in the EU since March 2024. Signals consent status to Google.',
    'settings_gcm_enable' => 'Enable Consent Mode',
    'settings_gcm_enable_label' => 'Enable Google Consent Mode v2',
    'settings_gcm_enable_desc' => 'Sends consent signals to Google Analytics and Google Ads.',
    'settings_gcm_how_title' => 'How does it work?',
    'settings_gcm_how_text' => 'Google Consent Mode sends these signals based on user decision:',
    'settings_gcm_keks_category' => 'Keks Category',
    'settings_gcm_google_param' => 'Google Parameter',
    'settings_gcm_auto_note' => 'The script is automatically loaded before all Google scripts. No additional code needed.',

    // Consent Log Settings Section
    'settings_log_title' => 'Consent Log Settings',
    'settings_log_desc' => 'Settings for logging cookie consents.',
    'settings_ip_storage' => 'IP Storage',
    'settings_ip_hash_label' => 'Store IP addresses as hash only',
    'settings_ip_disabled' => 'Disabled (default):',
    'settings_ip_disabled_desc' => 'IP addresses are stored in plain text – better proof for GDPR requests.',
    'settings_ip_enabled' => 'Enabled:',
    'settings_ip_enabled_desc' => 'IP addresses are hashed – more privacy, but proving individual consent is harder.',

    // Preview Section
    'settings_preview_title' => 'Test Banner',
    'settings_preview_desc' => 'In preview mode, the banner is always shown regardless of saved settings. Clicks are not saved.',
    'settings_preview_link' => 'Preview Link',
    'settings_preview_open' => 'Open banner preview',

    // Revoke Section
    'settings_revoke_title' => 'Consent Revocation (GDPR Requirement)',
    'settings_revoke_desc' => 'Users must be able to revoke their cookie consent at any time. Add a revocation link to your website.',
    'settings_shortcode_title' => 'Using Shortcode',
    'settings_shortcode_desc' => 'Add this shortcode to a page, post, or widget:',
    'settings_shortcode_shows' => 'Shows:',
    'settings_shortcode_custom' => '(custom text)',
    'settings_html_title' => 'HTML Link (for theme files)',
    'settings_html_desc' => 'For direct integration in footer, header, or template files:',
    'settings_placements_title' => 'Recommended Placements',
    'settings_placement_footer' => 'Footer',
    'settings_placement_footer_desc' => 'Next to Privacy Policy & Imprint links',
    'settings_placement_privacy' => 'Privacy page',
    'settings_placement_privacy_desc' => 'In the cookies section',
    'settings_placement_cookie' => 'Cookie Policy',
    'settings_placement_cookie_desc' => 'At the end of the page',

    // Save Button
    'settings_save' => 'Save',

    // ===========================================
    // Admin Scripts Page
    // ===========================================
    'scripts_page_title' => 'Keks - Script Management',
    'scripts_page_desc' => 'Add tracking scripts here that will only load after consent to the corresponding cookie category. Choose a known service from the list or add a custom script.',
    'scripts_add_service' => 'Add service:',
    'scripts_select_service' => '-- Select service --',
    'scripts_category_statistics' => 'Statistics',
    'scripts_category_marketing' => 'Marketing',
    'scripts_category_necessary' => 'Necessary',
    'scripts_category_other' => 'Other',
    'scripts_manual_entry' => 'Enter manually...',
    'scripts_add_button' => 'Add',
    'scripts_remove_button' => 'Remove',
    'scripts_position_head' => 'Head',
    'scripts_position_footer' => 'Footer',
    'scripts_manual_script' => 'Manual Script',
    'scripts_custom' => 'Custom',
    'scripts_name_placeholder' => 'Name (e.g., My Tracking)',
    'scripts_type_url' => 'External URL',
    'scripts_type_inline' => 'Inline Code',
    'scripts_url_placeholder' => 'Script URL',
    'scripts_code_placeholder' => 'Insert JavaScript code here...',
    'scripts_position_in_head' => 'In Head',
    'scripts_position_in_footer' => 'In Footer',
    'scripts_none_added' => 'No scripts added yet. Select a service above.',
    'scripts_select_alert' => 'Please select a service.',

    // ===========================================
    // Admin Pages Page
    // ===========================================
    'pages_page_title' => 'Keks - Page Exceptions',
    'pages_page_desc' => 'The cookie banner will not be shown on these pages.',
    'pages_privacy_page' => 'Privacy Page',
    'pages_please_select' => '-- Please select --',
    'pages_privacy_desc' => 'The banner will never be shown on this page. The privacy policy link points to this page.',
    'pages_imprint_page' => 'Imprint Page',
    'pages_imprint_desc' => 'The banner will never be shown on this page.',
    'pages_imprint_link' => 'Imprint link in banner',
    'pages_imprint_link_label' => 'Show link to imprint in banner',
    'pages_imprint_link_desc' => 'Shows a link to the imprint alongside the privacy policy.',
    'pages_more_exceptions' => 'More Exceptions',
    'pages_more_exceptions_desc' => 'Select more pages where the banner should not appear.',
    'pages_tip' => 'You can also hide the banner on individual pages/posts via the "Cookie Banner" meta box in the editor.',

    // ===========================================
    // Admin Consent Log Page
    // ===========================================
    'log_page_title' => 'Keks - Consent Log',
    'log_table_not_found' => 'Database table not found.',
    'log_table_recreate' => 'Please deactivate and reactivate the plugin to create the table.',
    'log_filter_all_actions' => 'All Actions',
    'log_filter_accept_all' => 'All accepted',
    'log_filter_reject_all' => 'All rejected',
    'log_filter_custom' => 'Custom selection',
    'log_filter_revoke' => 'Revoked',
    'log_filter_button' => 'Filter',
    'log_reset_button' => 'Reset',
    'log_export_csv' => 'Export CSV',
    'log_column_id' => 'ID',
    'log_column_date' => 'Date',
    'log_column_action' => 'Action',
    'log_column_categories' => 'Categories',
    'log_column_ip_hash' => 'IP Hash',
    'log_column_ip_address' => 'IP Address',
    'log_column_page' => 'Page',
    'log_column_browser' => 'Browser',
    'log_no_entries' => 'No entries found.',
    'log_browser_other' => 'Other',
    'log_action_accept' => 'All accepted',
    'log_action_reject' => 'All rejected',
    'log_action_custom' => 'Selection',
    'log_action_revoke' => 'Revoked',
    'log_entries' => 'entries',
    'log_of' => 'of',
    'log_manage_data' => 'Manage Data',
    'log_delete_older' => 'Delete entries older than',
    'log_days' => 'days',
    'log_delete_old_button' => 'Delete old entries',
    'log_delete_confirm' => 'Really delete old entries? This cannot be undone.',
    'log_gdpr_note' => 'Note GDPR proof requirements. Consents should be kept at least as long as they are valid.',
    'log_entries_deleted' => '%d entries deleted.',

    // CSV Export
    'csv_consent_id' => 'Consent ID',
    'csv_date' => 'Date',
    'csv_action' => 'Action',
    'csv_categories' => 'Categories',
    'csv_url' => 'URL',
    'csv_version' => 'Version',

    // ===========================================
    // Iframe Placeholder
    // ===========================================
    'iframe_placeholder' => 'This content will load after consent to %s cookies.',

    // ===========================================
    // Banner Template (banner.php)
    // ===========================================
    'banner_aria_label' => 'Cookie Settings',
    'banner_always_active' => 'always active',
    'banner_privacy_link' => 'Privacy Policy',
    'banner_imprint_link' => 'Imprint',
    'banner_save_selection' => 'Save Selection',
    'banner_reject_all' => 'Reject All',
    'banner_accept_all' => 'Accept All',

    // ===========================================
    // JavaScript Strings (banner.js)
    // ===========================================
    'js_preview_mode' => 'Keks: Preview mode active',
    'js_accepted_preview' => 'Keks: All accepted (Preview - not saved)',
    'js_rejected_preview' => 'Keks: All rejected (Preview - not saved)',
    'js_selection_saved_preview' => 'Keks: Selection saved (Preview)',
    'js_gcm_updated' => 'Keks: Google Consent Mode updated',
    'js_localstorage_unavailable' => 'Keks: localStorage not available',
    'js_logging_failed' => 'Keks: Consent logging failed',
    'js_consent_saved' => 'Keks: Consent saved',
    'js_revoke_failed' => 'Keks: Revoke logging failed',
    'js_script_activated' => 'Keks: Script activated',
    'js_iframe_activated' => 'Keks: Iframe activated',
    'js_overlay_message' => 'To use this website, you must consent to the use of cookies.',
    'js_overlay_hint' => 'Please select your cookie settings in the banner below.',
];
