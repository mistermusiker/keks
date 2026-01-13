<?php
/**
 * German (de_DE) translations for Keks Cookie Banner
 * Diese Datei enthält alle deutschen Übersetzungen
 */

return [
    // ===========================================
    // Plugin Header
    // ===========================================
    'plugin_name' => 'Keks - DSGVO Cookie Banner',
    'plugin_description' => 'Einfaches, DSGVO-konformes Cookie-Banner ohne Dark Patterns.',

    // ===========================================
    // Cookie Categories - Names
    // ===========================================
    'category_necessary_name' => 'Notwendig',
    'category_statistics_name' => 'Statistik',
    'category_marketing_name' => 'Marketing',

    // ===========================================
    // Cookie Categories - Descriptions
    // ===========================================
    'category_necessary_desc' => 'Diese Cookies sind für die Grundfunktionen der Website erforderlich und können nicht deaktiviert werden.',
    'category_statistics_desc' => 'Diese Cookies helfen uns zu verstehen, wie Besucher mit der Website interagieren.',
    'category_marketing_desc' => 'Diese Cookies werden verwendet, um Besuchern relevante Werbung anzuzeigen.',

    // ===========================================
    // Known Services - Labels
    // ===========================================
    'service_measurement_id' => 'Mess-ID',
    'service_container_id' => 'Container-ID',
    'service_matomo_url' => 'Matomo-URL (mit /)',
    'service_site_id' => 'Site-ID',
    'service_project_id' => 'Project-ID',
    'service_domain' => 'Domain',
    'service_secure_code' => 'Secure Code',
    'service_website_id' => 'Website-ID',
    'service_project_token' => 'Project Token',
    'service_pixel_id' => 'Pixel-ID',
    'service_conversion_id' => 'Conversion-ID',
    'service_partner_id' => 'Partner-ID',
    'service_tag_id' => 'Tag-ID',
    'service_uet_tag_id' => 'UET Tag-ID',
    'service_account_id' => 'Account-ID',
    'service_marketer_id' => 'Marketer-ID',
    'service_app_id' => 'App-ID',
    'service_hub_id' => 'Hub-ID',
    'service_chat_key' => 'Chat-Key',
    'service_license_id' => 'License-ID',
    'service_property_widget_id' => 'Property-ID/Widget-ID',
    'service_public_key' => 'Public Key',
    'service_site_key' => 'Site Key',
    'service_video_id' => 'Video-ID',
    'service_embed_code' => 'Embed-Code (Place-ID oder Query)',
    'service_youtube_tracking' => 'YouTube (mit Tracking)',

    // ===========================================
    // Shortcode
    // ===========================================
    'shortcode_default_text' => 'Cookie-Einstellungen ändern',

    // ===========================================
    // Meta Box
    // ===========================================
    'metabox_title' => 'Cookie-Banner',
    'metabox_hide_banner' => 'Banner auf dieser Seite ausblenden',
    'metabox_excluded_note' => 'Diese Seite ist in den Keks-Einstellungen als Ausnahme konfiguriert.',

    // ===========================================
    // Default Messages
    // ===========================================
    'default_require_message' => 'Kurz bevor es losgeht: Wählen Sie Ihre bevorzugten Einstellungen. Ihre Privatsphäre liegt uns am Herzen.',
    'default_banner_text' => 'Wir verwenden Cookies, um diese Website zuverlässig zu betreiben, Inhalte zu verbessern und Ihnen passende Unterstützungsangebote zeigen zu können. Sie entscheiden selbst, welche Cookies Sie zulassen – transparent, fair und jederzeit anpassbar.',

    // ===========================================
    // Admin Menu
    // ===========================================
    'menu_main_title' => 'Keks Cookie Banner',
    'menu_main' => 'Keks',
    'menu_settings' => 'Einstellungen',
    'menu_pages' => 'Seiten',
    'menu_pages_title' => 'Seiten-Ausnahmen',
    'menu_scripts' => 'Scripts',
    'menu_consent_log' => 'Consent-Log',

    // ===========================================
    // Admin Settings Page
    // ===========================================
    'settings_page_title' => 'Keks - Einstellungen',
    'settings_tip' => 'Tipp:',
    'settings_tip_text' => 'Die Einstellungen sind auf mehrere Seiten aufgeteilt:',
    'settings_enable_banner' => 'Cookie-Banner aktivieren',
    'settings_status_active' => 'Aktiv',
    'settings_status_inactive' => 'Inaktiv',
    'settings_enable_desc' => 'Zeigt das Cookie-Banner auf Ihrer Website und verwaltet die Einwilligung für Tracking-Scripts.',

    // Cookie Categories Section
    'settings_categories_title' => 'Cookie-Kategorien',
    'settings_categories_desc' => 'Konfiguriere die Cookie-Kategorien, die im Banner angezeigt werden.',
    'settings_granular_mode' => 'Granularer Modus',
    'settings_granular_mode_label' => 'Nutzer können einzelne Kategorien wählen',
    'settings_granular_mode_desc' => 'Wenn deaktiviert, gibt es nur "Alle akzeptieren" oder "Alle ablehnen".',
    'settings_active_categories' => 'Aktive Kategorien',
    'settings_display_name' => 'Anzeigename:',
    'settings_description' => 'Beschreibung:',
    'settings_always_active' => '(immer aktiv)',

    // Banner Content Section
    'settings_banner_content' => 'Banner-Inhalt',
    'settings_banner_text' => 'Banner-Text',
    'settings_banner_text_desc' => 'Der Text, der im Cookie-Banner angezeigt wird.',

    // Consent Required Section
    'settings_consent_required' => 'Zustimmung erforderlich',
    'settings_block_mode' => 'Blockier-Modus',
    'settings_block_mode_label' => 'Seite ohne Cookie-Zustimmung blockieren',
    'settings_block_mode_desc' => 'Wenn aktiviert, kann die Seite erst genutzt werden, nachdem die notwendigen Cookies akzeptiert wurden.',
    'settings_block_mode_note' => 'Der "Alle ablehnen"-Button wird ausgeblendet.',
    'settings_block_overlay' => 'Blockier-Overlay',
    'settings_block_overlay_label' => 'Zusätzliches Overlay mit Hinweistext anzeigen',
    'settings_block_overlay_desc' => 'Zeigt ein dunkles Overlay mit einer Nachricht über dem Seiteninhalt an.',
    'settings_block_overlay_warning' => 'Hinweis:',
    'settings_block_overlay_warning_text' => 'Diese Option ("Cookie Wall") ist DSGVO-rechtlich umstritten.',
    'settings_overlay_message' => 'Overlay-Nachricht',
    'settings_overlay_message_desc' => 'Text, der im Overlay angezeigt wird (nur wenn Overlay aktiviert).',

    // Google Consent Mode Section
    'settings_gcm_title' => 'Google Consent Mode v2',
    'settings_gcm_desc' => 'Seit März 2024 für Google Ads in der EU Pflicht. Signalisiert Google den Consent-Status.',
    'settings_gcm_enable' => 'Consent Mode aktivieren',
    'settings_gcm_enable_label' => 'Google Consent Mode v2 aktivieren',
    'settings_gcm_enable_desc' => 'Sendet Consent-Signale an Google Analytics und Google Ads.',
    'settings_gcm_how_title' => 'Wie funktioniert es?',
    'settings_gcm_how_text' => 'Google Consent Mode sendet diese Signale basierend auf der Nutzer-Entscheidung:',
    'settings_gcm_keks_category' => 'Keks-Kategorie',
    'settings_gcm_google_param' => 'Google Parameter',
    'settings_gcm_auto_note' => 'Das Script wird automatisch vor allen Google-Scripts geladen. Kein zusätzlicher Code nötig.',

    // Consent Log Settings Section
    'settings_log_title' => 'Consent-Log Einstellungen',
    'settings_log_desc' => 'Einstellungen für die Protokollierung von Cookie-Einwilligungen.',
    'settings_ip_storage' => 'IP-Speicherung',
    'settings_ip_hash_label' => 'IP-Adressen nur als Hash speichern',
    'settings_ip_disabled' => 'Deaktiviert (Standard):',
    'settings_ip_disabled_desc' => 'IP-Adressen werden im Klartext gespeichert – besserer Nachweis für DSGVO-Anfragen.',
    'settings_ip_enabled' => 'Aktiviert:',
    'settings_ip_enabled_desc' => 'IP-Adressen werden gehasht – mehr Datenschutz, aber Nachweis einzelner Personen erschwert.',

    // Preview Section
    'settings_preview_title' => 'Banner testen',
    'settings_preview_desc' => 'Im Preview-Modus wird das Banner immer angezeigt, unabhängig von bereits gespeicherten Einstellungen. Klicks werden nicht gespeichert.',
    'settings_preview_link' => 'Preview-Link',
    'settings_preview_open' => 'Banner-Preview öffnen',

    // Revoke Section
    'settings_revoke_title' => 'Consent-Widerruf (DSGVO-Pflicht)',
    'settings_revoke_desc' => 'Nutzer müssen ihre Cookie-Einwilligung jederzeit widerrufen können. Füge einen Widerruf-Link auf deiner Website ein.',
    'settings_shortcode_title' => 'Shortcode verwenden',
    'settings_shortcode_desc' => 'Füge diesen Shortcode in eine Seite, einen Beitrag oder ein Widget ein:',
    'settings_shortcode_shows' => 'Zeigt:',
    'settings_shortcode_custom' => '(eigener Text)',
    'settings_html_title' => 'HTML-Link (für Theme-Dateien)',
    'settings_html_desc' => 'Für direkten Einbau in Footer, Header oder Template-Dateien:',
    'settings_placements_title' => 'Empfohlene Platzierungen',
    'settings_placement_footer' => 'Footer',
    'settings_placement_footer_desc' => 'Neben Datenschutz & Impressum Links',
    'settings_placement_privacy' => 'Datenschutzseite',
    'settings_placement_privacy_desc' => 'Im Abschnitt über Cookies',
    'settings_placement_cookie' => 'Cookie-Richtlinie',
    'settings_placement_cookie_desc' => 'Am Ende der Seite',

    // Save Button
    'settings_save' => 'Speichern',

    // ===========================================
    // Admin Scripts Page
    // ===========================================
    'scripts_page_title' => 'Keks - Script-Verwaltung',
    'scripts_page_desc' => 'Füge hier Tracking-Scripts hinzu, die erst nach Zustimmung zur entsprechenden Cookie-Kategorie geladen werden. Wähle einen bekannten Dienst aus der Liste oder füge ein eigenes Script hinzu.',
    'scripts_add_service' => 'Dienst hinzufügen:',
    'scripts_select_service' => '-- Dienst auswählen --',
    'scripts_category_statistics' => 'Statistik',
    'scripts_category_marketing' => 'Marketing',
    'scripts_category_necessary' => 'Notwendig',
    'scripts_category_other' => 'Sonstige',
    'scripts_manual_entry' => 'Manuell eingeben...',
    'scripts_add_button' => 'Hinzufügen',
    'scripts_remove_button' => 'Entfernen',
    'scripts_position_head' => 'Head',
    'scripts_position_footer' => 'Footer',
    'scripts_manual_script' => 'Manuelles Script',
    'scripts_custom' => 'Benutzerdefiniert',
    'scripts_name_placeholder' => 'Name (z.B. Mein Tracking)',
    'scripts_type_url' => 'Externe URL',
    'scripts_type_inline' => 'Inline-Code',
    'scripts_url_placeholder' => 'Script-URL',
    'scripts_code_placeholder' => 'JavaScript-Code hier einfügen...',
    'scripts_position_in_head' => 'Im Head',
    'scripts_position_in_footer' => 'Im Footer',
    'scripts_none_added' => 'Noch keine Scripts hinzugefügt. Wähle oben einen Dienst aus.',
    'scripts_select_alert' => 'Bitte wähle einen Dienst aus.',

    // ===========================================
    // Admin Pages Page
    // ===========================================
    'pages_page_title' => 'Keks - Seiten-Ausnahmen',
    'pages_page_desc' => 'Auf diesen Seiten wird das Cookie-Banner nicht angezeigt.',
    'pages_privacy_page' => 'Datenschutz-Seite',
    'pages_please_select' => '-- Bitte wählen --',
    'pages_privacy_desc' => 'Auf dieser Seite wird das Banner nie angezeigt. Der Link zur Datenschutzerklärung zeigt auf diese Seite.',
    'pages_imprint_page' => 'Impressum-Seite',
    'pages_imprint_desc' => 'Auf dieser Seite wird das Banner nie angezeigt.',
    'pages_imprint_link' => 'Impressum-Link im Banner',
    'pages_imprint_link_label' => 'Link zum Impressum im Banner anzeigen',
    'pages_imprint_link_desc' => 'Zeigt neben der Datenschutzerklärung auch einen Link zum Impressum an.',
    'pages_more_exceptions' => 'Weitere Ausnahmen',
    'pages_more_exceptions_desc' => 'Wähle weitere Seiten, auf denen kein Banner erscheinen soll.',
    'pages_tip' => 'Du kannst das Banner auch auf einzelnen Seiten/Beiträgen über die Meta-Box "Cookie-Banner" im Editor ausblenden.',

    // ===========================================
    // Admin Consent Log Page
    // ===========================================
    'log_page_title' => 'Keks - Consent-Log',
    'log_table_not_found' => 'Datenbank-Tabelle nicht gefunden.',
    'log_table_recreate' => 'Bitte deaktiviere und reaktiviere das Plugin, um die Tabelle zu erstellen.',
    'log_filter_all_actions' => 'Alle Aktionen',
    'log_filter_accept_all' => 'Alle akzeptiert',
    'log_filter_reject_all' => 'Alle abgelehnt',
    'log_filter_custom' => 'Individuelle Auswahl',
    'log_filter_revoke' => 'Widerrufen',
    'log_filter_button' => 'Filtern',
    'log_reset_button' => 'Zurücksetzen',
    'log_export_csv' => 'CSV exportieren',
    'log_column_id' => 'ID',
    'log_column_date' => 'Datum',
    'log_column_action' => 'Aktion',
    'log_column_categories' => 'Kategorien',
    'log_column_ip_hash' => 'IP-Hash',
    'log_column_ip_address' => 'IP-Adresse',
    'log_column_page' => 'Seite',
    'log_column_browser' => 'Browser',
    'log_no_entries' => 'Keine Einträge gefunden.',
    'log_browser_other' => 'Andere',
    'log_action_accept' => 'Alle akzeptiert',
    'log_action_reject' => 'Alle abgelehnt',
    'log_action_custom' => 'Auswahl',
    'log_action_revoke' => 'Widerrufen',
    'log_entries' => 'Einträge',
    'log_of' => 'von',
    'log_manage_data' => 'Daten verwalten',
    'log_delete_older' => 'Einträge löschen, die älter sind als',
    'log_days' => 'Tage',
    'log_delete_old_button' => 'Alte Einträge löschen',
    'log_delete_confirm' => 'Wirklich alte Einträge löschen? Dies kann nicht rückgängig gemacht werden.',
    'log_gdpr_note' => 'DSGVO-Nachweispflicht beachten. Einwilligungen sollten mindestens so lange aufbewahrt werden, wie sie gültig sind.',
    'log_entries_deleted' => '%d Einträge gelöscht.',

    // CSV Export
    'csv_consent_id' => 'Consent-ID',
    'csv_date' => 'Datum',
    'csv_action' => 'Aktion',
    'csv_categories' => 'Kategorien',
    'csv_url' => 'URL',
    'csv_version' => 'Version',

    // ===========================================
    // Iframe Placeholder
    // ===========================================
    'iframe_placeholder' => 'Dieser Inhalt wird erst nach Zustimmung zu %s-Cookies geladen.',

    // ===========================================
    // Banner Template (banner.php)
    // ===========================================
    'banner_aria_label' => 'Cookie-Einstellungen',
    'banner_always_active' => 'immer aktiv',
    'banner_privacy_link' => 'Datenschutzerklärung',
    'banner_imprint_link' => 'Impressum',
    'banner_save_selection' => 'Auswahl speichern',
    'banner_reject_all' => 'Alle ablehnen',
    'banner_accept_all' => 'Alle akzeptieren',

    // ===========================================
    // JavaScript Strings (banner.js)
    // ===========================================
    'js_preview_mode' => 'Keks: Preview-Modus aktiv',
    'js_accepted_preview' => 'Keks: Alle akzeptiert (Preview - nicht gespeichert)',
    'js_rejected_preview' => 'Keks: Alle abgelehnt (Preview - nicht gespeichert)',
    'js_selection_saved_preview' => 'Keks: Auswahl gespeichert (Preview)',
    'js_gcm_updated' => 'Keks: Google Consent Mode aktualisiert',
    'js_localstorage_unavailable' => 'Keks: localStorage nicht verfügbar',
    'js_logging_failed' => 'Keks: Consent-Logging fehlgeschlagen',
    'js_consent_saved' => 'Keks: Consent gespeichert',
    'js_revoke_failed' => 'Keks: Revoke-Logging fehlgeschlagen',
    'js_script_activated' => 'Keks: Script aktiviert',
    'js_iframe_activated' => 'Keks: Iframe aktiviert',
    'js_overlay_message' => 'Um diese Website nutzen zu können, müssen Sie der Verwendung von Cookies zustimmen.',
    'js_overlay_hint' => 'Bitte wählen Sie im Banner unten Ihre Cookie-Einstellungen.',
];
