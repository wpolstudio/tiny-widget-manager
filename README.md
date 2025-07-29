# Tiny Widget Manager

**Tiny Widget Manager (TWIM)** enhances the WordPress widget system by letting you control the visibility of each widget based on various conditions.

📦 Download on [WordPress.org](https://wordpress.org/plugins/tiny-widget-manager/)
💬 Support me on [Ko-fi](https://ko-fi.com/wpolstudio)
🧩 Tags: widgets, visibility, admin, logic, translation-ready
🧪 Requires: WordPress 5.0+, PHP 7.4+
✅ Tested up to: WordPress 6.8
🪪 License: [GPLv2 or later](https://www.gnu.org/licenses/gpl-2.0.html)

---

## ✨ Features

- Show/hide widgets on specific **pages**
- Show/hide widgets on **post types** (including custom post types)
- Control visibility on **archive pages** (categories, tags, author, date)
- Conditional display by **user status** or **user role**
- Responsive logic based on **device type** (mobile, tablet, desktop)
- Global **AND/OR logic selector** for combining rules
- **Active condition indicators** via eye icons for easy overview
- Add **custom CSS classes** to widgets without extra plugins
- Basic settings panel (legacy mode toggle & color theme)

---

## ⚙️ Installation

1. Upload the plugin to the `/wp-content/plugins/tiny-widget-manager` directory, or install via the WordPress plugin dashboard.
2. Activate the plugin from the **Plugins** menu.
3. Go to **Appearance > Widgets**, edit a widget, and set visibility conditions using the new panel.

---

## ❓ FAQ

### Does this plugin work with block-based (FSE) themes?

Not currently. TWIM is compatible only with **classic widget-based themes**.

### Does it support custom post types and taxonomies?

Yes. All registered post types and archives are supported.

### Can I add custom CSS classes to widgets?

Yes — a field is provided for this purpose.

### Will it slow down my site?

No. TWIM is lightweight and processes logic server-side only when necessary.

---

## 🖼️ Screenshots

> These are available on the [plugin page on WordPress.org](https://wordpress.org/plugins/tiny-widget-manager/)

1. Condition selector – pages (default blue theme)
2. Condition selector – post types (gray theme)
3. Condition selector – archives (orange theme)
4. Condition selector – user roles (lime theme)
5. Condition selector – device types
6. Global AND/OR logic selector
7. Active condition indicator icons
8. Custom CSS class input
9. Settings page

---

## 📝 Changelog

### 1.0.1
- Bug fix: visibility rule for `post` also matched `post` archives

### 1.0.0
- Initial release with all core features:
  - Visibility rules by page, post type, archive, user, and device
  - Custom widget class input

---

## 🚀 Contributing

Pull requests and suggestions are welcome!

If you're reporting an issue, please include:
- Plugin version
- WordPress version
- Steps to reproduce
- Expected vs. actual behavior

---

## 📄 License

This plugin is licensed under the [GPLv2 or later](https://www.gnu.org/licenses/gpl-2.0.html).

---

💡 **Made with ❤️ by [wpolstudio](https://ko-fi.com/wpolstudio)**
