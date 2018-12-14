<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
  add_filter('woocommerce_general_settings', 'matdespatch_general_setting_field');
  add_action('woocommerce_settings_saved', 'matdespatch_setting_saved');

  function matdespatch_general_setting_field($settings) {
    $settings[] = array(
      'name' => __( 'Pricing Options', 'matdespatch' ),
      'type' => 'title',
      'desc' => __( 'The following options affect prices based on Matdespatch.com service', 'matdespatch' ),
      'id' => 'wc_general_settings_matdespatch_pricing_title'
  );

    $settings[] = array(
      'title'    	=> __( 'Price check', 'matdespatch' ),
      'id'       	=> 'wc_general_settings_matdespatch_pricing_enable',
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
    if (WC_Admin_Settings::get_option('wc_general_settings_matdespatch_pricing_enable') == 'no') {
      $woocommerce->shipping->reset_shipping();
    }

    if (WC_Admin_Settings::get_option('wc_general_settings_matdespatch_pricing_enable') == 'yes') {
      $woocommerce->shipping->reset_shipping();
    }
  }
}
?>