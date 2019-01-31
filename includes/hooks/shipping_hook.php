<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    add_action('woocommerce_shipping_init', 'ShippingInit');
    add_filter('woocommerce_shipping_methods', 'AddShippingMethod');

    if (class_exists('MatDespatchShippingMethod')) {
	    $t = new MatDespatchShippingMethod();
    	$t->init();
    }

    function ShippingInit() {
        if (!class_exists('MatDespatchShippingMethod')) {   

            class MatDespatchShippingMethod extends WC_Shipping_Method {

                /**
                 * Constructor for your shipping class.
                 */
                public function __construct() {
                    $this->id = 'matdespatch'; // Id for your shipping method. Should be uunique.
                    $this->method_title = __('Matdespatch.com', 'matdespatch');  // Title shown in admin
                    if (get_option('matdespatch_integration_id')) {
                        $this->method_description = __('Matdespatch account : ' . get_option('matdespatch_integration_id') . '<br />Log in to Matdespatch.com to configure fulfillment settings.', 'matdespatch');
                    } else {
                        $this->method_description = __('<h3 style="color:red"><b>NOTICE!</b> This app is not configured! Please log in to Matdespatch.com then navigate to "Integration > Setup"</h3>', 'matdespatch');
                    }
                    $this->init();
                    $this->enabled = isset($this->settings['enabled']) ? $this->settings['enabled'] : 'yes';
                    $this->title = isset($this->settings['title']) ? $this->settings['title'] : __('Matdespatch.com Settings', 'matdespatch');
                }

                /**
                 * Init your settings.
                 */
                public function init() {
                    // Load the settings API
                    $this->init_matdespatch_form_fields(); // This is part of the settings API. Override the method to add your own settings
                    $this->init_settings(); // This is part of the settings API. Loads settings you previously init.

                    // Save settings in admin if you have any defined
                    add_action('woocommerce_update_options_shipping_'.$this->id, array($this, 'process_admin_options'));
                }

                public function init_matdespatch_form_fields() {
                    $this->form_fields = array(
                        'title5' => array(
                            'title' => __( 'Shipping Rate Adjustments', 'matdespatch' ),
                            'type' => 'title',
                            'id' => 'wc_settings_matdespatch_shipping_rate_adjustment',
                            'description' => __( 'Formula, shipping cost = shipping price + % rate + flat rate' ),
                        ),
                        'PercentageRate' => array(
                            'title' => __('Percentage Rate %', 'matdespatch'),
                            'type' => 'text',
                            'default' => __('0', 'matdespatch'),
                            'id' => 'matdespatch_rate_adjustment_percentage'
                        ),
                        'FlatRate' => array(
                            'title' => __('Flat Rate', 'matdespatch'),
                            'type' => 'text',
                            'default' => __('0', 'matdespatch'),
                            'id' => 'matdespatch_rate_adjustment_flat'
                        ),
                    );
                }
                
                /**
                 * calculate_shipping function.
                 *
                 * @param mixed $package
                 */
                public function calculate_shipping($package)
                {
                    if (get_option('wc_general_settings_matdespatch_pricing_enable') == 'yes') {
                        global $woocommerce;
                        
                        $Grams = 0;
                        switch (get_option('woocommerce_weight_unit')) {
                            case 'kg':
                                $Grams = $woocommerce->cart->cart_contents_weight;
                                break;
                            case 'g':
                                $Grams = $woocommerce->cart->cart_contents_weight / 1000;
                                break;
                            case 'lbs':
                                $Grams = $woocommerce->cart->cart_contents_weight / 0.45359237;
                                break;
                            case 'oz':
                                $Grams = $woocommerce->cart->cart_contents_weight / 0.028349523125;
                                break;
                            default:
                                $Grams = '0.5';
                                break;
                        }

                        if ($Grams < '0.5') {
                            $Grams = '0.5';
                        }

                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, MATDESPATCH_API_ENDPOINT . '/api/integration/woocommerce/getRates/' . get_option('matdespatch_integration_id'));
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_HEADER, false);
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                            'address' => $woocommerce->customer->get_shipping_address(),
                            'postcode' => $woocommerce->customer->get_shipping_postcode(),
                            'country' => $woocommerce->customer->get_shipping_country(),
                            'weight' => $Grams
                        ]));
                        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                            'Content-Type: application/json',
                        ));
                        $results = curl_exec($ch);
                        curl_close($ch);
                        $results = json_decode($results, true);
                        
                        foreach ($results as $shipper) {
                            if (isset($shipper['service_name'])) {
                                $percentRate = get_options('matdespatch_rate_adjustment_percentage', 1) / 100 * $shipper['total_price'];
                                $flatRate = get_options('matdespatch_rate_adjustment_flat', 0);

                                $rate = array(
                                    'id' => $shipper['service_code'],
                                    'label' => $shipper['service_name'],
                                    'cost' => round($shipper['total_price'] + $percentRate + flatRate, 2),
                                    'taxes' => 'false',
                                    'calc_tax' => 'per_order',
                                    'meta_data' => array(
                                        'service_code' => $shipper['service_code'],
                                    ),
                                );
                                // Register the rate
                                $this->add_rate($rate);
                            }
                        }
                    }
                }
            }
        }
    }

    function AddShippingMethod($methods) {
        $methods['your_shipping_method'] = 'MatDespatchShippingMethod';
        return $methods;
    }

    function getIntegrationSettings() {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, MATDESPATCH_API_ENDPOINT . '/api/integration/get/' . get_option('matdespatch_integration_id'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'id: ' . get_option('matdespatch_user_id'),
            'session: ' . get_option('matdespatch_api_key')
        ));
        $results = curl_exec($ch);
        curl_close($ch);

        return json_decode($results, true);
    }
}
