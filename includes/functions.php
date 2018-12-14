<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
add_action('admin_post_matdespatch_call', 'CustomFormSubmited');
register_activation_hook('matdespatch/matdespatch.php', 'PluginActivated');
add_action('wp', 'checkSettingsConfigured');

function PluginActivated()
{
    global $wpdb;

    $TableName = $wpdb->prefix.'matdespatch';
    $Charset = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS `$TableName` (
	  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	  `ApiKey` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	  `UserID` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	  `FullName` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	  `BusinessName` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	  `PhoneNo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	  `EmailID` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	  `DispatchAddress` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	  `PostalCode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	  `City` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	  `Country` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	  `PickupDay` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	  `PickupTime` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	  PRIMARY KEY (`id`)
	) $Charset ";
    require_once ABSPATH.'wp-admin/includes/upgrade.php';
    dbDelta($sql);
    $result = $wpdb->get_results("SELECT * FROM $TableName WHERE id = 1");

    if (count($result) == 0) {
        $wpdb->insert(
            $TableName,
            array(
            'ApiKey' => '',
        )
        );
    }

    $TableName = $wpdb->prefix.'posts';
    $row = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$TableName' AND column_name = 'TrackingCode'");

    if (empty($row)) {
        $wpdb->query("ALTER TABLE `$TableName` ADD `TrackingCode` VARCHAR(255) NULL DEFAULT NULL;");
    }
}

function CustomFormSubmited()
{
    global $wpdb;
    $TableName = $wpdb->prefix.'matdespatch';
    $wpdb->update($TableName,
                  array(
        'ApiKey' => $_POST['ApiKey'],
        'UserID' => $_POST['UserID'],
        'FullName' => $_POST['FullName'],
        'BusinessName' => $_POST['BusinessName'],
        'PhoneNo' => $_POST['PhoneNo'],
        'EmailID' => $_POST['EmailID'],
        'DispatchAddress' => $_POST['DispatchAddress'],
        'PostalCode' => $_POST['PostalCode'],
        'City' => $_POST['City'],
        'Country' => $_POST['Country'],
        'PickupDay' => $_POST['PickupDay'],
        'PickupTime' => $_POST['PickupTime'],
    ), array('id' => 1));
    $url = admin_url('admin.php?page=matdespatch%2Fmain.php');
    wp_redirect($url);
}

function checkSettingsConfigured() {
    $settings = get_option( 'woocommerce_matdespatch_settings');

    if (strlen($settings['UserID']) < 24 || strlen($settings['ApiKey']) < 24) {
        add_action( 'admin_notices', 'unconfiguredNotice' );
    }
}

function unconfiguredNotice() {
    ?>
    <div class="notice notice-error">
        <p><?php _e( 'You didn\'t set Matdespatch.com user id and Api key plugin yet! <a href="' . admin_url('admin.php?page=wc-settings&tab=shipping&section=matdespatch') . '">Click here to configure</a>', 'matdespatch' ); ?></p>
    </div>
    <?php
}