# Customize Cart Button

A WordPress plugin to customize WooCommerce "Add to Cart" button text, styling, and redirect behavior.

## Features

- **Custom Button Text**: Change the "Add to Cart" button text for shop pages, product pages, and different product types
- **Redirect to Product Page**: Redirect the Add to Cart button to the product page instead of adding directly to cart
- **Custom Button Styling**: Customize button colors, fonts, and CSS
- **Per-Product Settings**: Override global settings on individual products

## Requirements

- WordPress 5.0 or higher
- WooCommerce 3.0 or higher
- PHP 7.2 or higher

## Installation

1. Download the plugin ZIP file
2. Go to WordPress Admin > Plugins > Add New
3. Click "Upload Plugin" and select the ZIP file
4. Click "Install Now" and then "Activate"

Or install via GitHub:

```bash
cd wp-content/plugins
git clone https://github.com/webjive/Customize-Cart-Button.git customize-cart-button
```

## Configuration

After activation, go to **WooCommerce > Settings > Customize Cart Button** to configure:

### Button Text Settings
- Shop page button text
- Product page button text
- Variable product button text
- Grouped product button text
- Out of stock button text

### Redirect Settings
- Enable/disable redirect to product page
- Choose which product types to redirect

### Styling Settings
- Button background color
- Button text color
- Button hover colors
- Custom CSS

## Usage

### Global Settings

Navigate to **WooCommerce > Settings > Customize Cart Button** and configure your preferences.

### Per-Product Override

On individual product edit pages, you'll find a "Customize Cart Button" metabox where you can override global settings for that specific product.

## Development

This plugin is structured for easy development and extension:

```
customize-cart-button/
├── customize-cart-button.php    # Main plugin file
├── includes/
│   ├── class-ccb-settings.php          # Admin settings
│   └── class-ccb-button-customizer.php # Button customization logic
├── assets/
│   ├── css/
│   └── js/
└── languages/
```

## Support

For bug reports and feature requests, please use the [GitHub Issues](https://github.com/webjive/Customize-Cart-Button/issues) page.

## License

GPL v2 or later

## Author

Developed by [WebJIVE](https://web-jive.com) - Digital Marketing Agency in Little Rock, Arkansas

## Changelog

### 1.0.0
- Initial release
- Custom button text functionality
- Redirect to product page feature
- Basic styling options
