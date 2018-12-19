<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
  add_filter('woocommerce_general_settings', 'matdespatch_general_setting_field');
  add_action('woocommerce_settings_saved', 'matdespatch_setting_saved');

  function matdespatch_general_setting_field($settings) {
    $settings[] = array(
      'name' => __( 'Matdespatch.com Settings', 'matdespatch' ),
      'type' => 'title',
      'id' => 'wc_general_settings_matdespatch_pricing_title'
    );

    $settings[] = array(
      'title'    	=> __( 'User Id', 'matdespatch' ),
      'id'       	=> 'matdespatch_id',
      'type'     	=> 'text',
      'custom_attributes' => array('readonly' => 'readonly')
    );

    $settings[] = array(
      'title'    	=> __( 'Api Key', 'matdespatch' ),
      'id'       	=> 'matdespatch_api',
      'type'     	=> 'text',
      'custom_attributes' => array('readonly' => 'readonly')
    );

    $settings[] = array(
      'title'    	=> __( 'Price check', 'matdespatch' ),
      'id'       	=> 'matdespatch_pricing_enable',
      'desc'  	=> __( 'Enable or disable price check', 'matdespatch' ),
      'type'     	=> 'checkbox',
      'default'	=> 'yes',
      'desc_tip'	=> true,
    );

    $settings[] = array( 'type' => 'sectionend', 'id' => 'wc_general_settings_matdespatch_pricing_section_end');

    return $settings;
  }

  function matdespatch_setting_saved() {
    global $woocommerce;
    global $wp_session;

        $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => 'https://api.dev.matdespatch.app/acikacikbukapintu',
        CURLOPT_USERAGENT => 'test'
    ));

    $resp = curl_exec($curl);

    curl_close($curl);

    if (WC_Admin_Settings::get_option('wc_general_settings_matdespatch_pricing_enable') == 'no') {
      $wp_session['chosen_shipping_methods'] = array( 'free_shipping' ); 
//      WC()->session->set('chosen_shipping_methods', array( 'free_shipping' ) );
    }

    if (WC_Admin_Settings::get_option('wc_general_settings_matdespatch_pricing_enable') == 'yes') {
      $wp_session['chosen_shipping_methods'] = array( 'matdespatch' );
      add_action('woocommerce_shipping_init', 'ShippingInit');
      add_filter('woocommerce_shipping_methods', 'AddShippingMethod');
  //    WC()->session->set('chosen_shipping_methods', array( 'matdespatch' ) );
    }
  }
}
?>