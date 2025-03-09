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
	}

	/**
	 * Add help/documentation page
	 */
	public function add_help_page() {
		$help_page = add_submenu_page(
			'edit.php?post_type=certification',  // Parent menu slug
			__( 'Certifications Help', 'certifications-plugin' ), // Page title
			__( 'How to Use', 'certifications-plugin' ),          // Menu title
			'edit_posts',                                         // Capability
			'certifications-help',                                // Menu slug
			array( $this, 'render_help_page' )                    // Callback function
		);

		// Add action to include CSS only on help page
		add_action( 'admin_head-' . $help_page, array( $this, 'help_page_css' ) );
	}

	/**
	 * Output CSS for help page directly in the head
	 */
	public function help_page_css() {
		?>
		<style type="text/css">
            /* Certifications Help Documentation Styles */
            .certifications-admin-help {
                max-width: 1200px; /* Increased from 950px to provide more room */
                margin: 25px 0;
            }

            .certifications-admin-help {
                max-width: 950px;
                margin: 25px 0;
            }

            .certifications-admin-card {
                background: #fff;
                border: 1px solid #ccd0d4;
                border-radius: 4px;
                padding: 20px 25px;
                margin-top: 20px;
                box-shadow: 0 1px 1px rgba(0,0,0,0.04);
            }

            .certifications-admin-card h2 {
                font-size: 22px;
                font-weight: 600;
                margin-top: 0;
                padding-top: 0;
                color: #23282d;
            }

            .certifications-admin-card h3 {
                font-size: 18px;
                font-weight: 600;
                margin-top: 30px;
                color: #23282d;
                border-bottom: 1px solid #eee;
                padding-bottom: 10px;
            }

            .certifications-admin-card h4 {
                font-size: 16px;
                font-weight: 600;
                margin-top: 25px;
                color: #23282d;
            }

            .certifications-admin-card p {
                font-size: 14px;
                line-height: 1.6;
                margin: 15px 0;
            }

            .certifications-admin-code-block {
                background: #f5f5f5;
                padding: 15px;
                border-left: 4px solid #2271b1;
                font-family: monospace;
                margin: 20px 0;
                overflow-x: auto;
            }

            .certifications-admin-code-block code {
                background: transparent;
                padding: 0;
                font-size: 14px;
                white-space: pre;
            }

            .certifications-admin-table {
                border-collapse: collapse;
                margin: 20px 0;
                width: 100%;
            }

            .certifications-admin-table th {
                background-color: #f1f1f1;
                font-weight: 600;
                text-align: left;
                padding: 10px;
                border: 1px solid #ccd0d4;
            }

            .certifications-admin-table td {
                padding: 10px;
                border: 1px solid #ccd0d4;
                vertical-align: top;
            }

            .certifications-admin-table tr:nth-child(even) {
                background-color: #f9f9f9;
            }

            .certifications-admin-table code {
                background: rgba(0,0,0,0.05);
                padding: 3px 5px;
                border-radius: 3px;
                font-size: 13px;
            }

            /* Responsive styles */
            @media screen and (max-width: 782px) {
                .certifications-admin-card {
                    padding: 15px;
                }

                .certifications-admin-table {
                    display: block;
                    overflow-x: auto;
                }

                .certifications-admin-code-block {
                    padding: 10px;
                }
            }
		</style>
		<?php
	}

	/**
	 * Render the help page content
	 */
	public function render_help_page() {
		?>
		<div class="wrap certifications-admin-help">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

			<div class="certifications-admin-card">
				<h2><?php _e( 'How to Use Certifications Shortcode', 'certifications-plugin' ); ?></h2>
				<p><?php _e( 'You can display certifications on any page or post using the shortcode below:', 'certifications-plugin' ); ?></p>

				<div class="certifications-admin-code-block">
					<code>[certifications]</code>
				</div>

				<h3><?php _e( 'Available Options', 'certifications-plugin' ); ?></h3>
				<table class="certifications-admin-table widefat">
					<thead>
					<tr>
						<th><?php _e( 'Parameter', 'certifications-plugin' ); ?></th>
						<th><?php _e( 'Description', 'certifications-plugin' ); ?></th>
						<th><?php _e( 'Default', 'certifications-plugin' ); ?></th>
						<th><?php _e( 'Example', 'certifications-plugin' ); ?></th>
					</tr>
					</thead>
					<tbody>
					<tr>
						<td><code>display_type</code></td>
						<td><?php _e( 'Display as grid or list', 'certifications-plugin' ); ?></td>
						<td>grid</td>
						<td><code>[certifications display_type="list"]</code></td>
					</tr>
					<tr>
						<td><code>count</code></td>
						<td><?php _e( 'Number of certifications to display. Use -1 for all.', 'certifications-plugin' ); ?></td>
						<td>-1</td>
						<td><code>[certifications count="4"]</code></td>
					</tr>
					<tr>
						<td><code>columns</code></td>
						<td><?php _e( 'Number of columns in the grid display.', 'certifications-plugin' ); ?></td>
						<td>4</td>
						<td><code>[certifications columns="3"]</code></td>
					</tr>
					<tr>
						<td><code>pagination</code></td>
						<td><?php _e( 'Whether to show pagination controls', 'certifications-plugin' ); ?></td>
						<td>false</td>
						<td><code>[certifications pagination="true"]</code></td>
					</tr>
					<tr>
						<td><code>category</code></td>
						<td><?php _e( 'Filter by category slug. Separate multiple with commas.', 'certifications-plugin' ); ?></td>
						<td></td>
						<td><code>[certifications category="featured,popular"]</code></td>
					</tr>
					<tr>
						<td><code>order</code></td>
						<td><?php _e( 'Order of certifications (ASC or DESC).', 'certifications-plugin' ); ?></td>
						<td>ASC</td>
						<td><code>[certifications order="DESC"]</code></td>
					</tr>
					<tr>
						<td><code>show_image</code></td>
						<td><?php _e( 'Whether to display the featured image', 'certifications-plugin' ); ?></td>
						<td>true</td>
						<td><code>[certifications show_image="false"]</code></td>
					</tr>
					<tr>
						<td><code>button_text</code></td>
						<td><?php _e( 'Custom text for the button', 'certifications-plugin' ); ?></td>
						<td>Learn More</td>
						<td><code>[certifications button_text="View Details"]</code></td>
					</tr>
					</tbody>
				</table>

				<h3><?php _e( 'Example', 'certifications-plugin' ); ?></h3>
				<p><?php _e( 'To display 3 certifications from the "featured" category in 2 columns:', 'certifications-plugin' ); ?></p>
				<div class="certifications-admin-code-block">
					<code>[certifications count="3" columns="2" category="featured"]</code>
				</div>

				<h3><?php _e( 'Single Certification Display', 'certifications-plugin' ); ?></h3>
				<p><?php _e( 'You can also display a single certification using the following shortcode:', 'certifications-plugin' ); ?></p>
				<div class="certifications-admin-code-block">
					<code>[certification id="123"]</code>
				</div>
				<p><?php _e( 'Where "123" is the ID of the certification you want to display.', 'certifications-plugin' ); ?></p>

				<h4><?php _e( 'Single Certification Parameters', 'certifications-plugin' ); ?></h4>
				<table class="certifications-admin-table widefat">
					<thead>
					<tr>
						<th><?php _e( 'Parameter', 'certifications-plugin' ); ?></th>
						<th><?php _e( 'Description', 'certifications-plugin' ); ?></th>
						<th><?php _e( 'Default', 'certifications-plugin' ); ?></th>
						<th><?php _e( 'Example', 'certifications-plugin' ); ?></th>
					</tr>
					</thead>
					<tbody>
					<tr>
						<td><code>id</code></td>
						<td><?php _e( 'The certification ID (required)', 'certifications-plugin' ); ?></td>
						<td>0</td>
						<td><code>[certification id="123"]</code></td>
					</tr>
					<tr>
						<td><code>show_image</code></td>
						<td><?php _e( 'Whether to show the featured image', 'certifications-plugin' ); ?></td>
						<td>true</td>
						<td><code>[certification id="123" show_image="false"]</code></td>
					</tr>
					<tr>
						<td><code>show_buttons</code></td>
						<td><?php _e( 'Whether to show action buttons', 'certifications-plugin' ); ?></td>
						<td>true</td>
						<td><code>[certification id="123" show_buttons="false"]</code></td>
					</tr>
					<tr>
						<td><code>show_sections</code></td>
						<td><?php _e( 'Whether to show content sections', 'certifications-plugin' ); ?></td>
						<td>true</td>
						<td><code>[certification id="123" show_sections="false"]</code></td>
					</tr>
					<tr>
						<td><code>sections</code></td>
						<td><?php _e( 'Which sections to display (comma-separated)', 'certifications-plugin' ); ?></td>
						<td>all</td>
						<td><code>[certification id="123" sections="intro,prepare"]</code></td>
					</tr>
					</tbody>
				</table>
			</div>
		</div>
		<?php
	}
}