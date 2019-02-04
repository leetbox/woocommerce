<?php
    /*
    Plugin Name: Delyva.com
    Plugin URI: https://delyva.com/
    description: A plugin to automate shipping and fulfilment via Delyva.com. Requires : WooCommerce
    Version: 1.0
    Author: Delyva.com
    Author URI: https://delyva.com
    License: GPL2
    */

    // Include functions.php, use require_once to stop the script if functions.php is not found
    defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
    define('DELYVA_API_ENDPOINT', 'https://api.dev.matdespatch.app');
    define('DELYVA_PLUGIN_VERSION', '1.0');
    require_once plugin_dir_path(__FILE__) . 'includes/functions.php';
    require_once plugin_dir_path(__FILE__) . 'includes/hooks/shipping_hook.php';
    require_once plugin_dir_path(__FILE__) . 'includes/hooks/general_setting.php';
    require_once plugin_dir_path(__FILE__) . 'includes/hooks/custom_order_widget_hook.php';
