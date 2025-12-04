-- WordPress Plugin Cache Clear SQL
-- REPLACE 'wp_' with your actual table prefix before running!
-- To find your prefix, look at your wp-config.php for $table_prefix

-- Example prefixes:
-- Standard: wp_
-- Custom example: es6g_

-- Clear plugin update transients (CHANGE wp_ TO YOUR PREFIX!)
DELETE FROM wp_options WHERE option_name LIKE '_site_transient_update_plugins%';
DELETE FROM wp_options WHERE option_name LIKE '_site_transient_timeout_update_plugins%';

-- Clear GitHub updater cache for this plugin (CHANGE wp_ TO YOUR PREFIX!)
DELETE FROM wp_options WHERE option_name LIKE '_transient_wcplt_github_release_%';
DELETE FROM wp_options WHERE option_name LIKE '_transient_timeout_wcplt_github_release_%';

-- Clear all transients (optional - use if above doesn't work, CHANGE wp_ TO YOUR PREFIX!)
-- DELETE FROM wp_options WHERE option_name LIKE '_transient_%';
-- DELETE FROM wp_options WHERE option_name LIKE '_site_transient_%';

-- EXAMPLE FOR es6g_ prefix:
-- DELETE FROM es6g_options WHERE option_name LIKE '_site_transient_update_plugins%';
-- DELETE FROM es6g_options WHERE option_name LIKE '_site_transient_timeout_update_plugins%';
-- DELETE FROM es6g_options WHERE option_name LIKE '_transient_wcplt_github_release_%';
-- DELETE FROM es6g_options WHERE option_name LIKE '_transient_timeout_wcplt_github_release_%';
