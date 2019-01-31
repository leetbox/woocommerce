<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
register_activation_hook('matdespatch/matdespatch.php', 'matdespatchPluginActivated');
register_uninstall_hook('matdespatch/matdespatch.php', 'matdespatchPluginUninstalled');
add_filter('parse_request', 'matdespatchRequest');

function matdespatchPluginActivated() {

}

Function matdespatchPluginUninstalled() {
    delete_option('matdespatch_user_id');
    delete_option('matdespatch_api_key');
    delete_option('matdespatch_integration_id');
}

function matdespatchRequest() {
    if ($_GET['matdespatch'] == 'plugin_check') {
        header('Content-Type: application/json');

        die(json_encode([
            'url' => get_home_url(),
            'version' => MATDESPATCH_PLUGIN_VERSION,
        ], JSON_UNESCAPED_SLASHES));

    } else if ($_GET['matdespatch'] == 'plugin_install') {
        header('Content-Type: application/json');

        if (!$_GET['timestamp'] || !$_GET['hmac'] || !$_GET['user_id']) {
            die(json_encode(['error' => 'failed']));
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, MATDESPATCH_API_ENDPOINT . '/api/integration/woocommerce_install');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'shop' => $_GET['shop'],
            'timestamp' => $_GET['timestamp'],
            'hmac' => $_GET['hmac'],
            'user_id' => $_GET['user_id']
        ]);
        $results = json_decode(curl_exec($ch));
        curl_close($ch);

        if (json_last_error() === JSON_ERROR_NONE && $results->integration_id) {
            update_option('matdespatch_integration_id', $results->integration_id);
            update_option('matdespatch_api_key', $results->api_key);
            update_option('matdespatch_user_id', $results->user_id);

            die(json_encode([
                'integration_id' => $results->integration_id,
                'api_key' => $results->api_key,
                'user_id' => $results->user_id
            ]));
        } else {
            die(json_encode(['error' => $results->integration_id]));
        }
    }
}
