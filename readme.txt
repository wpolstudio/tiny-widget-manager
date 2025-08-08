=== Tiny Widget Manager ===
Contributors: wpolstudio
Donate link: https://ko-fi.com/wpolstudio
Tags: widgets, visibility, admin, logic, translation-ready
Requires at least: 5.0
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Plugin URI: https://github.com/wpolstudio/tiny-widget-manager

**Tiny Widget Manager** enhances the WordPress widget system by letting you control the visibility of each widget based on various conditions.

== Description ==

**Tiny Widget Manager (TWIM)** improves the WordPress widget system by allowing you to control each widget's visibility directly from the admin panel.
It’s a lightweight yet powerful tool that gives you full control over how and when widgets are displayed.

TWIM adds a simple interface below each widget for defining visibility rules. This provides site editors with a much smarter and more flexible way to manage widget visibility.

= Available show/hide conditions =

The power of TWIM lies in the variety of logic conditions it supports:
- Show/hide on specific *page(s)*
- Show/hide on specific *post type(s)* (custom or built-in)
- Show/hide on *archive* pages (category, tag, author, date)
- Show/hide by *user* status (logged-in, logged-out, user roles)
- Show/hide on *device* type (mobile, tablet, desktop)

= Global AND/OR setting for conditions =
For each widget, you can also define whether *all* conditions must be true or if *only one* is enough.
Since each group of condtions has its own show/hide setting, combinations are nearly endless.

= Active conditions indicator =
When setting visibility conditions for each of your widgets, it can soon become difficult to understand which conditions have been set.
TWIM provides a useful active condition indicator in the form of "eye" icons allowing you to check in a glance which conditions have been activated for a given plugin (see screenshots).

= Widget Class =
A dedicated input field allows you to add *custom CSS classes* to your widgets—no need for an additional plugin just for styling.

= Settings =
The settings page currently offers two options:
- Restore the classic widget management screen (instead of the block-based "Widgets" editor — see *Limitations* below).
- Choose the TWIM color theme for the admin interface.

= Limitations =
TWIM does not currently support the new block-based widget editor introduced in recent WordPress versions.
If you want to use TWIM, you must switch to the *legacy widget interface* (this can be done from the plugin’s settings).

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/tiny-widget-manager` directory, or install it via the WordPress plugin screen.
2. Activate the plugin through the “Plugins” menu in WordPress.
3. Go to **Appearance > Widgets**, open any widget, and configure its visibility using the new panel.

== Frequently Asked Questions ==

= Does this plugin work with block-based (FSE) themes? =
No. Tiny Widget Manager currently supports classic widget-based themes only.

= Does it support custom post types and taxonomies? =
Yes, visibility rules can be applied to any registered post type or archive.

= Can I add custom CSS classes to widgets? =
Yes! A built-in input lets you apply your own classes—no third-party plugin needed.

= Will it slow down my site? =
No. The plugin is lightweight and adds minimal overhead. Visibility logic is evaluated server-side only when necessary.

== Development ==
You can contribute to this plugin or follow development on GitHub:
🔗 https://github.com/wpolstudio/tiny-widget-manager

== Screenshots ==

1. Condition selector showing pages (default blue color theme)
2. Condition selector showing single post type (gray color theme)
3. Condition selector showing archive pages (orange color theme)
4. Condition selector showing user roles (lime color theme)
5. Condition selector showing device types
6. Global logic selector
7. Active condition(s) indicator
8. Custom CSS class(es) input
9. TWIM settings page

== Changelog ==

= 1.0.0 =
* Initial release with support for page, post type, archive, user, and device-based visibility rules
* Added input for applying custom CSS classes to widgets
= 1.0.1 =
* Bug fix for post type = "post" also accepted for "post" archives

== Upgrade Notice ==

= 1.0.0 =
First stable version. No upgrade steps required.


