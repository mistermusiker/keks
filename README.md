# Keks - DSGVO/GDPR Cookie Banner

Simple, DSGVO/GDPR-compliant cookie banner without dark patterns. Supports Google Consent Mode v2.

## Features

* **DSGVO/GDPR Compliant** - Proper consent management with proof logging
* **No Dark Patterns** - All buttons equally prominent (accept, reject, save selection)
* **Google Consent Mode v2** - Built-in support for EU requirements since March 2024
* **Granular Control** - Users can choose individual cookie categories
* **30+ Pre-configured Services** - Google Analytics, Facebook Pixel, Hotjar, and more
* **Consent Logging** - Database logging for GDPR compliance proof
* **No Cookies Before Consent** - Uses localStorage until user decides
* **Multilingual** - German and English included
* **Lightweight** - No external dependencies

## Installation

1. Upload the `keks` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to **Keks > Settings** to configure your banner
4. Go to **Keks > Pages** to set your Privacy Policy and Imprint pages
5. Go to **Keks > Scripts** to add your tracking services
6. Add the revoke link to your footer: `[keks_revoke]`

## Requirements

* WordPress 5.0 or higher
* PHP 7.4 or higher

## License

GPL v2 or later

## Author

Roger Kirchhoff
