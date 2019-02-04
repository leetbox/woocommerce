<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
  add_filter('woocommerce_general_settings', 'delyva_general_setting_field');

  function delyva_general_setting_field($settings) {
    $settings[] = array(
      'name' => __( 'Delyva.com Settings', 'delyva' ),
      'type' => 'title',
      'id' => 'wc_general_settings_delyva_pricing_title'
    );

    $settings[] = array(
      'title'    	=> __( 'User Id', 'delyva' ),
      'id'       	=> 'delyva_user_id',
      'type'     	=> 'hidden',
  //    'custom_attributes' => array('readonly' => 'readonly')
    );

    $settings[] = array(
      'title'    	=> __( 'Api Key', 'delyva' ),
      'id'       	=> 'delyva_api_key',
      'type'     	=> 'hidden',
//      'custom_attributes' => array('readonly' => 'readonly')
    );

    $settings[] = array(
      'title'           => __( 'Integration Id', 'delyva' ),
      'id'              => 'delyva_integration_id',
      'type'            => 'hidden',
//      'custom_attributes' => array('readonly' => 'readonly')
    );

    $settings[] = array(
      'title'    	=> __( 'Price check', 'delyva' ),
      'id'       	=> 'delyva_pricing_enable',
      'desc'  	=> __( 'Show shipping method upon checkout', 'delyva' ),
      'type'     	=> 'checkbox',
      'default'	=> 'yes',
      //'desc_tip'	=> true,
    );

    $settings[] = array( 'type' => 'sectionend', 'id' => 'wc_general_settings_delyva_pricing_section_end');

    return $settings;
  }
}
?>
