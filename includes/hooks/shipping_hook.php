<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    if (get_option('wc_general_settings_matdespatch_pricing_enable') == 'yes') {
      add_action('woocommerce_shipping_init', 'ShippingInit');
      add_filter('woocommerce_shipping_methods', 'AddShippingMethod');
      add_action('woocommerce_add_to_cart', 'Test_');
    }

    function Test_() {
      //add_action('woocommerce_update_options_shipping_degil_tak_mau_jalan', array($this, 'process_admin_options'));
      // Get cURL resource
      $curl = curl_init();
      // Set some options - we are passing in a useragent too here
      curl_setopt_array($curl, array(
          CURLOPT_RETURNTRANSFER => 1,
          CURLOPT_URL => 'https://api.dev.matdespatch.app',
          CURLOPT_USERAGENT => 'Codular Sample cURL Request'
      ));
      // Send the request & save response to $resp
      $resp = curl_exec($curl);
      // Close request to clear up some resources
      curl_close($curl);
    }
    
    if (class_exists('MatDespatchShippingMethod')) {
    $t = new MatDespatchShippingMethod();
    $t->init();
    }

    function ShippingInit()
    {
        if (!class_exists('MatDespatchShippingMethod')) {

            class MatDespatchShippingMethod extends WC_Shipping_Method
            {
              
                /**
                 * Constructor for your shipping class.
                 */
                public function __construct()
                {
                    $this->id = 'matdespatch'; // Id for your shipping method. Should be uunique.
                    $this->method_title = __('Matdespatch.com', 'matdespatch');  // Title shown in admin
                    $this->method_description = __('A plugin to automate shipping upon orders', 'matdespatch'); // Description shown in admin
                    $this->init();
                    $this->enabled = isset($this->settings['enabled']) ? $this->settings['enabled'] : 'yes';
                    $this->title = isset($this->settings['title']) ? $this->settings['title'] : __('Matdespatch.com Settings', 'matdespatch');
                }

                /**
                 * Init your settings.
                 */
                public function init()
                {
                    // Load the settings API
                    $this->init_form_fields(); // This is part of the settings API. Override the method to add your own settings
                    $this->init_settings(); // This is part of the settings API. Loads settings you previously init.

                    // Save settings in admin if you have any defined
                    add_action('woocommerce_update_options_shipping_'.$this->id, array($this, 'process_admin_options'));
                }
                public function init_form_fields()
                {
                    $this->form_fields = array(
                        'title0' => array(
                          'title' => __( 'Account Details', 'matdespatch' ),
                          'type' => 'title',
                          'description' => __('Matdespatch account details', 'matdespatch'),
                          'id' => 'matdespatch_account_details'
                        ),
                        'AccountBalance' => array(
                            'title' => __('Account Balance', 'matdespatch'),
                            'type' => 'text',
                            'id' => 'matdespatch_account_balance',
                            'custom_attributes' => array('readonly' => 'readonly')
                        ),
                        'MatdespatchAccount' => array(
                          'title' => __('Matdespatch.com Account', 'matdespatch'),
                          'type' => 'text',
                          'id' => 'matdespatch_account',
                          'custom_attributes' => array('readonly' => 'readonly')
                        ),
                        'title2' => array(
                            'title' => __( 'Sender / Pickup Information', 'matdespatch' ),
                            'type' => 'title',
                            'id' => 'wc_settings_matdespatch_sender'
                        ),
                        'FullName' => array(
                            'title' => __('Name', 'matdespatch'),
                            'type' => 'text',
                        ),
                        'PhoneNo' => array(
                            'title' => __('Mobile No.', 'matdespatch'),
                            'type' => 'text',
                        ),
                        'EmailID' => array(
                            'title' => __('Email', 'matdespatch'),
                            'type' => 'email',
                        ),
                        'DispatchAddress' => array(
                            'title' => __('Address', 'matdespatch'),
                            'type' => 'textarea',
                        ),
                        'PostalCode' => array(
                            'title' => __('Postcode', 'matdespatch'),
                            'type' => 'text',
                        ),
                        'City' => array(
                            'title' => __('City', 'matdespatch'),
                            'type' => 'text',
                        ),
                        'State' => array(
                            'title' => __('State', 'matdespatch'),
                            'type' => 'text',
                        ),
                        'Country' => array(
                            'title' => __('Country', 'matdespatch'),
                            'type' => 'select',
                            'options' => array(
                                'MY' => 'Malaysia',
                            ), // array of options for select/multiselects only
                        ),
                        'title3' => array(
                            'title' => __( 'Shipment Settings', 'matdespatch' ),
                            'type' => 'title',
                            'id' => 'wc_settings_matdespatch_shipment'
                        ),
                        'PickupDay' => array(
                            'title' => __('Day', 'matdespatch'),
                            'type' => 'select',
                            'default' => '0',
                            'options' => array(
                                '0' => 'Same Day',
                                '1' => '+1 Day',
                                '2' => '+2 Day',
                                '3' => '+3 Day',
                            ), // array of options for select/multiselects only
                        ),
                        'PickupTime' => array(
                            'title' => __('Time', 'matdespatch'),
                            'type' => 'select',
                            'options' => array(
                                '09:00' => '09:00',
                                '10:00' => '10:00',
                                '11:00' => '11:00',
                                '12:00' => '12:00',
                                '13:00' => '13:00',
                                '14:00' => '14:00',
                                '15:00' => '15:00',
                                '16:00' => '16:00',
                                '17:00' => '17:00',
                                '18:00' => '18:00',
                                '19:00' => '19:00',
                                '20:00' => '20:00',
                                '21:00' => '21:00',
                                '22:00' => '22:00',
                                '23:00' => '23:00',
                                '24:00' => '24:00',
                            ), // array of options for select/multiselects only
                        ),
                        'Fulfilment' => array(
                          'title' => __( 'Fulfillment Settings', 'matdespatch' ),
                          'type' => 'title',
                          'id' => 'matdespatch_fulfillment',
                        ),
                        'Automatic' => array(
                            'title' => __('Automatic Fulfillment', 'matdespatch'),
                            'id'       	=> 'matdespatch_automatic_fulfillment',
                            'desc'  	=> __( 'Enable or disable automatic fulfillment', 'matdespatch' ),
                            'type'     	=> 'checkbox',
                            'default'	=> 'yes',
                        ),
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
                        ),
                        'FlatRate' => array(
                            'title' => __('Flat Rate', 'matdespatch'),
                            'type' => 'text',
                            'default' => __('0', 'matdespatch')
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
                    global $woocommerce;
                    global $wpdb;
                    $TableName = $wpdb->prefix.'matdespatch';
                    $result = $wpdb->get_results("SELECT * FROM $TableName WHERE id = 1");
                    if (isset($result[0])) {
                        $Shop = $result[0];
                    } else {
                        echo 'Failed to initialize. Please install plugin again.';

                        return;
                    }

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

                    if (date('w') == 0) {
                        $SendDate = date('Y-m-d', strtotime('+1 day')).'T20:30:00.000Z';
                    } elseif (date('w') == 6) {
                        $SendDate = date('Y-m-d', strtotime('+2 day')).'T20:30:00.000Z';
                    } else {
                        $SendDate = date('Y-m-d', strtotime('+0 day')).'T20:30:00.000Z';
                    }
                    $shippingAddress = '';
                    if ($woocommerce->customer->get_shipping_address() == null || $woocommerce->customer->get_shipping_address() == '') {
                        $shippingAddress = 'Some House, Some Area ';
                    } else {
                        $shippingAddress = $woocommerce->customer->get_shipping_address();
                    }

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, MATDESPATCH_PRICE_QUOTE);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HEADER, false);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, '{
                      "sender_address": "'.$this->settings['DispatchAddress'].'",
                      "sender_postcode": "'.$this->settings['PostalCode'].'",
                      "sender_country": "'.$this->settings['Country'].'",
                      "receiver_address": "'.$shippingAddress.'",
                      "receiver_postcode": "'.$woocommerce->customer->get_shipping_postcode().'",
                      "receiver_country": "'.$woocommerce->customer->get_shipping_country().'",
                      "item_weight": '.$Grams.',
                      "item_type": "PARCEL",
                      "shipment_date": "'.$SendDate.'"
                    }');
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        'Content-Type: application/json',
                    ));
                    curl_setopt($ch, CURLOPT_USERPWD, get_option('matdespatch_id').':'.get_option('matdespatch_api'));
                    $results = curl_exec($ch);
                    curl_close($ch);
                    $results = json_decode($results, true);
                    foreach ($results as $shipper) {
                        if (isset($shipper['name'])) {
                            $percentRate = $this->settings['PercentageRate'] / 100 * $shipper['price'];

                            $rate = array(
                                'id' => $shipper['service_code'],
                                'label' => $shipper['name'],
                                'cost' => round($shipper['price'] + $percentRate + $this->settings['FlatRate'], 2),
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
    function AddShippingMethod($methods)
    {
        $methods['your_shipping_method'] = 'MatDespatchShippingMethod';

        return $methods;
    }
}
