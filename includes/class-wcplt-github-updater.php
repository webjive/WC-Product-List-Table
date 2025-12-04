<?php
/**
 * GitHub Updater Class
 *
 * Handles automatic plugin updates from GitHub repository
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WCPLT_GitHub_Updater {

    /**
     * Plugin file path
     */
    private $plugin_file;

    /**
     * Plugin slug
     */
    private $plugin_slug;

    /**
     * Plugin basename
     */
    private $plugin_basename;

    /**
     * GitHub username
     */
    private $github_username = 'webjive';

    /**
     * GitHub repository name
     */
    private $github_repo = 'WC-Product-List-Table';

    /**
     * GitHub API URL
     */
    private $github_api_url;

    /**
     * Plugin data
     */
    private $plugin_data;

    /**
     * Constructor
     */
    public function __construct( $plugin_file ) {
        $this->plugin_file     = $plugin_file;
        $this->plugin_basename = plugin_basename( $plugin_file );
        $this->plugin_slug     = dirname( $this->plugin_basename );
        $this->github_api_url  = "https://api.github.com/repos/{$this->github_username}/{$this->github_repo}/releases/latest";

        // Get plugin data
        if ( ! function_exists( 'get_plugin_data' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        $this->plugin_data = get_plugin_data( $plugin_file );

        // Hook into WordPress update system
        add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_for_update' ) );
        add_filter( 'plugins_api', array( $this, 'plugin_info' ), 10, 3 );
        add_filter( 'upgrader_post_install', array( $this, 'after_install' ), 10, 3 );
    }

    /**
     * Check for plugin updates
     */
    public function check_for_update( $transient ) {
        if ( empty( $transient->checked ) ) {
            return $transient;
        }

        // Get latest release info from GitHub
        $release_info = $this->get_latest_release();

        if ( ! $release_info ) {
            return $transient;
        }

        // Extract version number (remove 'v' prefix if present)
        $latest_version = ltrim( $release_info->tag_name, 'v' );
        $current_version = $this->plugin_data['Version'];

        // Compare versions
        if ( version_compare( $current_version, $latest_version, '<' ) ) {
            $plugin_data = array(
                'slug'        => $this->plugin_slug,
                'new_version' => $latest_version,
                'url'         => $this->plugin_data['PluginURI'],
                'package'     => $release_info->zipball_url,
                'tested'      => $this->plugin_data['Tested up to'] ?? '',
                'requires'    => $this->plugin_data['Requires at least'] ?? '',
                'requires_php' => $this->plugin_data['Requires PHP'] ?? '',
            );

            $transient->response[ $this->plugin_basename ] = (object) $plugin_data;
        }

        return $transient;
    }

    /**
     * Get plugin information for update screen
     */
    public function plugin_info( $result, $action, $args ) {
        if ( $action !== 'plugin_information' ) {
            return $result;
        }

        if ( ! isset( $args->slug ) || $args->slug !== $this->plugin_slug ) {
            return $result;
        }

        // Get latest release info
        $release_info = $this->get_latest_release();

        if ( ! $release_info ) {
            return $result;
        }

        $latest_version = ltrim( $release_info->tag_name, 'v' );

        $plugin_info = array(
            'name'          => $this->plugin_data['Name'],
            'slug'          => $this->plugin_slug,
            'version'       => $latest_version,
            'author'        => $this->plugin_data['Author'],
            'author_profile' => $this->plugin_data['AuthorURI'],
            'homepage'      => $this->plugin_data['PluginURI'],
            'requires'      => $this->plugin_data['Requires at least'] ?? '',
            'tested'        => $this->plugin_data['Tested up to'] ?? '',
            'requires_php'  => $this->plugin_data['Requires PHP'] ?? '',
            'downloaded'    => 0,
            'last_updated'  => $release_info->published_at,
            'sections'      => array(
                'description' => $this->plugin_data['Description'],
                'changelog'   => $this->parse_changelog( $release_info ),
            ),
            'download_link' => $release_info->zipball_url,
        );

        return (object) $plugin_info;
    }

    /**
     * After installation, rename the plugin directory
     */
    public function after_install( $response, $hook_extra, $result ) {
        global $wp_filesystem;

        // Only process if this is our plugin
        if ( ! isset( $hook_extra['plugin'] ) || $hook_extra['plugin'] !== $this->plugin_basename ) {
            return $response;
        }

        // Get the destination directory name
        $plugin_folder = WP_PLUGIN_DIR . '/' . dirname( $this->plugin_basename );

        // Move files from GitHub's extracted folder to the correct plugin folder
        $wp_filesystem->move( $result['destination'], $plugin_folder, true );
        $result['destination'] = $plugin_folder;

        // Reactivate the plugin
        if ( $this->is_plugin_active() ) {
            activate_plugin( $this->plugin_basename );
        }

        return $response;
    }

    /**
     * Get latest release information from GitHub API
     */
    private function get_latest_release() {
        // Check transient cache
        $cache_key = 'wcplt_github_release_' . md5( $this->github_api_url );
        $cached = get_transient( $cache_key );

        if ( false !== $cached ) {
            return $cached;
        }

        // Fetch from GitHub API
        $response = wp_remote_get(
            $this->github_api_url,
            array(
                'headers' => array(
                    'Accept' => 'application/vnd.github.v3+json',
                ),
            )
        );

        if ( is_wp_error( $response ) ) {
            return false;
        }

        $body = wp_remote_retrieve_body( $response );
        $release_info = json_decode( $body );

        if ( empty( $release_info ) || isset( $release_info->message ) ) {
            return false;
        }

        // Cache for 6 hours
        set_transient( $cache_key, $release_info, 6 * HOUR_IN_SECONDS );

        return $release_info;
    }

    /**
     * Parse changelog from release notes
     */
    private function parse_changelog( $release_info ) {
        if ( empty( $release_info->body ) ) {
            return 'No changelog available.';
        }

        // Convert markdown to HTML
        $changelog = wpautop( $release_info->body );

        return $changelog;
    }

    /**
     * Check if plugin is currently active
     */
    private function is_plugin_active() {
        return is_plugin_active( $this->plugin_basename );
    }

    /**
     * Clear update cache (useful for debugging)
     */
    public function clear_cache() {
        $cache_key = 'wcplt_github_release_' . md5( $this->github_api_url );
        delete_transient( $cache_key );
        delete_site_transient( 'update_plugins' );
    }
}
