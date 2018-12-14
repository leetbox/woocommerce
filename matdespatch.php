<?php
    /*
    Plugin Name: Matdespatch.com
    Plugin URI: https://matdespatch.com/
    description: A plugin to automate shipping and fulfilment via Matdespatch.com. Requires : WooCommerce
    Version: 1.0
    Author: Matdespatch.com
    Author URI: https://matdespatch.com
    License: GPL2
    */

    // Include functions.php, use require_once to stop the script if functions.php is not found
    defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
    define('MATDESPATCH_PRICE_QUOTE', 'https://api.dev.matdespatch.app/v2/service/price_quote');
    define('MATDESPATCH_SHIPMENT', 'https://api.dev.matdespatch.app/v2/shipment');
    require_once plugin_dir_path(__FILE__) . 'includes/functions.php';
    require_once plugin_dir_path(__FILE__) . 'includes/hooks/shipping_hook.php';
    require_once plugin_dir_path(__FILE__) . 'includes/hooks/general_setting.php';
    require_once plugin_dir_path(__FILE__) . 'includes/hooks/custom_order_widget_hook.php';