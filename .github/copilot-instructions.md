## WC Product List Table — Copilot Instructions

Short summary
- This is a small WordPress/WooCommerce plugin that customizes shop list/table layouts and the Add-to-Cart button (text, redirect, styling). Primary runtime files: `wc-product-list-table.php`, `includes/class-wcplt-button-customizer.php`, and `includes/class-wcplt-settings.php`.

Big picture (what matters)
- The plugin boots from `wc-product-list-table.php` which defines constants and a singleton class `WC_Product_List_Table` and a helper `wc_product_list_table()` to initialize. Most behavior lives in two singleton classes under `includes/`:
  - `WCPLT_Button_Customizer` — registers filters/actions to change button text, change add-to-cart links (redirect), hide quantity, emit inline styles, and implement the table layout wrapper.
  - `WCPLT_Settings` — extends `WC_Settings_Page` and exposes admin sections/options. Also adds a product metabox to set product-level overrides.

Key patterns & conventions (project-specific)
- Singleton classes: both major classes use a `get_instance()` static method and private constructor.
- Option keys: global options use `wcplt_*` (e.g. `wcplt_enable_custom_text`, `wcplt_redirect_simple`). Code checks values using string flags `'yes'`/`'no'` (not booleans). Always read defaults with `get_option( 'wcplt_x', 'no' )`.
- Post meta keys: product-level keys are `_wcplt_override_text`, `_wcplt_custom_text`, `_wcplt_override_redirect`, `_wcplt_disable_redirect`.
- Hooks: uses `add_action`/`add_filter` WP APIs; notable filters include `woocommerce_product_add_to_cart_text`, `woocommerce_loop_add_to_cart_link`, and `woocommerce_loop_add_to_cart_args`.
- Text domain: `wc-product-list-table` — use when adding translations or running gettext tools.
- Inline CSS policy: CSS is generated in `WCPLT_Button_Customizer::add_custom_styles()` and printed in `wp_head`; if modifying styling, keep logic there or move to an enqueued stylesheet if needed.

```markdown
## WC Product List Table — Copilot Instructions

Short summary
- Small WooCommerce plugin that alters shop/archive layouts and customizes the Add-to-Cart button (text, redirect, styling). Primary files: `wc-product-list-table.php`, `includes/class-wcplt-button-customizer.php`, `includes/class-wcplt-settings.php`.

Big picture
- `wc-product-list-table.php` boots the plugin: defines `WCPLT_*` constants and the singleton `WC_Product_List_Table` which registers activation/migration and initializes features after WooCommerce loads.
- Runtime behavior lives in two singletons under `includes/`:
  - `WCPLT_Button_Customizer` — button text filters, redirecting add-to-cart links, table/layout output, inline CSS in `wp_head`.
  - `WCPLT_Settings` — extends `WC_Settings_Page`: exposes settings sections (layout, text, redirect, styling) and a product metabox for per-product overrides.

Patterns & repo conventions (important)
- Singleton pattern: classes expose `get_instance()` and use private constructors.
- Option flags: use string values `'yes'` / `'no'`. Always call `get_option('wcplt_key', 'no')` and compare with `'yes'`.
- Option keys: start with `wcplt_` (examples: `wcplt_enable_custom_text`, `wcplt_redirect_simple`, `wcplt_enable_styling`).
- Postmeta keys: product overrides use `_wcplt_override_text`, `_wcplt_custom_text`, `_wcplt_override_redirect`, `_wcplt_disable_redirect`.
- Hook points: key filters/actions to use or extend — `woocommerce_product_add_to_cart_text`, `woocommerce_product_single_add_to_cart_text`, `woocommerce_loop_add_to_cart_link`, `woocommerce_loop_add_to_cart_args`, `wp_head` (for inline CSS), and the `woocommerce_get_settings_pages` filter (settings loader).

Important implementation notes
- Inline CSS: `WCPLT_Button_Customizer::add_custom_styles()` prints styling in `wp_head`. For larger changes prefer enqueuing a stylesheet.
- Table layout wrappers: `table_layout_start()` and `table_layout_end()` wrap product markup; WooCommerce will inject title/price/button between hooks — keep wrappers consistent when editing markup.
- Redirect behavior: `redirect_to_product_page()` rebuilds anchor HTML. Respect product-level overrides (`_wcplt_override_redirect` / `_wcplt_disable_redirect`). Use `get_option('wcplt_redirect_new_tab')` to set target attribute.
- Migrations: `migrate_from_old_plugin()` updates option keys and renames postmeta via `$wpdb->query()` and sets `wcplt_migration_done` to keep it idempotent. Avoid changing migration SQL without testing on a copy of the DB.
- Admin metabox: `WCPLT_Settings::render_product_metabox()` uses nonces and `save_post` checks. Follow the same nonce/permission pattern when adding fields.

Quick code examples (copy-paste safe)
- Read a flag: ``$enabled = 'yes' === get_option( 'wcplt_enable_custom_text', 'no' );``
- Read product override: ``$override = get_post_meta( $product->get_id(), '_wcplt_override_text', true );``
- Add a filter: ``add_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'redirect_to_product_page' ), 10, 2 );``

Local dev & testing
- Requirements: PHP >= 7.2, WordPress (wp >= 5.0), WooCommerce 3.0+. The plugin header lists tested WC version.
- To test locally, drop the plugin into `wp-content/plugins/` of a local WP instance with WooCommerce and activate. Example using WP-CLI:

```bash
wp plugin activate wc-product-list-table
wp option get wcplt_enable_custom_text
```

- To trigger migration locally, (re)activate the plugin so `register_activation_hook` runs. Use a copy of your DB before testing migrations.

Dev guidance for AI agents (do this, not that)
- Prefer using existing setting APIs: when adding new settings, add them in `WCPLT_Settings::get_*_settings()` so they appear in WooCommerce settings pages.
- Keep guard toggles: new behavior should respect a `wcplt_` option checked with `'yes' === get_option(..., 'no')`.
- Avoid changing global SQL in migrations without a DB copy — migrations rename meta keys via `$wpdb->query()`.
- Follow existing markup hooks for layout changes — do not hardcode WooCommerce markup expectations.

Where to look (quick references)
- `wc-product-list-table.php` — bootstrap, constants, activation/migration, `add_settings_page()` loader.
- `includes/class-wcplt-button-customizer.php` — `custom_button_text()`, `custom_single_button_text()`, `redirect_to_product_page()`, `add_custom_styles()`, `table_layout_start()`, `table_layout_end()`.
- `includes/class-wcplt-settings.php` — `get_layout_settings()`, `get_button_text_settings()`, `get_redirect_settings()`, `get_styling_settings()`, `render_product_metabox()`, `save_product_metabox()`.

Missing / ask before changing
- Tests/CI config (none present). Tell us preferred local stack (Docker, LocalWP, Lando) if you want runnable instructions added.
- Any coding standards (PHPCS rules) you want enforced.

If you'd like, I can open a PR with small follow-ups (move CSS to an enqueued file, add type hints where safe). Ask for changes or clarifications.
```
