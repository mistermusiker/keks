=== Keks - DSGVO/GDPR Cookie Banner ===
Contributors: onegiantleap
Tags: cookie, gdpr, dsgvo, consent, banner, privacy, cookie-banner, cookie-consent, google-consent-mode
Requires at least: 5.0
Tested up to: 6.7
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Simple, DSGVO/GDPR-compliant cookie banner without dark patterns. Supports Google Consent Mode v2.

== Description ==

Keks is a lightweight, privacy-focused cookie consent solution for WordPress. It follows GDPR requirements and avoids manipulative dark patterns - all buttons are equally styled to give users a fair choice.

**Key Features:**

* **DSGVO/GDPR Compliant** - Proper consent management with proof logging
* **No Dark Patterns** - All buttons equally prominent (accept, reject, save selection)
* **Google Consent Mode v2** - Built-in support for EU requirements since March 2024
* **Granular Control** - Users can choose individual cookie categories
* **30+ Pre-configured Services** - Google Analytics, Facebook Pixel, Hotjar, and more
* **Consent Logging** - Database logging for GDPR compliance proof
* **No Cookies Before Consent** - Uses localStorage until user decides
* **Multilingual** - German and English included
* **Lightweight** - No external dependencies

**Cookie Categories:**

* **Necessary** - Always active, cannot be disabled
* **Statistics** - Analytics tools (GA4, Matomo, Hotjar, etc.)
* **Marketing** - Advertising pixels (Facebook, Google Ads, LinkedIn, etc.)

**Pre-configured Services:**

*Statistics:* Google Analytics 4, Google Tag Manager, Matomo, Hotjar, Microsoft Clarity, Plausible, Fathom, etracker, Mouseflow, Mixpanel

*Marketing:* Facebook Pixel, Google Ads, LinkedIn Insight, TikTok Pixel, Pinterest Tag, Twitter/X Pixel, Snapchat Pixel, Bing Ads, Criteo, Taboola, Outbrain

*Chat & Support:* Intercom, HubSpot, Crisp, Zendesk, LiveChat, Tawk.to, Tidio

== Installation ==

1. Upload the `keks` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to **Keks > Settings** to configure your banner
4. Go to **Keks > Pages** to set your Privacy Policy and Imprint pages
5. Go to **Keks > Scripts** to add your tracking services
6. Add the revoke link to your footer: `[keks_revoke]`

== Frequently Asked Questions ==

= Is this plugin GDPR compliant? =

Yes. Keks follows GDPR principles:
- No cookies/tracking before consent
- Equal prominence for all choices (no dark patterns)
- Granular consent options
- Easy consent withdrawal
- Consent logging for compliance proof

= How do I add Google Analytics? =

1. Go to **Keks > Scripts**
2. Select "Google Analytics 4" from the dropdown
3. Click "Add"
4. Enter your Measurement ID (G-XXXXXXXXXX)
5. Save

The script will only load after user consent.

= How do users revoke their consent? =

Add the shortcode `[keks_revoke]` to your privacy policy or footer. Users can click this link to change their cookie settings at any time.

= What is Google Consent Mode v2? =

Since March 2024, Google requires websites using Google Ads in the EU to implement Consent Mode v2. Keks automatically sends the correct consent signals to Google based on user choices.

= Does the plugin work with caching? =

Yes. The banner uses JavaScript and localStorage, so it works with all caching plugins.

= Can I customize the banner text? =

Yes. Go to **Keks > Settings** and edit the banner text. You can also customize category names and descriptions.

= How do I add a custom tracking script? =

1. Go to **Keks > Scripts**
2. Select "Manual entry..." from the dropdown
3. Choose URL (external script) or Inline (code snippet)
4. Select the appropriate category and position
5. Save

== Screenshots ==

1. Cookie banner on the frontend
2. Settings page in WordPress admin
3. Script management interface
4. Consent log for GDPR compliance

== Changelog ==

= 1.0.0 =
* Initial release
* Cookie banner with granular consent
* Google Consent Mode v2 support
* 30+ pre-configured tracking services
* Consent logging for GDPR proof
* German and English translations
* Page exceptions (Privacy, Imprint)
* Shortcode for consent revocation

== Upgrade Notice ==

= 1.0.0 =
Initial release of Keks Cookie Banner.

== Privacy ==

Keks stores consent data in the browser's localStorage and optionally logs consent actions to your WordPress database for GDPR compliance proof. No data is sent to external servers.

**Data Stored:**
* Consent choices (localStorage)
* Consent timestamp (localStorage)
* Optional: IP address, user agent, URL (database log)

You can enable IP hashing in settings for additional privacy.
