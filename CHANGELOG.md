# Changelog

All notable changes to this project will be documented in this file.

## [1.0.0] - 2024-12-04

### Added
- Initial release
- Custom button text functionality for shop pages, single product pages, and different product types
- Redirect to product page feature with product type filtering
- Button styling options (colors, hover states, border radius, padding)
- Custom CSS support for advanced styling
- Per-product override settings via metabox
- WooCommerce settings page integration with three sections: Button Text, Redirect Settings, and Button Styling
- Proper WooCommerce dependency checking
- Clean uninstall functionality
- Translation ready with text domain

### Features
- **Button Text Customization**
  - Separate text for shop pages and single product pages
  - Specific text for variable, grouped, and external products
  - Custom out of stock button text
  - Per-product text override

- **Redirect Functionality**
  - Global enable/disable toggle
  - Product type filtering (simple, variable, grouped)
  - Optional new tab opening
  - Per-product redirect override

- **Styling Options**
  - Background and text colors
  - Hover state colors
  - Border radius control
  - Padding customization
  - Custom CSS field for advanced users

### Technical
- Object-oriented architecture with singleton pattern
- WordPress coding standards compliance
- Secure with nonce verification and capability checks
- Proper escaping and sanitization
- Hooks and filters for extensibility
