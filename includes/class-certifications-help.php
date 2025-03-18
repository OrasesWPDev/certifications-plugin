<?php
/**
 * Help Documentation Class
 *
 * @package Certifications_Plugin
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class to handle help documentation for the Certifications plugin.
 */
class Certifications_Help {

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Add the help page to the admin menu
		add_action( 'admin_menu', array( $this, 'add_help_page' ) );

		// Add admin-specific styles
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		
		// Add AJAX handler for dismissing the cache notice
		add_action( 'wp_ajax_dismiss_certifications_cache_notice', array( $this, 'dismiss_cache_notice' ) );
	}

	/**
	 * Add help/documentation page
	 */
	public function add_help_page() {
		add_submenu_page(
			'edit.php?post_type=certification',  // Parent menu slug
			__( 'Certifications Help', 'certifications-plugin' ), // Page title
			__( 'How to Use', 'certifications-plugin' ),          // Menu title
			'edit_posts',                                         // Capability
			'certifications-help',                                // Menu slug
			array( $this, 'help_page_content' )                   // Callback function
		);
		
		// Add Cache Management page
		add_submenu_page(
			'edit.php?post_type=certification',  // Parent menu slug
			__( 'Cache Management', 'certifications-plugin' ), // Page title
			__( 'Cache Management', 'certifications-plugin' ),  // Menu title
			'manage_options',                                   // Capability - admin only
			'certifications-cache',                             // Menu slug
			array( $this, 'cache_management_page' )             // Callback function
		);
	}

	/**
	 * Enqueue styles for admin help page
	 *
	 * @param string $hook Current admin page
	 */
	public function enqueue_admin_styles($hook) {
		// Only load on our help page
		if ('certification_page_certifications-help' !== $hook) {
			return;
		}

		// Add inline styles for help page
		wp_add_inline_style('wp-admin', $this->get_admin_styles());
	}

	/**
	 * Get admin styles for help page
	 *
	 * @return string CSS styles
	 */
	/**
	 * AJAX handler for dismissing the cache notice
	 */
	public function dismiss_cache_notice() {
		// Verify nonce
		if (!isset($_REQUEST['nonce']) || !wp_verify_nonce($_REQUEST['nonce'], 'dismiss_certifications_cache_notice')) {
			wp_die('Security check failed');
		}
		
		// Update user meta to mark notice as dismissed
		update_user_meta(get_current_user_id(), 'certifications_cache_notice_dismissed', true);
		
		wp_die();
	}

	private function get_admin_styles() {
		return '
            .certifications-help-wrap {
                max-width: 1300px; /* Increased from 1200px */
                margin: 20px 20px 0 0;
            }
            .certifications-help-header {
                background: #fff;
                padding: 20px;
                border-radius: 3px;
                margin-bottom: 20px;
                border-left: 4px solid #2c6c3e;
                box-shadow: 0 1px 1px rgba(0,0,0,.04);
            }
            .certifications-help-section {
                background: #fff;
                padding: 20px;
                border-radius: 3px;
                margin-bottom: 20px;
                box-shadow: 0 1px 1px rgba(0,0,0,.04);
                overflow-x: auto; /* Added for table overflow */
            }
            .certifications-help-section h2 {
                border-bottom: 1px solid #eee;
                padding-bottom: 10px;
                margin-top: 0;
            }
            .certifications-help-section h3 {
                margin-top: 1.5em;
                margin-bottom: 0.5em;
            }
            .certifications-help-section table {
                border-collapse: collapse;
                width: 100%;
                margin: 1em 0;
                table-layout: fixed;
            }
            .certifications-help-section table th,
            .certifications-help-section table td {
                text-align: left;
                padding: 8px;
                border: 1px solid #ddd;
                vertical-align: top;
                word-wrap: break-word;
                word-break: break-word; /* Added to break long words */
                hyphens: auto; /* Added for better text wrapping */
            }
            /* Adjust column widths */
            .certifications-help-section table th:nth-child(1), 
            .certifications-help-section table td:nth-child(1) {
                width: 15%; /* Parameter column */
            }
            .certifications-help-section table th:nth-child(2), 
            .certifications-help-section table td:nth-child(2) {
                width: 25%; /* Description column */
            }
            .certifications-help-section table th:nth-child(3), 
            .certifications-help-section table td:nth-child(3) {
                width: 10%; /* Default column */
            }
            .certifications-help-section table th:nth-child(4), 
            .certifications-help-section table td:nth-child(4) {
                width: 20%; /* Options column */
            }
            .certifications-help-section table th:nth-child(5), 
            .certifications-help-section table td:nth-child(5) {
                width: 30%; /* Examples column */
            }
            .certifications-help-section table th {
                background-color: #f8f8f8;
                font-weight: 600;
            }
            .certifications-help-section table tr:nth-child(even) {
                background-color: #f9f9f9;
            }
            .certifications-help-section code {
                background: #f8f8f8;
                padding: 2px 6px;
                border-radius: 3px;
                font-size: 13px;
                color: #0073aa;
                display: inline-block;
                max-width: 100%; /* Ensure code blocks does not overflow */
                overflow-wrap: break-word; /* Allow long code to wrap */
                white-space: normal; /* Allow long code to wrap */
            }
            .certifications-shortcode-example {
                background: #f8f8f8;
                padding: 15px;
                border-left: 4px solid #0073aa;
                font-family: monospace;
                margin: 10px 0;
                overflow-x: auto; /* Allow scrolling for very long examples */
                white-space: pre-wrap; /* Better wrapping for code examples */
                word-break: break-word; /* Break words if necessary */
            }
        ';
	}

	/**
	 * Content for cache management page
	 */
	public function cache_management_page() {
		// Check if the clear cache button was clicked
		if (isset($_POST['clear_certifications_cache']) && 
			isset($_POST['certifications_cache_nonce']) && 
			wp_verify_nonce($_POST['certifications_cache_nonce'], 'clear_certifications_cache')) {
			
			// Clear the cache
			if (function_exists('certifications_plugin_clear_cache')) {
				certifications_plugin_clear_cache();
				echo '<div class="notice notice-success is-dismissible"><p>' . 
					__('Certifications cache cleared successfully!', 'certifications-plugin') . 
					'</p></div>';
			}
		}
		?>
		<div class="wrap">
			<h1><?php _e('Certifications Cache Management', 'certifications-plugin'); ?></h1>
			
			<div class="card">
				<h2><?php _e('Clear Cache', 'certifications-plugin'); ?></h2>
				<p><?php _e('If you\'ve deleted or modified certifications and they still appear in shortcodes, clear the cache to refresh the data.', 'certifications-plugin'); ?></p>
				
				<form method="post" action="">
					<?php wp_nonce_field('clear_certifications_cache', 'certifications_cache_nonce'); ?>
					<p>
						<input type="submit" name="clear_certifications_cache" class="button button-primary" 
							value="<?php _e('Clear Certifications Cache', 'certifications-plugin'); ?>">
					</p>
				</form>
			</div>
			
			<div class="card">
				<h2><?php _e('About Caching', 'certifications-plugin'); ?></h2>
				<p><?php _e('The Certifications plugin uses caching to improve performance. Cached data includes:', 'certifications-plugin'); ?></p>
				<ul style="list-style-type: disc; margin-left: 20px;">
					<li><?php _e('Certification grid/list shortcode output', 'certifications-plugin'); ?></li>
					<li><?php _e('Single certification shortcode output', 'certifications-plugin'); ?></li>
					<li><?php _e('Certification images shortcode output', 'certifications-plugin'); ?></li>
				</ul>
				<p><?php _e('Cache is automatically cleared when certifications are updated, but manual clearing may be needed after bulk operations.', 'certifications-plugin'); ?></p>
			</div>
			
			<div class="card">
				<h2><?php _e('Disable Caching Temporarily', 'certifications-plugin'); ?></h2>
				<p><?php _e('You can also disable caching for individual shortcodes by adding the cache="false" parameter:', 'certifications-plugin'); ?></p>
				<code>[certifications cache="false"]</code><br>
				<code>[certification id="123" cache="false"]</code><br>
				<code>[certification_images cache="false"]</code>
			</div>
		</div>
		<?php
	}

	/**
	 * Content for help page
	 */
	public function help_page_content() {
		?>
        <div class="wrap certifications-help-wrap">
            <div class="certifications-help-header">
                <h1><?php esc_html_e('Certifications - Documentation', 'certifications-plugin'); ?></h1>
                <p><?php esc_html_e('This page provides documentation on how to use Certifications shortcodes and features.', 'certifications-plugin'); ?></p>
            </div>

            <!-- Overview Section -->
            <div class="certifications-help-section">
                <h2><?php esc_html_e('Overview', 'certifications-plugin'); ?></h2>
                <p><?php esc_html_e('Certifications Plugin allows you to create and display certification offerings on your site. The plugin provides two main shortcodes:', 'certifications-plugin'); ?></p>
                <ul>
                    <li><code>[certifications]</code> - <?php esc_html_e('Display multiple certifications in a grid or list layout', 'certifications-plugin'); ?></li>
                    <li><code>[certification]</code> - <?php esc_html_e('Display a single certification\'s details', 'certifications-plugin'); ?></li>
                </ul>
            </div>

            <!-- Multiple Certifications Shortcode Section -->
            <div class="certifications-help-section">
                <h2><?php esc_html_e('Shortcode: [certifications]', 'certifications-plugin'); ?></h2>
                <p><?php esc_html_e('This shortcode displays a grid or list of Certifications with various customization options.', 'certifications-plugin'); ?></p>

                <h3><?php esc_html_e('Basic Usage', 'certifications-plugin'); ?></h3>
                <div class="certifications-shortcode-example">
                    [certifications]
                </div>

                <h3><?php esc_html_e('Display Options', 'certifications-plugin'); ?></h3>
                <table>
                    <tr>
                        <th><?php esc_html_e('Parameter', 'certifications-plugin'); ?></th>
                        <th><?php esc_html_e('Description', 'certifications-plugin'); ?></th>
                        <th><?php esc_html_e('Default', 'certifications-plugin'); ?></th>
                        <th><?php esc_html_e('Options', 'certifications-plugin'); ?></th>
                        <th><?php esc_html_e('Examples', 'certifications-plugin'); ?></th>
                    </tr>
                    <tr>
                        <td><code>display_type</code></td>
                        <td><?php esc_html_e('Layout type for certifications', 'certifications-plugin'); ?></td>
                        <td><code>grid</code></td>
                        <td><code>grid</code>, <code>list</code></td>
                        <td><code>display_type="list"</code></td>
                    </tr>
                    <tr>
                        <td><code>columns</code></td>
                        <td><?php esc_html_e('Number of columns in grid view', 'certifications-plugin'); ?></td>
                        <td><code>4</code></td>
                        <td><?php esc_html_e('any number (1-6 recommended)', 'certifications-plugin'); ?></td>
                        <td><code>columns="3"</code></td>
                    </tr>
                    <tr>
                        <td><code>count</code></td>
                        <td><?php esc_html_e('Number of certifications to display', 'certifications-plugin'); ?></td>
                        <td><code>-1</code></td>
                        <td><?php esc_html_e('any number, -1 for all', 'certifications-plugin'); ?></td>
                        <td><code>count="6"</code><br><code>count="-1"</code></td>
                    </tr>
                    <tr>
                        <td><code>pagination</code></td>
                        <td><?php esc_html_e('Whether to show pagination', 'certifications-plugin'); ?></td>
                        <td><code>false</code></td>
                        <td><code>true</code>, <code>false</code></td>
                        <td><code>pagination="true"</code></td>
                    </tr>
                </table>

                <h3><?php esc_html_e('Ordering Parameters', 'certifications-plugin'); ?></h3>
                <table>
                    <tr>
                        <th><?php esc_html_e('Parameter', 'certifications-plugin'); ?></th>
                        <th><?php esc_html_e('Description', 'certifications-plugin'); ?></th>
                        <th><?php esc_html_e('Default', 'certifications-plugin'); ?></th>
                        <th><?php esc_html_e('Options', 'certifications-plugin'); ?></th>
                        <th><?php esc_html_e('Examples', 'certifications-plugin'); ?></th>
                    </tr>
                    <tr>
                        <td><code>order</code></td>
                        <td><?php esc_html_e('Sort order', 'certifications-plugin'); ?></td>
                        <td><code>ASC</code></td>
                        <td><code>ASC</code>, <code>DESC</code></td>
                        <td><code>order="DESC"</code></td>
                    </tr>
                    <tr>
                        <td><code>orderby</code></td>
                        <td><?php esc_html_e('Field to order by', 'certifications-plugin'); ?></td>
                        <td><code>menu_order</code></td>
                        <td><code>date</code>, <code>title</code>, <code>menu_order</code>, <code>rand</code>, <code>meta_value</code></td>
                        <td><code>orderby="date"</code><br><code>orderby="rand"</code></td>
                    </tr>
                    <tr>
                        <td><code>meta_key</code></td>
                        <td><?php esc_html_e('Custom field to order by (when orderby is meta_value)', 'certifications-plugin'); ?></td>
                        <td><code>''</code></td>
                        <td><?php esc_html_e('any ACF field name', 'certifications-plugin'); ?></td>
                        <td><code>orderby="meta_value" meta_key="_certification_display_order"</code></td>
                    </tr>
                </table>

                <h3><?php esc_html_e('Filtering Parameters', 'certifications-plugin'); ?></h3>
                <table>
                    <tr>
                        <th><?php esc_html_e('Parameter', 'certifications-plugin'); ?></th>
                        <th><?php esc_html_e('Description', 'certifications-plugin'); ?></th>
                        <th><?php esc_html_e('Default', 'certifications-plugin'); ?></th>
                        <th><?php esc_html_e('Options', 'certifications-plugin'); ?></th>
                        <th><?php esc_html_e('Examples', 'certifications-plugin'); ?></th>
                    </tr>
                    <tr>
                        <td><code>category</code></td>
                        <td><?php esc_html_e('Filter by category', 'certifications-plugin'); ?></td>
                        <td><code>''</code></td>
                        <td><?php esc_html_e('category slug or ID', 'certifications-plugin'); ?></td>
                        <td><code>category="featured"</code><br><code>category="5"</code></td>
                    </tr>
                    <tr>
                        <td><code>tag</code></td>
                        <td><?php esc_html_e('Filter by tag', 'certifications-plugin'); ?></td>
                        <td><code>''</code></td>
                        <td><?php esc_html_e('tag slug or ID', 'certifications-plugin'); ?></td>
                        <td><code>tag="popular"</code><br><code>tag="8"</code></td>
                    </tr>
                    <tr>
                        <td><code>include</code></td>
                        <td><?php esc_html_e('Include only specific certifications', 'certifications-plugin'); ?></td>
                        <td><code>''</code></td>
                        <td><?php esc_html_e('IDs separated by commas', 'certifications-plugin'); ?></td>
                        <td><code>include="42,51,90"</code></td>
                    </tr>
                    <tr>
                        <td><code>exclude</code></td>
                        <td><?php esc_html_e('Exclude specific certifications', 'certifications-plugin'); ?></td>
                        <td><code>''</code></td>
                        <td><?php esc_html_e('IDs separated by commas', 'certifications-plugin'); ?></td>
                        <td><code>exclude="42,51,90"</code></td>
                    </tr>
                </table>

                <h3><?php esc_html_e('Content Parameters', 'certifications-plugin'); ?></h3>
                <table>
                    <tr>
                        <th><?php esc_html_e('Parameter', 'certifications-plugin'); ?></th>
                        <th><?php esc_html_e('Description', 'certifications-plugin'); ?></th>
                        <th><?php esc_html_e('Default', 'certifications-plugin'); ?></th>
                        <th><?php esc_html_e('Options', 'certifications-plugin'); ?></th>
                        <th><?php esc_html_e('Examples', 'certifications-plugin'); ?></th>
                    </tr>
                    <tr>
                        <td><code>show_image</code></td>
                        <td><?php esc_html_e('Whether to show the certification image', 'certifications-plugin'); ?></td>
                        <td><code>true</code></td>
                        <td><code>true</code>, <code>false</code></td>
                        <td><code>show_image="false"</code></td>
                    </tr>
                    <tr>
                        <td><code>image_size</code></td>
                        <td><?php esc_html_e('Size of the image', 'certifications-plugin'); ?></td>
                        <td><code>medium</code></td>
                        <td><code>thumbnail</code>, <code>medium</code>, <code>large</code>, <code>full</code></td>
                        <td><code>image_size="thumbnail"</code><br><code>image_size="large"</code></td>
                    </tr>
                    <tr>
                        <td><code>show_title</code></td>
                        <td><?php esc_html_e('Whether to show the certification title', 'certifications-plugin'); ?></td>
                        <td><code>true</code></td>
                        <td><code>true</code>, <code>false</code></td>
                        <td><code>show_title="false"</code></td>
                    </tr>
                    <tr>
                        <td><code>excerpt_length</code></td>
                        <td><?php esc_html_e('Length of excerpt in words', 'certifications-plugin'); ?></td>
                        <td><code>25</code></td>
                        <td><?php esc_html_e('any number', 'certifications-plugin'); ?></td>
                        <td><code>excerpt_length="15"</code><br><code>excerpt_length="50"</code></td>
                    </tr>
                    <tr>
                        <td><code>link_target</code></td>
                        <td><?php esc_html_e('Where to open links', 'certifications-plugin'); ?></td>
                        <td><code>_self</code></td>
                        <td><code>_self</code>, <code>_blank</code></td>
                        <td><code>link_target="_blank"</code></td>
                    </tr>
                    <tr>
                        <td><code>show_button</code></td>
                        <td><?php esc_html_e('Display "Learn More" button', 'certifications-plugin'); ?></td>
                        <td><code>true</code></td>
                        <td><code>true</code>, <code>false</code></td>
                        <td><code>show_button="false"</code></td>
                    </tr>
                    <tr>
                        <td><code>button_text</code></td>
                        <td><?php esc_html_e('Custom text for button', 'certifications-plugin'); ?></td>
                        <td><code>Learn More</code></td>
                        <td><?php esc_html_e('any text', 'certifications-plugin'); ?></td>
                        <td><code>button_text="View Details"</code><br><code>button_text="See Certification"</code></td>
                    </tr>
                </table>

                <h3><?php esc_html_e('Advanced Parameters', 'certifications-plugin'); ?></h3>
                <table>
                    <tr>
                        <th><?php esc_html_e('Parameter', 'certifications-plugin'); ?></th>
                        <th><?php esc_html_e('Description', 'certifications-plugin'); ?></th>
                        <th><?php esc_html_e('Default', 'certifications-plugin'); ?></th>
                        <th><?php esc_html_e('Options', 'certifications-plugin'); ?></th>
                        <th><?php esc_html_e('Examples', 'certifications-plugin'); ?></th>
                    </tr>
                    <tr>
                        <td><code>offset</code></td>
                        <td><?php esc_html_e('Number of posts to skip', 'certifications-plugin'); ?></td>
                        <td><code>0</code></td>
                        <td><?php esc_html_e('any number', 'certifications-plugin'); ?></td>
                        <td><code>offset="3"</code><br><code>offset="10"</code></td>
                    </tr>
                    <tr>
                        <td><code>cache</code></td>
                        <td><?php esc_html_e('Whether to cache results', 'certifications-plugin'); ?></td>
                        <td><code>true</code></td>
                        <td><code>true</code>, <code>false</code></td>
                        <td><code>cache="false"</code></td>
                    </tr>
                    <tr>
                        <td><code>class</code></td>
                        <td><?php esc_html_e('Additional CSS classes', 'certifications-plugin'); ?></td>
                        <td><code>''</code></td>
                        <td><?php esc_html_e('any class names', 'certifications-plugin'); ?></td>
                        <td><code>class="featured-certifications"</code><br><code>class="blue-theme highlighted"</code></td>
                    </tr>
                </table>

                <h3><?php esc_html_e('Example Shortcodes', 'certifications-plugin'); ?></h3>
                <p><?php esc_html_e('Basic grid with 3 columns:', 'certifications-plugin'); ?></p>
                <div class="certifications-shortcode-example">
                    [certifications columns="3" count="6"]
                </div>

                <p><?php esc_html_e('List display with pagination:', 'certifications-plugin'); ?></p>
                <div class="certifications-shortcode-example">
                    [certifications display_type="list" pagination="true" count="10"]
                </div>

                <p><?php esc_html_e('Certifications from a specific category, randomly ordered:', 'certifications-plugin'); ?></p>
                <div class="certifications-shortcode-example">
                    [certifications category="featured-certifications" orderby="rand"]
                </div>
            </div>

            <!-- Single Certification Shortcode Section -->
            <div class="certifications-help-section">
                <h2><?php esc_html_e('Shortcode: [certification]', 'certifications-plugin'); ?></h2>
                <p><?php esc_html_e('This shortcode displays a single Certification with customizable elements.', 'certifications-plugin'); ?></p>

                <h3><?php esc_html_e('Basic Usage', 'certifications-plugin'); ?></h3>
                <p><?php esc_html_e('You must specify the ID of the certification to display:', 'certifications-plugin'); ?></p>
                <div class="certifications-shortcode-example">
                    [certification id="42"]
                </div>

                <h3><?php esc_html_e('Available Parameters', 'certifications-plugin'); ?></h3>
                <table>
                    <tr>
                        <th><?php esc_html_e('Parameter', 'certifications-plugin'); ?></th>
                        <th><?php esc_html_e('Description', 'certifications-plugin'); ?></th>
                        <th><?php esc_html_e('Default', 'certifications-plugin'); ?></th>
                        <th><?php esc_html_e('Options', 'certifications-plugin'); ?></th>
                        <th><?php esc_html_e('Examples', 'certifications-plugin'); ?></th>
                    </tr>
                    <tr>
                        <td><code>id</code></td>
                        <td><?php esc_html_e('Certification ID (required)', 'certifications-plugin'); ?></td>
                        <td><code>0</code></td>
                        <td><?php esc_html_e('any valid post ID', 'certifications-plugin'); ?></td>
                        <td><code>id="42"</code><br><code>id="156"</code></td>
                    </tr>
                    <tr>
                        <td><code>show_image</code></td>
                        <td><?php esc_html_e('Whether to show the certification image', 'certifications-plugin'); ?></td>
                        <td><code>true</code></td>
                        <td><code>true</code>, <code>false</code></td>
                        <td><code>show_image="false"</code></td>
                    </tr>
                    <tr>
                        <td><code>show_buttons</code></td>
                        <td><?php esc_html_e('Display action buttons', 'certifications-plugin'); ?></td>
                        <td><code>true</code></td>
                        <td><code>true</code>, <code>false</code></td>
                        <td><code>show_buttons="false"</code></td>
                    </tr>
                    <tr>
                        <td><code>show_sections</code></td>
                        <td><?php esc_html_e('Display the content sections', 'certifications-plugin'); ?></td>
                        <td><code>true</code></td>
                        <td><code>true</code>, <code>false</code></td>
                        <td><code>show_sections="false"</code></td>
                    </tr>
                    <tr>
                        <td><code>sections</code></td>
                        <td><?php esc_html_e('Which sections to display (comma-separated)', 'certifications-plugin'); ?></td>
                        <td><code>all</code></td>
                        <td><code>all</code>, <code>intro</code>, <code>prepare</code>, <code>get_certified</code>, <code>after_exam</code>, <code>documents</code></td>
                        <td><code>sections="intro,prepare"</code><br><code>sections="get_certified,documents"</code></td>
                    </tr>
                    <tr>
                        <td><code>class</code></td>
                        <td><?php esc_html_e('Additional CSS classes', 'certifications-plugin'); ?></td>
                        <td><code>''</code></td>
                        <td><?php esc_html_e('any class names', 'certifications-plugin'); ?></td>
                        <td><code>class="featured-certification"</code><br><code>class="compact-layout special"</code></td>
                    </tr>
                </table>

                <h3><?php esc_html_e('Example Shortcodes', 'certifications-plugin'); ?></h3>
                <p><?php esc_html_e('Display a certification with ID 42, hiding the buttons:', 'certifications-plugin'); ?></p>
                <div class="certifications-shortcode-example">
                    [certification id="42" show_buttons="false"]
                </div>

                <p><?php esc_html_e('Display a simplified certification profile:', 'certifications-plugin'); ?></p>
                <div class="certifications-shortcode-example">
                    [certification id="42" sections="intro,prepare"]
                </div>

                <p><?php esc_html_e('Add a custom class to the certification for styling:', 'certifications-plugin'); ?></p>
                <div class="certifications-shortcode-example">
                    [certification id="42" class="featured-certification special-layout"]
                </div>
            </div>

            <!-- Certification Images Shortcode Section -->
            <div class="certifications-help-section">
                <h2><?php esc_html_e('Shortcode: [certification_images]', 'certifications-plugin'); ?></h2>
                <p><?php esc_html_e('This shortcode displays featured images of all certifications in a grid layout, with each image linking to its corresponding certification page.', 'certifications-plugin'); ?></p>

                <h3><?php esc_html_e('Basic Usage', 'certifications-plugin'); ?></h3>
                <div class="certifications-shortcode-example">
                    [certification_images]
                </div>

                <h3><?php esc_html_e('Available Parameters', 'certifications-plugin'); ?></h3>
                <table>
                    <tr>
                        <th><?php esc_html_e('Parameter', 'certifications-plugin'); ?></th>
                        <th><?php esc_html_e('Description', 'certifications-plugin'); ?></th>
                        <th><?php esc_html_e('Default', 'certifications-plugin'); ?></th>
                        <th><?php esc_html_e('Options', 'certifications-plugin'); ?></th>
                        <th><?php esc_html_e('Examples', 'certifications-plugin'); ?></th>
                    </tr>
                    <tr>
                        <td><code>cache</code></td>
                        <td><?php esc_html_e('Whether to cache results', 'certifications-plugin'); ?></td>
                        <td><code>true</code></td>
                        <td><code>true</code>, <code>false</code></td>
                        <td><code>cache="false"</code></td>
                    </tr>
                </table>

                <h3><?php esc_html_e('Notes', 'certifications-plugin'); ?></h3>
                <ul>
                    <li><?php esc_html_e('Images are displayed in rows with 4 images per row.', 'certifications-plugin'); ?></li>
                    <li><?php esc_html_e('Certifications are sorted by their menu order (page attributes) in ascending order.', 'certifications-plugin'); ?></li>
                    <li><?php esc_html_e('Each image automatically links to its corresponding certification page.', 'certifications-plugin'); ?></li>
                    <li><?php esc_html_e('The shortcode is responsive and will adjust to different screen sizes.', 'certifications-plugin'); ?></li>
                </ul>
            </div>

            <!-- Finding IDs Section -->
            <div class="certifications-help-section">
                <h2><?php esc_html_e('Finding Certification IDs', 'certifications-plugin'); ?></h2>
                <p><?php esc_html_e('To find the ID of a Certification:', 'certifications-plugin'); ?></p>
                <ol>
                    <li><?php esc_html_e('Go to Certifications in the admin menu', 'certifications-plugin'); ?></li>
                    <li><?php esc_html_e('Hover over a certification\'s title', 'certifications-plugin'); ?></li>
                    <li><?php esc_html_e('Look at the URL that appears in your browser\'s status bar', 'certifications-plugin'); ?></li>
                    <li><?php esc_html_e('The ID is the number after "post=", e.g., post=42', 'certifications-plugin'); ?></li>
                </ol>
                <p><?php esc_html_e('Alternatively, open a certification for editing and the ID will be visible in the URL.', 'certifications-plugin'); ?></p>
            </div>

            <!-- Need Help Section -->
            <div class="certifications-help-section">
                <h2><?php esc_html_e('Need More Help?', 'certifications-plugin'); ?></h2>
                <p><?php esc_html_e('If you need further assistance:', 'certifications-plugin'); ?></p>
                <ul>
                    <li><?php esc_html_e('Contact your website administrator', 'certifications-plugin'); ?></li>
                    <li><?php esc_html_e('Refer to the WordPress documentation for general shortcode usage', 'certifications-plugin'); ?></li>
                </ul>
            </div>
        </div>
		<?php
	}
}
