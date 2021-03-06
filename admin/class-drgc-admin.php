<?php
/**
 * Admin-specific functionality
 *
 * @link       https://www.digitalriver.com
 * @since      1.0.0
 *
 * @package    Digital_River_Global_Commerce
 * @subpackage Digital_River_Global_Commerce/admin
 */

class DRGC_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $drgc
	 */
	private $drgc;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version
	 */
	private $version;

	/**
	 * The plugin name
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string     $plugin_name
	 */
	private $plugin_name = 'digital-river-global-commerce';

	/**
	 * The option name to be used in this plugin
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string     $option_name
	 */
	private $option_name = 'drgc';

	/**
	 * site ID
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string
	 */
	private $drgc_site_id;

	/**
	 * API key
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string
	 */
	private $drgc_api_key;

	/**
	 * API Secret
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string
	 */
	private $drgc_api_secret;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $drgc
	 * @param      string    $version
	 */
	public function __construct( $drgc, $version, $drgc_ajx ) {
		$this->drgc = $drgc;
		$this->version = $version;
		$this->drgc_ajx = $drgc_ajx;
		$this->drgc_site_id = get_option( 'drgc_site_id' );
		$this->drgc_api_key = get_option( 'drgc_api_key' );
		$this->drgc_api_secret = get_option( 'drgc_api_secret' );
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->drgc, DRGC_PLUGIN_URL . 'assets/css/drgc-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		wp_enqueue_script( $this->drgc, DRGC_PLUGIN_URL . 'assets/js/drgc-admin' . $suffix . '.js', array( 'jquery', 'jquery-ui-progressbar' ), $this->version, false );

		// transfer drgc options from PHP to JS
		wp_localize_script( $this->drgc, 'drgc_admin_params',
			array(
				'api_key'               => $this->drgc_api_key,
				'api_secret'            => $this->drgc_api_secret,
				'site_id'               => $this->drgc_site_id,
				'drgc_ajx_instance_id'  => $this->drgc_ajx->instance_id,
				'ajax_url'              => admin_url( 'admin-ajax.php' ),
				'ajax_nonce'            => wp_create_nonce( 'drgc_admin_ajax' ),
			)
		);
	}

	/**
	 * Add settings menu and link it to settings page.
	 *
	 * @since    1.0.0
	 */
	public function add_settings_page() {
		add_submenu_page(
      'edit.php?post_type=dr_product',
			__( 'Settings', 'digital-river-global-commerce' ),
			__( 'Settings', 'digital-river-global-commerce' ),
			'manage_options',
			'digital-river-global-commerce',
			array( $this, 'display_settings_page' ),
			100
		);
	}

	/**
	 * Render settings page.
	 *
	 * @since    1.0.0
	 */
	public function display_settings_page() {
		// Double check user capabilities
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		include_once 'partials/drgc-admin-display.php';
	}

	/**
	 * Register settings fields.
	 *
	 * @since    1.0.0
	 */
	public function register_settings_fields() {

		add_settings_section(
			$this->option_name . '_general',
			__('General', 'digital-river-global-commerce' ),
			array( $this, $this->option_name . '_general_cb' ),
			$this->plugin_name
		);

		add_settings_field(
			$this->option_name . '_site_id',
			__( 'Site ID', 'digital-river-global-commerce' ),
			array( $this, $this->option_name . '_site_id_cb' ),
			$this->plugin_name,
			$this->option_name . '_general',
			array( 'label_for' => $this->option_name . '_site_id' )
		);

		add_settings_field(
			$this->option_name . '_api_key',
			__( 'Commerce API Key', 'digital-river-global-commerce' ),
			array( $this, $this->option_name . '_api_key_cb' ),
			$this->plugin_name,
			$this->option_name . '_general',
			array( 'label_for' => $this->option_name . '_api_key' )
		);

		add_settings_field(
			$this->option_name . '_api_secret',
			__( 'Commerce API Secret', 'digital-river-global-commerce' ),
			array( $this, $this->option_name . '_api_secret_cb' ),
			$this->plugin_name,
			$this->option_name . '_general',
			array( 'label_for' => $this->option_name . '_api_secret' )
		);

		add_settings_field(
			$this->option_name . '_domain',
			__( 'Domain', 'digital-river-global-commerce' ),
			array( $this, $this->option_name . '_domain_cb' ),
			$this->plugin_name,
			$this->option_name . '_general',
			array( 'label_for' => $this->option_name . '_domain' )
		);

		add_settings_field(
			$this->option_name . '_digitalRiver_key',
			__( 'Payment Services API Key', 'digital-river-global-commerce' ),
			array( $this, $this->option_name . '_digitalRiver_key_cb' ),
			$this->plugin_name,
			$this->option_name . '_general',
			array( 'label_for' => $this->option_name . '_digitalRiver_key' )
		);

		add_settings_field(
			$this->option_name . '_big_blue_username',
			__( 'UMS Username', 'digital-river-global-commerce' ),
			array( $this, $this->option_name . '_big_blue_username_cb' ),
			$this->plugin_name,
			$this->option_name . '_general',
			array( 'label_for' => $this->option_name . '_big_blue_username' )
		);

		add_settings_field(
			$this->option_name . '_big_blue_password',
			__( 'UMS Password', 'digital-river-global-commerce' ),
			array( $this, $this->option_name . '_big_blue_password_cb' ),
			$this->plugin_name,
			$this->option_name . '_general',
			array( 'label_for' => $this->option_name . '_big_blue_password' )
		);

		add_settings_field(
			$this->option_name . '_cron_handler',
			__( 'Scheduled Products Import', 'digital-river-global-commerce' ),
			array( $this, $this->option_name . '_cron_handler_cb' ),
			$this->plugin_name,
			$this->option_name . '_general',
			array( 'label_for' => $this->option_name . '_cron_handler' )
    );

		add_settings_section(
			$this->option_name . '_checkout',
			__( 'Checkout', 'digital-river-global-commerce' ),
			array( $this, $this->option_name . '_checkout_cb' ),
			$this->plugin_name
		);

		add_settings_field(
			$this->option_name . '_testOrder_handler',
			__( 'Test Order', 'digital-river-global-commerce' ),
			array( $this, $this->option_name . '_testOrder_handler_cb' ),
			$this->plugin_name,
			$this->option_name . '_checkout',
			array( 'label_for' => $this->option_name . '_testOrder_handler' )
		);

		add_settings_field(
			$this->option_name . '_force_excl_tax_handler',
			__( 'Display As Excl. Tax', 'digital-river-global-commerce' ),
			array( $this, $this->option_name . '_force_excl_tax_handler_cb' ),
			$this->plugin_name,
			$this->option_name . '_checkout',
			array( 'label_for' => $this->option_name . '_force_excl_tax_handler' )
		);

		add_settings_section(
			$this->option_name . '_payment',
			__( 'Payment Buttons', 'digital-river-global-commerce' ),
			array( $this, $this->option_name . '_payment_cb' ),
			$this->plugin_name
		);

		add_settings_field(
			$this->option_name . '_applepay_handler',
			__( 'Apple Pay', 'digital-river-global-commerce' ),
			array( $this, $this->option_name . '_applepay_handler_cb' ),
			$this->plugin_name,
			$this->option_name . '_payment',
			array( 'label_for' => $this->option_name . '_applepay_handler' )
    );
    
    add_settings_field(
      $this->option_name . '_applepay_button_type',
      __( 'Button Type', 'digital-river-global-commerce' ),
      array( $this, $this->option_name . '_applepay_button_type_cb' ),
      $this->plugin_name,
      $this->option_name . '_payment',
      array( 'label_for' => $this->option_name . '_applepay_button_type' )
    );
    
    add_settings_field(
      $this->option_name . '_applepay_button_color',
      __( 'Button Color', 'digital-river-global-commerce' ),
      array( $this, $this->option_name . '_applepay_button_color_cb' ),
      $this->plugin_name,
      $this->option_name . '_payment',
      array( 'label_for' => $this->option_name . '_applepay_button_color' )
    );

		add_settings_field(
			$this->option_name . '_googlepay_handler',
			__( 'Google Pay', 'digital-river-global-commerce' ),
			array( $this, $this->option_name . '_googlepay_handler_cb' ),
			$this->plugin_name,
			$this->option_name . '_payment',
			array( 'label_for' => $this->option_name . '_googlepay_handler' )
    );
    
    add_settings_field(
      $this->option_name . '_googlepay_button_type',
      __( 'Button Type', 'digital-river-global-commerce' ),
      array( $this, $this->option_name . '_googlepay_button_type_cb' ),
      $this->plugin_name,
      $this->option_name . '_payment',
      array( 'label_for' => $this->option_name . '_googlepay_button_type' )
    );
    
    add_settings_field(
      $this->option_name . '_googlepay_button_color',
      __( 'Button Color', 'digital-river-global-commerce' ),
      array( $this, $this->option_name . '_googlepay_button_color_cb' ),
      $this->plugin_name,
      $this->option_name . '_payment',
      array( 'label_for' => $this->option_name . '_googlepay_button_color' )
    );

		add_settings_section(
			$this->option_name . '_extra',
			'',
			array( $this, $this->option_name . '_extra_cb' ),
			$this->plugin_name
		);

		register_setting( $this->plugin_name, $this->option_name . '_site_id', array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ) );
		register_setting( $this->plugin_name, $this->option_name . '_api_key', array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ) );
		register_setting( $this->plugin_name, $this->option_name . '_api_secret', array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ) );
		register_setting( $this->plugin_name, $this->option_name . '_domain', array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ) );
		register_setting( $this->plugin_name, $this->option_name . '_digitalRiver_key', array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ) );
		register_setting( $this->plugin_name, $this->option_name . '_big_blue_username', array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ) );
		register_setting( $this->plugin_name, $this->option_name . '_big_blue_password', array( 'type' => 'string', 'sanitize_callback' => null ) );
    register_setting( $this->plugin_name, $this->option_name . '_cron_handler', array( 'sanitize_callback' => array( $this, 'dr_sanitize_checkbox' ), 'default' => '' ) );
		register_setting( $this->plugin_name, $this->option_name . '_testOrder_handler', array( 'sanitize_callback' => array( $this, 'dr_sanitize_checkbox' ), 'default' => '' ) );
		register_setting( $this->plugin_name, $this->option_name . '_force_excl_tax_handler', array( 'sanitize_callback' => array( $this, 'dr_sanitize_checkbox' ), 'default' => '' ) );
		register_setting( $this->plugin_name, $this->option_name . '_applepay_handler', array( 'sanitize_callback' => array( $this, 'dr_sanitize_checkbox' ), 'default' => '' ) );
    register_setting( $this->plugin_name, $this->option_name . '_googlepay_handler', array( 'sanitize_callback' => array( $this, 'dr_sanitize_checkbox' ), 'default' => '' ) );
    register_setting( $this->plugin_name, $this->option_name . '_applepay_button_type' );
    register_setting( $this->plugin_name, $this->option_name . '_applepay_button_color' );
    register_setting( $this->plugin_name, $this->option_name . '_googlepay_button_type' );
    register_setting( $this->plugin_name, $this->option_name . '_googlepay_button_color' );
	}

	/**
	 * Render the text for the general section.
	 *
	 * @since  1.0.0
	 */
	public function drgc_general_cb() {
		return; // No need to print section message
	}

	/**
	 * Render the text for the checkout section.
	 *
	 * @since  1.3.1
	 */
	public function drgc_checkout_cb() {
		return; // No need to print section message
	}

	/**
	 * Render the text for the payment section.
	 *
	 * @since  1.0.2
	 */
	public function drgc_payment_cb() {
		return; // No need to print section message
	}

	/**
	 * Render the text for the extra section.
	 *
	 * @since  1.0.0
	 */
	public function drgc_extra_cb() {
		echo '<p class="description">' . __( 'Please contact your account representative for assistance with these settings.', 'digital-river-global-commerce' ) . '</p>';
	}

	/**
	 * Render input text field for Site ID.
	 *
	 * @since    1.0.0
	 */
	public function drgc_site_id_cb() {
		$site_id = get_option( $this->option_name . '_site_id' );
		echo '<input type="text" class="regular-text" name="' . $this->option_name . '_site_id' . '" id="' . $this->option_name . '_site_id' . '" value="' . $site_id . '"> ';
	}

	/**
	 * Render input text field for API Key.
	 *
	 * @since    1.0.0
	 */
	public function drgc_api_key_cb() {
		$api_key = get_option( $this->option_name . '_api_key' );
		echo '<div data-tooltip="Required to access your Global Commerce catalog data" data-tooltip-location="right"><input type="text" class="regular-text" name="' . $this->option_name . '_api_key' . '" id="' . $this->option_name . '_api_key' . '" value="' . $api_key . '"></div>';
	}

	/**
	 * Render input text field for API Secret.
	 *
	 * @since    1.0.0
	 */
	public function drgc_api_secret_cb() {
		$api_secret = get_option( $this->option_name . '_api_secret' );
		echo '<div data-tooltip="Required to support saved accounts for returning users" data-tooltip-location="right"><input type="text" class="regular-text" name="' . $this->option_name . '_api_secret' . '" id="' . $this->option_name . '_api_secret' . '" value="' . $api_secret . '"></div>';
	}

	/**
	 * Render input text field for domain setting.
	 *
	 * @since    1.0.0
	 */
	public function drgc_domain_cb() {
		$domain = get_option( $this->option_name . '_domain' );
		echo '<input type="text" class="regular-text" name="' . $this->option_name . '_domain' . '" id="' . $this->option_name . '_domain' . '" value="' . $domain . '"> ';
	}

	/**
	 * Render input text field for DigitalRiver Plugin
	 *
	 * @since    1.0.0
	 */
	public function drgc_digitalRiver_key_cb() {
		$digitalRiver_key = get_option( $this->option_name . '_digitalRiver_key' );
		echo '<div data-tooltip="Required to process payments via DigitalRiver.js" data-tooltip-location="right"><input type="text" class="regular-text" name="' . $this->option_name . '_digitalRiver_key' . '" id="' . $this->option_name . '_digitalRiver_key' . '" value="' . $digitalRiver_key . '"></div>';
	}

	/**
	 * Render checkbox field for enabling scheduled import
	 *
	 * @since    1.0.0
	 */

	public function drgc_testOrder_handler_cb() {
		$option = get_option( $this->option_name . '_testOrder_handler' );
		$checked = '';

		if ( is_array( $option ) && $option['checkbox'] === '1' ) {
			$checked = 'checked="checked"';
		}

		echo '<input type="checkbox" class="regular-text" name="' . $this->option_name . '_testOrder_handler[checkbox]" id="' . $this->option_name . '_testOrder_handler" value="1" ' . $checked . ' />';
		echo '<span class="description" id="cron-description">' . __( 'Enable Test Order.', 'digital-river-global-commerce' ) . '</span>';
	}

	public function drgc_force_excl_tax_handler_cb() {
		$option = get_option( $this->option_name . '_force_excl_tax_handler' );
		$checked = '';

		if ( is_array( $option ) && $option['checkbox'] === '1' ) {
			$checked = 'checked="checked"';
		}

		echo '<input type="checkbox" class="regular-text" name="' . $this->option_name . '_force_excl_tax_handler[checkbox]" id="' . $this->option_name . '_force_excl_tax_handler" value="1" ' . $checked . ' />';
		echo '<span class="description" id="force-excl-tax-description">' . __( 'Display pricing as tax exclusive on checkout flow', 'digital-river-global-commerce' ) . '</span>';
	}

	public function drgc_cron_handler_cb() {
		$option = get_option( $this->option_name . '_cron_handler' );
		$checked = '';

		if ( is_array( $option ) && $option['checkbox'] === '1' ) {
			$checked = 'checked="checked"';
		}

		echo '<input type="checkbox" class="regular-text" name="' . $this->option_name . '_cron_handler[checkbox]" id="' . $this->option_name . '_cron_handler" value="1" ' . $checked . ' />';
		echo '<span class="description" id="cron-description">' . __( 'Twice daily product synchronization with GC.', 'digital-river-global-commerce' ) . '</span>';
	}

	public function dr_sanitize_checkbox( $input ) {
		$new_input['checkbox'] = trim( $input['checkbox'] );
		return $new_input;
  }
  
	/**
	 * Render button of products import.
	 *
	 * @since    1.0.0
	 */
	public function render_products_import_button( $views ) {
		include_once DRGC_PLUGIN_DIR . 'admin/partials/drgc-products-import-btn.php';
		return $views;
	}

	/**
	 * Delete associated variations when a DR product is being deleted
	 * Note: This fires when the user empties the Trash
	 *
	 * @param $postid
	 */
	public function clean_variations_on_product_deletion( $postid ) {
		if ( get_post_type( $postid ) != 'dr_product' ) {
			return;
		}

		$variations = drgc_get_product_variations( $postid );

		if ( $variations ) {
			foreach ( $variations as $variation ) {
				wp_delete_post( $variation->ID, true );
			}
		}
	}

	/**
	 * Render checkbox field for enabling Apple Pay
	 *
	 * @since    1.0.2
	 */
	public function drgc_applepay_handler_cb() {
		$option = get_option( $this->option_name . '_applepay_handler' );
		$checked = '';

		if ( is_array( $option ) && $option['checkbox'] === '1' ) {
			$checked = 'checked="checked"';
		}

		echo '<label class="switch"><input type="checkbox" class="regular-text" name="' . $this->option_name . '_applepay_handler[checkbox]" id="' . $this->option_name . '_applepay_handler" value="1" ' . $checked . ' /><span class="slider round"></span></label>';
	}

  /**
   * Render radio button for Apple Pay button type.
   *
   * @since    1.3.0
   */
	public function drgc_applepay_button_type_cb() {
    $button_type = get_option( $this->option_name . '_applepay_button_type', 'buy' );
    $option = get_option( $this->option_name . '_applepay_handler' );
    $disabled = ( is_array( $option ) && $option['checkbox'] === '1' ) ? '' : 'disabled';
  ?>
    <fieldset class="payment-btn-field" data-tooltip="<?php _e( 'Required to the type of Apple Pay button', 'digital-river-global-commerce' ); ?>" data-tooltip-location="up" <?php echo $disabled; ?>>
      <legend><span><?php _e( 'Button Type', 'digital-river-global-commerce' ); ?></span></legend>
      <input type="radio" id="applepay_long" name="<?php echo $this->option_name; ?>_applepay_button_type" value="buy" <?php checked( $button_type, 'buy' ); ?> />
      <label for="applepay_long"><?php _e( 'Long', 'digital-river-global-commerce' ); ?></label><br />
      <input type="radio" id="applepay_plain" name="<?php echo $this->option_name; ?>_applepay_button_type" value="plain" <?php checked( $button_type, 'plain' ); ?> />
      <label for="applepay_plain"><?php _e( 'Plain', 'digital-river-global-commerce' ); ?></label>
    </fieldset>
  <?php if ( $disabled ): ?>
    <input type="hidden" id="applepay_button_type" name="<?php echo $this->option_name; ?>_applepay_button_type" value="<?php echo $button_type; ?>" />
  <?php endif; ?>
  <?php }
  
  /**
   * Render radio button for Apple Pay button color.
   *
   * @since    1.3.0
   */
  public function drgc_applepay_button_color_cb() {
    $button_color = get_option( $this->option_name . '_applepay_button_color', 'dark' );
    $option = get_option( $this->option_name . '_applepay_handler' );
    $disabled = ( is_array( $option ) && $option['checkbox'] === '1' ) ? '' : 'disabled';
  ?>
    <fieldset class="payment-btn-field" data-tooltip="<?php _e( 'Required to the color of Apple Pay button', 'digital-river-global-commerce' ); ?>" data-tooltip-location="up" <?php echo $disabled; ?>>
      <legend><span><?php __( 'Button Color', 'digital-river-global-commerce' ) ?></span></legend>
      <input type="radio" id="applepay_black" name="<?php echo $this->option_name; ?>_applepay_button_color" value="dark" <?php checked( $button_color, 'dark' ); ?> />
      <label for="applepay_black"><?php _e( 'Black', 'digital-river-global-commerce' ); ?></label><br />
      <input type="radio" id="applepay_white" name="<?php echo $this->option_name; ?>_applepay_button_color" value="light" <?php checked( $button_color, 'light' ); ?> />
      <label for="applepay_white"><?php _e( 'White', 'digital-river-global-commerce' ); ?></label>
    </fieldset>
  <?php if ( $disabled ): ?>
    <input type="hidden" id="applepay_button_color" name="<?php echo $this->option_name; ?>_applepay_button_color" value="<?php echo $button_color; ?>" />
  <?php endif; ?>
  <?php }

	/**
	 * Render checkbox field for enabling Google Pay
	 *
	 * @since    1.0.2
	 */
	public function drgc_googlepay_handler_cb() {
		$option = get_option( $this->option_name . '_googlepay_handler' );
		$checked = '';

		if ( is_array( $option ) && $option['checkbox'] === '1' ) {
			$checked = 'checked="checked"';
		}

		echo '<label class="switch"><input type="checkbox" class="regular-text" name="' . $this->option_name . '_googlepay_handler[checkbox]" id="' . $this->option_name . '_googlepay_handler" value="1" ' . $checked . ' /><span class="slider round"></span></label>';
	}
	
  /**
   * Render radio button for Google Pay button type.
   *
   * @since    1.3.0
   */
  public function drgc_googlepay_button_type_cb() {
    $button_type = get_option( $this->option_name . '_googlepay_button_type', 'long' );
    $option = get_option( $this->option_name . '_googlepay_handler' );
    $disabled = ( is_array( $option ) && $option['checkbox'] === '1' ) ? '' : 'disabled';
  ?>
    <fieldset class="payment-btn-field" data-tooltip="<?php _e( 'Required to the type of Google Pay button', 'digital-river-global-commerce' ); ?>" data-tooltip-location="up" <?php echo $disabled; ?>>
      <legend><span><?php _e( 'Button Type', 'digital-river-global-commerce' ); ?></span></legend>
      <input type="radio" id="googlepay_long" name="<?php echo $this->option_name; ?>_googlepay_button_type" value="long" <?php checked( $button_type, 'long' ); ?> />
      <label for="googlepay_long"><?php _e( 'Long', 'digital-river-global-commerce' ); ?></label><br />
      <input type="radio" id="googlepay_plain" name="<?php echo $this->option_name; ?>_googlepay_button_type" value="plain" <?php checked( $button_type, 'plain' ); ?> />
      <label for="googlepay_plain"><?php _e( 'Plain', 'digital-river-global-commerce' ); ?></label>
    </fieldset>
  <?php if ( $disabled ): ?>
    <input type="hidden" id="googlepay_button_type" name="<?php echo $this->option_name; ?>_googlepay_button_type" value="<?php echo $button_type; ?>" />
  <?php endif; ?>
  <?php }

  /**
   * Render radio button for Google Pay button color.
   *
   * @since    1.3.0
   */
  public function drgc_googlepay_button_color_cb() {
    $button_color = get_option( $this->option_name . '_googlepay_button_color', 'dark' );
    $option = get_option( $this->option_name . '_googlepay_handler' );
    $disabled = ( is_array( $option ) && $option['checkbox'] === '1' ) ? '' : 'disabled';
  ?>
    <fieldset class="payment-btn-field" data-tooltip="<?php _e( 'Required to the color of Google Pay button', 'digital-river-global-commerce' ); ?>" data-tooltip-location="up" <?php echo $disabled; ?>>
      <legend><span><?php __( 'Button Color', 'digital-river-global-commerce' ) ?></span></legend>
      <input type="radio" id="googlepay_black" name="<?php echo $this->option_name; ?>_googlepay_button_color" value="dark" <?php checked( $button_color, 'dark' ); ?> />
      <label for="googlepay_black"><?php _e( 'Black', 'digital-river-global-commerce' ); ?></label><br />
      <input type="radio" id="googlepay_white" name="<?php echo $this->option_name; ?>_googlepay_button_color" value="light" <?php checked( $button_color, 'light' ); ?> />
      <label for="googlepay_white"><?php _e( 'White', 'digital-river-global-commerce' ); ?></label>
    </fieldset>
  <?php if ( $disabled ): ?>
    <input type="hidden" id="googlepay_button_color" name="<?php echo $this->option_name; ?>_googlepay_button_color" value="<?php echo $button_color; ?>" />
  <?php endif; ?>
  <?php }
  
  /**
	 * Render input text field for UMS username.
	 *
	 * @since    1.3.0
	 */
	public function drgc_big_blue_username_cb() {
		$username = get_option( $this->option_name . '_big_blue_username' );
		echo '<div data-tooltip="Required to manage and retrieve subscriptions via User Management Service" data-tooltip-location="right"><input type="text" class="regular-text" name="' . $this->option_name . '_big_blue_username' . '" id="' . $this->option_name . '_big_blue_username' . '" value="' . $username . '"></div>';
	}

	/**
	 * Render input text field for UMS password.
	 *
	 * @since    1.3.0
	 */
	public function drgc_big_blue_password_cb() {
		$password = password_hash( get_option( $this->option_name . '_big_blue_password' ), PASSWORD_DEFAULT );
		echo '<div data-tooltip="Required to manage and retrieve subscriptions via User Management Service" data-tooltip-location="right"><input type="password" class="regular-text" name="' . $this->option_name . '_big_blue_password' . '" id="' . $this->option_name . '_big_blue_password' . '" value="' . $password . '"></div>';
	}
}
