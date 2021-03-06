<?php
/*
Plugin Name: WC Autoship Product Page Options
Plugin URI: http://wooautoship.com
Description: Customize the autoship options on the product page.
Version: 1.1.4
Author: Patterns in the Cloud
Author URI: http://patternsinthecloud.com
License: Single-site
*/

define( 'WC_Autoship_Product_Page_Options_Version', '1.1.4' );

function wc_autoship_product_page_install() {
	// Add default settings
	add_option( 'wc_autoship_product_page_layout', 'radio-buttons' );
	add_option( 'wc_autoship_product_page_frequency_options', array(
		'7' => 'Weekly',
		'30' => 'Monthly',
		'60' => 'Bi-Monthly',
		'90' => 'Quarterly'
	) );
}
register_activation_hook( __FILE__, 'wc_autoship_product_page_install' );

function wc_autoship_product_page_deactivate() {

}
register_deactivation_hook( __FILE__, 'wc_autoship_product_page_deactivate' );

function wc_autoship_product_page_uninstall() {

}
register_uninstall_hook( __FILE__, 'wc_autoship_product_page_uninstall' );

require_once( 'src/dependency.php' );
// Include source files
// check is wc running
if(wc_as_check_is_wc_running() == false) {
	return;
}

if(wc_as_running() == false) {
	return;
}

function wc_autoship_product_page_admin_scripts() {
	wp_enqueue_style( 'wc-autoship-product-page-admin', plugin_dir_url( __FILE__ ) . 'css/admin.css', array(), WC_Autoship_Product_Page_Options_Version );
	wp_enqueue_script( 'jquery-ui-core' );
	wp_enqueue_script( 'jquery-ui-autocomplete' );
	wp_register_script( 'wc-autoship-product-page-admin', plugin_dir_url( __FILE__ ) . 'js/admin.js', array('jquery'), WC_Autoship_Product_Page_Options_Version, true );
	wp_localize_script('wc-autoship-product-page-admin', 'WC_AUTOSHIP_PRODUCT_PAGE_OPTIONS', array(
		'WC_AUTOSHIP_MIN_FREQUENCY' => defined( 'WC_AUTOSHIP_MIN_FREQUENCY' ) ? WC_AUTOSHIP_MIN_FREQUENCY : 7,
		'WC_AUTOSHIP_MAX_FREQUENCY' => defined( 'WC_AUTOSHIP_MAX_FREQUENCY' ) ? WC_AUTOSHIP_MAX_FREQUENCY : 365
	));
	wp_enqueue_script( 'wc-autoship-product-page-admin' );
}
add_action( 'admin_enqueue_scripts', 'wc_autoship_product_page_admin_scripts' );

function wc_autoship_product_page_settings( $settings ) {
	$settings[] = array(
		'title' => __( 'Product Page Autoship Options', 'wc-autoship-product-page' ),
		'desc' => __( 'Customize the autoship options on the product page', 'wc-autoship-product-page' ),
		'desc_tip' => false,
		'type' => 'title',
		'id' => 'wc_autoship_product_page_title'
	);
	$settings[] = array(
		'name' => __( 'License Key', 'wc-autoship-product-page' ),
		'desc' => __( 'Enter your software license key issued after purchase.', 'wc-autoship-product-page' ),
		'desc_tip' => true,
		'type' => 'text',
		'id' => 'wc_autoship_product_page_license_key'
	);
	$settings[] = array(
		'name' => __( 'Product Page Autoship Options Layout', 'wc-autoship-product-page' ),
		'desc' => __( 'The layout for autoship options on the product page', 'wc-autoship-product-page' ),
		'desc_tip' => true,
		'type' => 'select',
		'id' => 'wc_autoship_product_page_layout',
		'options' => array(
			'' => __( 'Disable custom options', 'wc-autoship-product-page' ),
			'radio-buttons' => __( 'Radio Buttons', 'wc-autoship-product-page' ),
			'select-box' => __( 'Select Box', 'wc-autoship-product-page' )
		)
	);
	$settings[] = array(
		'name' => __( 'Product Page Autoship Description', 'wc-autoship-product-page' ),
		'desc' => __( 'Enter a custom description for the autoship options on the product page.', 'wc-autoship-product-page' ),
		'desc_tip' => true,
		'type' => 'textarea',
		'id' => 'wc_autoship_product_page_description',
		'placeholder' => wc_autoship_product_page_get_default_description(),
		'css' => 'min-width: 300px;'
	);
	$settings[] = array(
		'name' => __( 'Product Page Autoship Options', 'wc-autoship-product-page' ),
		'desc' => __( 'The autoship options to show on the product page', 'wc-autoship-product-page' ),
		'desc_tip' => true,
		'type' => 'wc_autoship_product_page_frequency_options',
		'id' => 'wc_autoship_product_page_frequency_options'
	);
	$settings[] = array(
		'type' => 'sectionend',
		'id' => 'wc_autoship_product_page_sectionend'
	);
	return $settings;
}
add_filter( 'wc_autoship_addons_settings', 'wc_autoship_product_page_settings', 10, 1 );

function wc_autoship_product_page_addon_license_keys( $addon_license_keys ) {
	if ( ! isset( $addon_license_keys['wc_autoship_product_page_license_key'] ) ) {
		$addon_license_keys['wc_autoship_product_page_license_key'] = array(
			'item_name' => 'WC Auto-Ship Product Page Options',
			'license' => trim( get_option( 'wc_autoship_product_page_license_key' ) ),
			'version' => WC_Autoship_Product_Page_Options_Version,
			'plugin_file' => __FILE__
		);
	}
	return $addon_license_keys;
}
add_filter( 'wc_autoship_addon_license_keys', 'wc_autoship_product_page_addon_license_keys', 10, 1 );

function wc_autoship_product_page_frequency_options( $value ) {
	$vars = array(
		'value' => $value,
		'description' => WC_Admin_Settings::get_field_description( $value ),
		'frequency_options' => get_option( 'wc_autoship_product_page_frequency_options' )
	);
	$relative_path = 'admin/wc-settings/wc-autoship/product-page-frequency-options';
	wc_autoship_product_page_include_plugin_template( $relative_path, $vars );
}
add_action( 'woocommerce_admin_field_wc_autoship_product_page_frequency_options', 'wc_autoship_product_page_frequency_options' );

function wc_autoship_product_page_options_available_frequencies( $available_frequencies, $schedule_id ) {
	$frequency_options = get_option( 'wc_autoship_product_page_frequency_options' );
	if ( empty( $frequency_options ) ) {
		return $available_frequencies;
	}

	$titled_frequencies = array();
	foreach ( $frequency_options as $frequency => $title ) {
		if ( ! wc_autoship_product_page_options_frequency_is_available( $frequency, $available_frequencies ) ) {
			continue;
		}
		$option = array(
			'frequency' => $frequency,
			'title' => $title
		);
		$titled_frequencies[] = $option;
	}
	if ( ! empty( $titled_frequencies ) ) {
		return $titled_frequencies;
	}
	return $available_frequencies;
}
add_filter( 'wc_autoship_schedule_available_frequencies', 'wc_autoship_product_page_options_available_frequencies', 10, 2 );

function wc_autoship_product_page_options_frequency_is_available( $frequency, $available_frequencies ) {
	foreach ( $available_frequencies as $f ) {
		if ( $frequency == $f['frequency'] ) {
			return true;
		}
	}
	return false;
}

function wc_autoship_product_page_template( $path, $template, $vars ) {
	$layout = get_option( 'wc_autoship_product_page_layout' );
	if ( empty( $layout ) ) {
		return $path;
	}
	if ( $template == 'product/autoship-options' ) {
		return wc_autoship_product_page_get_plugin_template_path( $layout . '/' . $template );
	} elseif ( $template == 'product/autoship-options-variable' ) {
		return wc_autoship_product_page_get_plugin_template_path( $layout . '/' . $template );
	}
	return $path;
}
add_filter( 'wc_autoship_plugin_template', 'wc_autoship_product_page_template', 10, 3 );

function wc_autoship_product_page_get_plugin_template_path( $relative_path ) {
	return plugin_dir_path( __FILE__ ) . 'templates/' . $relative_path . '.php';
}

function wc_autoship_product_page_include_plugin_template( $relative_path, $vars = array() ) {
	extract( $vars );
	include ( wc_autoship_product_page_get_plugin_template_path( $relative_path, $vars ) );
}

function wc_autoship_product_page_options_sanitize_value( $value, $option, $raw_value ) {
	if ( isset( $_POST['wc_autoship_product_page_frequency_options_array'] ) ) {
		return $_POST['wc_autoship_product_page_frequency_options_array'];
	}
	return array();
}
add_filter( 'woocommerce_admin_settings_sanitize_option_wc_autoship_product_page_frequency_options',
	'wc_autoship_product_page_options_sanitize_value',
	10,
	3
);

function wc_autoship_product_page_get_description() {
	$description = get_option( 'wc_autoship_product_page_description' );
	if ( $description ) {
		return do_shortcode( $description );
	}
	return wc_autoship_product_page_get_default_description();
}

function wc_autoship_product_page_get_default_description() {
	return __( 'Select an Auto-Ship Frequency to add this item to auto-ship.', 'wc-autoship-product-page' );
}

function wc_autoship_product_page_get_no_autoship_name() {
	if ( function_exists( 'wc_autoship_get_no_autoship_option_name' ) ) {
		return wc_autoship_get_no_autoship_option_name();
	}
	return 'No Autoship';
}

function wc_as_page_options_updater() {
	$addon_license_keys = apply_filters( 'wc_autoship_addon_license_keys', array() );
	$item_name = "WC Autoship Product Page Options";


	if ( ! isset( $addon_license_keys[ 'wc_autoship_product_page_license_key' ] ) ) {
		# we do not have license show some message
		return;
	}else{
		if(!isset($addon_license_keys[ 'wc_autoship_product_page_license_key' ]['license'])){
			# we do not have license show some message
			return;
		}else{
			$license_key = $addon_license_keys[ 'wc_autoship_product_page_license_key' ]['license'];
		}
	}

	if ( empty( $license_key ) || empty($item_name) ) {
		# license field is empty, show some message
		return;
	}

	#This will make sure that code does not break the website
	if ( function_exists( 'wc_autoship_get_licensing_url' ) ) {

		require_once( 'edd/wc-autoship-plugin-updater.php' );

		new WC_AS_PAGE_OPTIONS_Plugin_Updater( wc_autoship_get_licensing_url(), __FILE__, array(
			'version'   => WC_Autoship_Product_Page_Options_Version,
			'license'   => $license_key,
			'item_name' => $item_name,
			'author'    => 'Patterns In the Cloud'
		) );
	} else {
		# Display a message that wc_auto_ship plugin is not activated, so customer knows what is going on
	}
}

add_action( 'admin_init', 'wc_as_page_options_updater', 0 );