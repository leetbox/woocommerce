<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
register_activation_hook('delyva/delyva.php', 'delyvaPluginActivated');
register_uninstall_hook('delyva/delyva.php', 'delyvaPluginUninstalled');
add_filter('parse_request', 'delyvaRequest');

function delyvaPluginActivated() {

}

Function delyvaPluginUninstalled() {
    delete_option('delyva_user_id');
    delete_option('delyva_api_key');
    delete_option('delyva_integration_id');
}

function delyvaRequest() {
    if ($_GET['delyva'] == 'plugin_check') {
        header('Content-Type: application/json');

        die(json_encode([
            'url' => get_home_url(),
            'version' => DELYVA_PLUGIN_VERSION,
        ], JSON_UNESCAPED_SLASHES));

    } else if ($_GET['delyva'] == 'plugin_install') {
        header('Content-Type: application/json');

        if (!$_POST['integration_id'] || !$_POST['api_key'] || !$_POST['user_id']) {
            die(json_encode(['error' => 'failed']));
        }

        update_option('delyva_integration_id', $_POST['integration_id']);
        update_option('delyva_api_key', $_POST['api_key']);
        update_option('delyva_user_id', $_POST['user_id']);

        die(json_encode([
            'integration_id' => $_POST['integration_id'],
            'api_key' => $_POST['api_key'],
            'user_id' => $_POST['user_id']
        ]));
    }
}
