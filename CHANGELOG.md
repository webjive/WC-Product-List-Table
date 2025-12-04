# Changelog

All notable changes to WC Product List Table will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.2.2] - 2025-12-04

### Fixed
- **Critical: Add to Cart Button Not Displaying**: Fixed issue where the Add to Cart button was not appearing on product listings
- Restructured HTML wrapper rendering from two methods to three methods to ensure proper WooCommerce hook execution
- The actions wrapper now stays open while WooCommerce injects price and button elements
- Used `woocommerce_after_shop_loop_item_title` hook (priority 15) for the middle transition point

### Technical
- Split table layout rendering into three methods: `table_layout_start()`, `table_layout_middle()`, `table_layout_end()`
- Improved hook timing to work correctly with WooCommerce's default price (priority 10) and button (priority 10) injection
- Ensures `.wcplt-table-actions` wrapper remains open for WooCommerce content insertion

## [2.2.1] - 2025-12-04

### Fixed
- **Hide Product Images in Table Layout**: Added CSS to automatically hide product featured images when table layout mode is enabled
- Ensures clean card-based layout with only text content (title, description, price, button)
- Matches the intended design with no large product images interfering with the layout

### Changed
- Improved table layout CSS to enforce image-free product cards
- Better visual consistency in list view mode

## [2.2.0] - 2025-12-04

### Added
- **Shortcode Support**: New `[wcplt_products]` and `[wc_product_table]` shortcodes to embed product tables anywhere
- **11 Shortcode Parameters**: Comprehensive filtering and sorting options
  - `limit` - Control number of products displayed
  - `columns` - Set grid layout columns
  - `orderby` - Sort by date, title, price, popularity, rating
  - `order` - Ascending or descending sort order
  - `category` - Filter by product category slug(s)
  - `tag` - Filter by product tag(s)
  - `ids` - Display specific product IDs
  - `skus` - Filter by product SKU(s)
  - `on_sale` - Show only products on sale
  - `best_selling` - Display best-selling products
  - `top_rated` - Show top-rated products
  - `class` - Add custom CSS classes
- New shortcode class: `WCPLT_Shortcode` for handling all shortcode functionality
- Helper method `get_products_by_category()` for category-based filtering
- Full WP_Query integration with tax_query and meta_query support
- Automatic application of all plugin settings to shortcode output

### Changed
- Shortcodes inherit table layout, button styling, and all plugin customizations
- Improved code organization with dedicated shortcode handler class
- Enhanced plugin architecture for better extensibility

### Technical
- Added `includes/class-wcplt-shortcode.php` with complete shortcode functionality
- Integrated shortcode initialization in main plugin class
- Version bumped from 2.1.0 to 2.2.0

## [2.1.0] - 2025-12-04

### Added
- **Table Layout Mode**: New setting to display products in a clean table/list format with improved spacing and visual hierarchy
- **Hide Quantity Selector**: Option to remove quantity input fields from product listings
- **Description Display Control**: Toggle to show/hide product short descriptions with character limit option
- **Button Icon Support**: Add shopping cart icon to "Add to Cart" buttons using WordPress Dashicons
- **Button Width Options**: Choose between auto, full width, or fixed width button styles
- **Button Size Control**: Select from small, medium, or large button sizes
- **Enhanced Layout Options**: New "Layout & Display" settings tab for better organization
- Product descriptions in table layout with character truncation support
- Flexible button styling with multiple size and width configurations

### Changed
- Reorganized settings tabs: Added "Layout & Display" as primary tab
- Improved settings organization with dedicated sections for different features
- Enhanced CSS styling for table layout mode with card-based design
- Updated button customization with more granular control options

### Fixed
- Improved table layout rendering with proper wrapper closures
- Better responsive handling for table layout mode
- Enhanced description trimming to prevent layout breaking

## [2.0.0] - 2025-12-04

### Changed
- **BREAKING**: Plugin renamed from "Customize Cart Button" to "WC Product List Table"
- Updated plugin slug from `customize-cart-button` to `wc-product-list-table`
- Updated text domain from `customize-cart-button` to `wc-product-list-table`
- Updated all class prefixes from `CCB_` to `WCPLT_`
- Updated all option keys from `ccb_*` to `wcplt_*`
- Updated all meta keys from `_ccb_*` to `_wcplt_*`
- Updated GitHub repository URL to reflect new name
- Bumped version to 2.0.0

### Added
- Automatic migration system for users upgrading from "Customize Cart Button"
- Migration runs on plugin activation via `register_activation_hook()`
- Converts all old database options to new format
- Converts all product meta keys to new format
- Migration tracking flag (`wcplt_migration_done`) to prevent duplicate migrations
- Enhanced documentation with migration instructions

### Fixed
- Fixed undefined `$args` variable bug in `redirect_to_product_page()` method
- Improved code documentation and inline comments

### Removed
- Old class files with CCB_ prefix (replaced with WCPLT_ naming convention)

## [1.0.0] - 2024-12-04

### Added
- Initial release as "Customize Cart Button"
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

### Fixed (post-initial release patches)
- Fixed WooCommerce settings class loading order issue
- Fixed array formatting in redirect settings
