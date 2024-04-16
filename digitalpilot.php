<?php
/*
 * Plugin Name: DigitalPilot
 * Plugin URI: https://wordpress.org/plugins/digitalpilot/
 * Description: DigitalPilot
 * Text Domain: digitalpilot
 * Domain Path: /languages
 * Version: 1.0.0
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Author: DigitalPilot
 * Author URI: https://digitalpilot.app/
 * Contributors: valeriutihai
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */
defined( 'ABSPATH' ) || die( 'Plugin file cannot be accessed directly.' );
// No direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Main Class - DigitalPilot
 *
 * @since 1.0.0
 */
class DigitalPilot {


	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {

		// Load language file.
		add_action( 'plugins_loaded', array( &$this, 'digitalpilot_load_textdomain' ), 2 );

		// Add Support Link.
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( &$this, 'digitalpilot_links' ), 1 );

		// Add Admin menu.
		add_action( 'admin_menu', array( $this, 'digitalpilot_admin_menu' ), 4 );

		// Load settings.
		add_action( 'plugins_loaded', array( &$this, 'digitalpilot_settings' ), 1 );

		// Setting Initialization.
		add_action( 'admin_init', array( &$this, 'digitalpilot_settings_init' ), 1 );

		// Load external JavaScript.
		add_action( 'wp_enqueue_scripts', array( &$this, 'digitalpilot_load_js' ), 1 );

		// Load Admin JavaScript and CSS.
		add_action( 'admin_enqueue_scripts', array( &$this, 'digitalpilot_enqueue_admin' ), 1 );
	}


	/**
	 * Load language file
	 *
	 * This will load the MO file for the current locale.
	 * The translation file must be named digitalpilot-$locale.mo.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function digitalpilot_load_textdomain() {
		load_plugin_textdomain( 'digitalpilot', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}


	/**
	 * Add settings links to plugin page
	 *
	 * @since 1.0.0
	 * @access  public
	 *
	 * @param array $links An array of existing plugin action links.
	 * @return array The modified array of plugin action links.
	 */
	public function digitalpilot_links( $links ) {
		$links[] = '<a href="https://digitalpilot.app/" target="_blank">' . __( 'Get Tag', 'digitalpilot' ) . '</a>';
		$links[] = '<a href="https://wordpress.org/support/plugin/digitalpilot/" target="_blank">' . __( 'Support', 'digitalpilot' ) . '</a>';
		return $links;
	}


	/**
	 * Add plugin menu.
	 *
	 * This function adds a submenu page under the 'Settings' menu in the WordPress admin area
	 * for the DigitalPilot plugin. This menu allows users with the 'manage_options' capability
	 * to access the settings page of the DigitalPilot plugin.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function digitalpilot_admin_menu() {
		add_submenu_page(
			'options-general.php',
			__( 'DigitalPilot', 'digitalpilot' ),
			__( 'DigitalPilot', 'digitalpilot' ),
			'manage_options',
			'digitalpilot-settings',
			array( $this, 'digitalpilot_options_page' ),
			8
		);
	}


	/**
	 * Retrieve plugin settings.
	 *
	 * This function returns an array containing all the settings for the DigitalPilot plugin.
	 * Each setting is defined as an array containing various parameters such as ID, title, callback, page, section, and arguments.
	 * These settings are used to generate the settings page in the WordPress admin area.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array An array containing all the settings for the DigitalPilot plugin.
	 */
	public function digitalpilot_settings() {
		return array(
			array(
				'settings_type' => 'section',
				'id'            => 'digitalpilot_section_settings_general',
				'title'         => __( 'General Settings', 'digitalpilot' ),
				'callback'      => 'digitalpilot_description_section_callback',
				'page'          => 'digitalpilot_page',
			),
			array(
				'settings_type' => 'field',
				'id'            => 'digitalpilot_tag',
				'title'         => __( 'DigitalPilot tracking ID', 'digitalpilot' ),
				'callback'      => 'digitalpilot_settings_field_render',
				'page'          => 'digitalpilot_page',
				'section'       => 'digitalpilot_section_settings_general',
				'args'          => array(
					'id'          => 'digitalpilot_tag',
					'type'        => 'text',
					'class'       => 'css_digitalpilot_tag',
					'name'        => 'digitalpilot_tag',
					'value'       => 'digitalpilot_tag',
					'label_for'   => '',
					'description' => __( 'Add DigitalPilot tracking ID. Where can I find <a href="https://www.digitalpilot.app/" target="_blank">my tracking ID?</a>', 'digitalpilot' ),
				),
			),
		);
	}



	/**
	 * Get the DigitalPilot tracking ID.
	 *
	 * This function retrieves the DigitalPilot tracking ID saved in the plugin settings.
	 * It first retrieves the saved options from the WordPress database using the 'digitalpilot_settings' option name.
	 * If a tracking ID is found in the saved options, it is sanitized and returned.
	 * If no tracking ID is found or if it's empty, the function returns false.
	 *
	 * @since 1.0.2
	 * @access public
	 * @return string|false The DigitalPilot tracking ID, or false if not found.
	 */
	public function digitalpilot_tag_get() {
		$saved_options = get_option( 'digitalpilot_settings' );
		return ( ! empty( $saved_options['digitalpilot_tag'] ) ) ? sanitize_text_field( $saved_options['digitalpilot_tag'] ) : false;
	}


	/**
	 * Load JavaScript for DigitalPilot tag.
	 *
	 * This function adds JavaScript code to the WordPress site for loading the DigitalPilot tag.
	 * It first retrieves the DigitalPilot tracking ID using the digitalpilot_tag_get() method.
	 * If a tracking ID is found, it enqueues the DigitalPilot tag script with the ID as a parameter.
	 * The script is added asynchronously for better performance.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function digitalpilot_load_js() {
		// Get the DigitalPilot tracking ID.
		$digitalpilot_tag = $this->digitalpilot_tag_get();

		// If a tracking ID is found, enqueue the DigitalPilot tag script.
		if ( $digitalpilot_tag ) {
			wp_enqueue_script( 'dp_tag', 'https://api.digitalpilot.app/tag.js?id=' . $digitalpilot_tag, array(), null, false );
			wp_script_add_data( 'dp_tag', 'async', true );
		}
	}


	/**
	 * Enqueue JavaScript and CSS files for the DigitalPilot plugin in wp-admin.
	 *
	 * This function is responsible for adding necessary JavaScript and CSS files to the WordPress
	 * admin area specifically for the DigitalPilot plugin settings page. It checks the current admin
	 * page hook to ensure that the files are only loaded on the DigitalPilot settings page.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $hook The current admin page hook.
	 */
	public function digitalpilot_enqueue_admin( $hook ) {
		// Check if the current admin page is the DigitalPilot settings page.
		if ( 'settings_page_digitalpilot-settings' === $hook ) {
			// Enqueue the JavaScript file for the DigitalPilot admin functionality.
			wp_enqueue_script( 'analyticstracker-js-admin', plugins_url( '/css_js/digitalpilot-admin.js', __FILE__ ), array( 'jquery' ), true, '1.0.0' );

			// Register and enqueue the CSS file for styling the DigitalPilot admin page.
			wp_register_style( 'digitalpilot-css-admin', plugins_url( '/css_js/digitalpilot-admin.css', __FILE__ ), array(), '1.0.0', 'all' );

			wp_enqueue_style( 'digitalpilot-css-admin' );
		}
	}



	/**
	 * Callback function for the description section in the DigitalPilot settings page.
	 *
	 * This function serves as the callback for displaying a description section on the DigitalPilot settings page.
	 * It is used to provide additional information or instructions to users regarding a specific section of settings.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function digitalpilot_description_section_callback() {
		// Output the description section content.
		echo '<p class="digitalpilot-description">' . esc_html( __( 'DigitalPilot lets you instantly reveal valuable company information about the visitors to your website - without requiring intrusive form fills. Leverage these insights to prioritize follow-ups and turn high-value strangers into qualified leads.', 'digitalpilot' ) ) . '</p>';
		echo '<p class="digitalpilot-description">' . esc_html( __( 'With our easy-to-integrate code snippet, you’ll unlock a goldmine of actionable data to supercharge your lead generation, equip sales teams with visitor intel to build rapport, and enable marketing to craft targeted campaigns.', 'digitalpilot' ) ) . '</p>';
		echo '<p class="digitalpilot-description">' . esc_html( __( 'Stop marketing in the dark. Join hundreds of businesses who use DigitalPilot to illuminate website traffic and transform stranger danger into sales opportunities.', 'digitalpilot' ) ) . '</p>';
	}



	/**
	 * Initialize DigitalPilot settings.
	 *
	 * This function is responsible for initializing the settings for the DigitalPilot plugin.
	 * It registers the settings and adds sections and fields to the settings page based on the configuration
	 * provided by the digitalpilot_settings() method.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function digitalpilot_settings_init() {
		// Register the setting for the DigitalPilot page.
		register_setting( 'digitalpilot_page', 'digitalpilot_settings', array( $this, 'digitalpilot_validate_settings' ) );

		// Loop through each setting and add sections and fields accordingly.
		foreach ( $this->digitalpilot_settings() as $setting ) {
			// If the setting type is a section, add a settings section.
			if ( 'section' === $setting['settings_type'] ) {
				add_settings_section(
					$setting['id'],
					$setting['title'],
					array( $this, $setting['callback'] ),
					$setting['page']
				);
			}
			// If the setting type is a field, add a settings field.
			if ( 'field' === $setting['settings_type'] ) {
				add_settings_field(
					$setting['id'],
					$setting['title'],
					array( $this, $setting['callback'] ),
					$setting['page'],
					$setting['section'],
					$setting['args']
				);
			}
		}
	}

	/**
	 * Validate settings before saving.
	 *
	 * This function is responsible for validating settings before saving them to the database.
	 * It checks if the input data is valid and returns sanitized data or an error message accordingly.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $input An array containing the input data to be validated.
	 * @return array|WP_Error The sanitized input data or a WP_Error object if validation fails.
	 */
	public function digitalpilot_validate_settings( $input ) {

		// Check if required fields are not empty.
		if ( empty( $input['digitalpilot_tag'] ) ) {
			add_settings_error( 'digitalpilot_settings', 'digitalpilot_tag_empty', __( 'Please enter a DigitalPilot tracking ID.', 'digitalpilot' ), 'error' );
			return get_option( 'digitalpilot_settings' ); // Revert to the previous settings.
		}

		// Check if the input matches the specified format.
		if ( ! preg_match( '/^[A-Za-z0-9]{7}$/', $input['digitalpilot_tag'] ) ) {
			add_settings_error( 'digitalpilot_settings', 'digitalpilot_tag_invalid_format', __( 'Invalid DigitalPilot tracking ID format. It should be 7 characters long and contain only letters (uppercase or lowercase) and numbers.', 'digitalpilot' ), 'error' );
			return get_option( 'digitalpilot_settings' ); // Revert to the previous settings.
		}

		// Sanitize and return the input data.
		$input['digitalpilot_tag'] = sanitize_text_field( $input['digitalpilot_tag'] );
		return $input;
	}


	/**
	 * Render a settings field for the DigitalPilot plugin.
	 *
	 * This function generates HTML markup for a settings field based on the provided arguments.
	 * It supports various input types such as text, checkboxes, and selects.
	 * It also checks for empty values and displays an error message.
	 *
	 * @since 1.0.0
	 * @access public
	 * @param array $args An array containing the configuration options for the field.
	 */
	public function digitalpilot_settings_field_render( array $args = array() ) {
		// Retrieve saved options from the database.
		$saved_options = get_option( 'digitalpilot_settings' );

		// Define attributes for the field.
		$atts = array(
			'id'          => $args['id'],
			'type'        => isset( $args['type'] ) ? $args['type'] : 'text',
			'class'       => $args['class'],
			'name'        => 'digitalpilot_settings[' . $args['name'] . ']',
			'value'       => array_key_exists( 'default', $args ) ? $args['default'] : null,
			'label_for'   => array_key_exists( 'label_for', $args ) ? $args['label_for'] : false,
			'description' => array_key_exists( 'description', $args ) ? $args['description'] : false,
		);

		// Populate field value if saved options exist.
		if ( isset( $args['id'] ) && isset( $saved_options[ $args['id'] ] ) ) {
			$val           = $saved_options[ $args['id'] ];
			$atts['value'] = $val;
		}

		// Render input type Text.
		if ( 'text' === $atts['type'] ) {
			$input_type  = esc_attr( $atts['type'] );
			$input_class = esc_attr( $atts['class'] );
			$input_id    = esc_attr( $atts['id'] );
			$input_name  = esc_attr( $atts['name'] );
			$input_value = esc_attr( $atts['value'] );

			// Render the input field.
			printf( '<input type="%1$s" class="%2$s" id="%3$s" name="%4$s" value="%5$s"/>', $input_type, $input_class, $input_id, $input_name, $input_value );

			// Render the description if provided.
			if ( $atts['description'] ) {
				$description = wp_kses(
					$atts['description'],
					array(
						'a' => array(
							'href'   => array(),
							'target' => array(),
						),
					)
				);
				// Description.
				printf( '<p class="description">%1$s</p>', $description );
			}
		}
	}


	/**
	 * Generate the settings page for the DigitalPilot plugin.
	 *
	 * This function outputs the HTML markup for the settings page, including the form
	 * for submitting settings, displaying any errors, and rendering the settings sections.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function digitalpilot_options_page() {
		?>
		<div class="wrap">
			<form action='options.php' method='post'>
				<h1><?php esc_html_e( 'DigitalPilot Settings', 'digitalpilot' ); ?></h1>
				<?php
					// Output the settings fields.
					settings_fields( 'digitalpilot_page' );
					do_settings_sections( 'digitalpilot_page' );

					// Output the submit button.
					submit_button();
				?>
			</form>
		</div>
		<?php
	}
}
// Instantiate the main class.
new DigitalPilot();
