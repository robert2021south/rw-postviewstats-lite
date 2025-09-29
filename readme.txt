=== RW PostViewStats Lite ===
Contributors: robert2021south
Tags: views counter, post views, page views, ajax views, stats
Requires at least: 5.6
Tested up to: 6.8
Requires PHP: 7.2
Stable tag: 1.0.0
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Language: en_US
Languages Folder: /languages
Text Domain: rw-postviewstats-lite
Update URI: robertwp.com

A lightweight plugin to track post views via AJAX with anti-duplicate mechanisms, REST API, and GDPR-compliant data handling.

== Description ==
**RW PostViewStats Lite** provides accurate view tracking for posts, pages, and custom post types — designed for performance and privacy.
* Tracks views of standard **posts** and **pages** by default, and can be extended to support any **custom post type** via a filter hook.


**Key Features**:
- 🔢 **AJAX View Counting** – Async requests for better performance.
- 🚫 **Duplicate Prevention** – 12-hour cookie-based protection.
- 📊 **Sortable Admin Columns** – Sort posts by views.
- 📁 **Data Cleaner** – Clean the view data by date/post .
- 📁 **CSV Export** – Export stats by date/post .
- 🌐 **REST API** – Fetch data via `/wp-json/rwpsl/v1/views/{post_id}`.
- 📌 **Shortcodes** – Display views with `[rwpsl_post_views]`.
- 📌 **multisite support** – support main/sub site .
- 🔒 **GDPR Compliance** – IPs anonymized (e.g., `192.168.1.xxx`).

**📚 Documentation**:
[Full Guide](https://docs.robertwp.com/rw-postviewstats-lite/) | [API Reference](https://docs.robertwp.com/rw-postviewstats-lite/en/#/API)

== Installation ==
1. Upload to `/wp-content/plugins/rw-postviewstats-lite/`.
2. Activate in WordPress admin.
3. Enter your **License Key** in Settings → RW PostViewStats Lite.

== Frequently Asked Questions ==
= Can this plugin track views of custom post types? =
Yes. By default, it tracks the **'post'** and **'page'** types. You can add support for any custom post type using a filter hook in your theme or another plugin:
```php
add_filter('rwpsl_supported_post_types', function($types) {
    $types[] = 'your_custom_post_type';
    return $types;
});

= How are duplicate views prevented? =
Uses browser cookies to ignore repeat visits for 12 hours (configurable via JS).

= Can I export view data? =
Yes! Pro version supports CSV exports by post/date range.

= Is my personal data transmitted or stored? =
Some data such as IP address and admin email may be transmitted over HTTPS for license validation, but we do not store this data in identifiable form. All sensitive information is hashed or anonymized before storage.

= How to add translations? =
Copy `/languages/rw-postviewstats-lite.pot` and submit your `.po` file.

== Screenshots ==
1. Settings Page – Enable/disable features and enter license.
2. REST API Output – JSON response example.
3. Admin Post List – Sortable "Views" column.
4. Data Export – Filter and download CSV.

== Changelog ==
= 1.0.0 =
* Initial release with AJAX tracking and Pro features.
* GDPR-compliant IP anonymization and email hash.
* Fixed multilingual support.

== Privacy ==
This plugin sends the following data to our server via secure HTTPS connection for license validation and analytics:

- IP address (not stored in full; anonymized before saving)
- Admin email (used for validation, hashed before storage)
- Tracking ID (random site identifier)
- Domain name
- License key / purchase code
- Plugin name and version
- User-Agent string

We do **not** store full IPs or raw email addresses. All personally identifiable data is anonymized before storage. By activating the plugin, you agree to our [Privacy Policy](https://robertwp.com/privacy-policy).

== Upgrade Notice ==
= 1.0.0 =
First stable release. Free users can upgrade seamlessly.