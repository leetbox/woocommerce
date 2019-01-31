<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
  add_filter('woocommerce_general_settings', 'matdespatch_general_setting_field');

  function matdespatch_general_setting_field($settings) {
    $settings[] = array(
      'name' => __( 'Matdespatch.com Settings', 'matdespatch' ),
      'type' => 'title',
      'id' => 'wc_general_settings_matdespatch_pricing_title'
    );

    $settings[] = array(
      'title'    	=> __( 'User Id', 'matdespatch' ),
      'id'       	=> 'matdespatch_user_id',
      'type'     	=> 'hidden',
  //    'custom_attributes' => array('readonly' => 'readonly')
    );

    $settings[] = array(
      'title'    	=> __( 'Api Key', 'matdespatch' ),
      'id'       	=> 'matdespatch_api_key',
      'type'     	=> 'hidden',
//      'custom_attributes' => array('readonly' => 'readonly')
    );

    $settings[] = array(
      'title'           => __( 'Integration Id', 'matdespatch' ),
      'id'              => 'matdespatch_integration_id',
      'type'            => 'hidden',
//      'custom_attributes' => array('readonly' => 'readonly')
    );

    $settings[] = array(
      'title'    	=> __( 'Price check', 'matdespatch' ),
      'id'       	=> 'matdespatch_pricing_enable',
      'desc'  	=> __( 'Show shipping method upon checkout', 'matdespatch' ),
      'type'     	=> 'checkbox',
      'default'	=> 'yes',
      //'desc_tip'	=> true,
    );

    $settings[] = array( 'type' => 'sectionend', 'id' => 'wc_general_settings_matdespatch_pricing_section_end');

    return $settings;
  }
}
?>
