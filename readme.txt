=== RW PostViewStats Lite ===
Contributors: robert2021south
Tags: page views, post views, view counter, statistics, analytics
Donate link: http://ko-fi.com/robertsouth
Requires at least: 6.6
Tested up to: 6.8
Requires PHP: 8.2
Stable tag: 1.0.2
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Language: en_US
Languages Folder: /languages
Text Domain: rw-postviewstats-lite
Update URI: https://wordpress.org/plugins/rw-postviewstats-lite/

A lightweight plugin to track post views via AJAX with anti-duplicate mechanisms and privacy-friendly design.

== Description ==
**RW PostViewStats Lite** provides accurate view tracking for posts, pages, and custom post types — designed for performance and privacy.

**🆓 Lite Version Features**:
- 🔢 **AJAX View Counting** – Async requests for better performance
- 🚫 **Duplicate Prevention** – 12-hour cookie-based protection
- 📊 **Sortable Admin Columns** – Sort posts by views in admin list
- 📁 **Data Cleaner** – Remove view data by date or post
- 🌐 **REST API Endpoint** – Fetch view counts via `/wp-json/rwpsl/v1/views/{post_id}`
- 📌 **Shortcode Support** – Display views with `[rwpsl_post_views]`
- 🔒 **Privacy Friendly** – No IP addresses collected

**⭐ Pro Version Additional Features**:
- 🌍 **Multisite Support** – Network-wide tracking for WordPress Multisite
- 📈 **Advanced Analytics** – Detailed views reports and charts
- 📁 **CSV Export** – Export stats by date range or specific posts
- 🔔 **Views Notifications** – Get notified when posts reach view milestones
- 🎯 **Custom Post Type Support** – Enhanced CPT tracking options
- ⚡ **Performance Optimizations** – Cached views and batch processing

**Documentation**:
[Full Documentation](https://docs.robertwp.com/rw-postviewstats-pro/) - Includes both free and pro version guides.

== Installation ==
1. Upload the `rw-postviewstats-lite` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure settings in Settings → RW PostViewStats Lite (optional)

== Frequently Asked Questions ==
= Can this plugin track views of custom post types? =
Yes. By default, it tracks the **'post'** and **'page'** types. You can add support for any custom post type using a filter hook in your theme or another plugin:
```php
add_filter('rwpsl_supported_post_types', function($types) {
    $types[] = 'your_custom_post_type';
    return $types;
});
= How are duplicate views prevented? =
The plugin uses browser cookies to ignore repeat visits from the same user for 12 hours.

= What data does the plugin collect? =
The plugin only stores post view counts in your WordPress database. It uses a cookie to prevent duplicate counting but does not collect any personal information or IP addresses.

= What are the system requirements? =
Tested with:
WordPress 6.6 to 6.8
PHP 8.2 to 8.4
MySQL 5.7 to 8.0

= How can I display view counts on my site? =
You can use the shortcode [rwpsl_post_views] in posts/pages/widgets, or use the provided REST API endpoint.

= Does this plugin support WordPress Multisite? =
Multisite support is available in the Pro version. The Lite version works on single WordPress installations.

= How to add translations? =
Copy /languages/rw-postviewstats-lite.pot and create your translation files. Submit your .po file to help improve the plugin.

== Screenshots ==
1. Settings Page – Main configuration screen
2. Admin Post List – Shows sortable "Views" column
3. Data Management – Clean view data by date or post
4. REST API Example – JSON response from views endpoint

== Changelog ==
= 1.0.2 =
Fix the problem of random numbers not being disinfected

= 1.0.1 =
Standard constant naming
Fix REST API callback permissions
Add protection code to HTML template

= 1.0.0 =

Initial release with AJAX tracking
Cookie-based duplicate prevention
REST API integration
Shortcode for displaying views

== Upgrade Notice ==
= 1.0.2 =
