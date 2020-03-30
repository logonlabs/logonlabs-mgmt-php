<?php

include_once __DIR__ . '/../vendor/autoload.php';
include_once "templates/base.php";
$config = parse_ini_file('config.ini');

echo pageHeader("Provider Management/Assignment", "index");

if (empty($config['username']) || empty($config['password']) ) {
    echo pageRequire();
    return;
}

use LogonLabs\Manage\LogonClient as LogonClient;

$logon = new LogonClient(array(
    'username' => $config['username'],
    'password' => $config['password'],
    'api_path' => $config['api_path'],
    'gateway_id' => $config['gateway_id'],
));

$apps = $logon->getApps()->getBody();
$app = $apps['results'][0];
$app_id = $app['app_id'];

$users = $logon->getAppUsers($app_id)->getBody();
$user = $users['results'][1];
$user_id = $user['user_id'];

$result_1 = $logon->createSocialProvider(array(
    'identity_provider' => 'google',
    'protocol' => 'oauth',
    'name' => 'sample provider',
    'description' => 'descriptions',
    'client_id' => 'client_sample_id',
    'client_secret' => 'client_sample_secret'
))->getBody();

$identity_provider_id_1 = $result_1['identity_provider_id'];

$result_1_1 = $logon->getProviderDetails($identity_provider_id_1)->getBody();

$result_2 = $logon->createEnterpriseProvider(array(
    'identity_provider' => 'google',
    'protocol' => 'saml',
    'name' => 'google saml provider',
    'description' => 'descriptions',
    'client_id' => 'client_sample_id',
    'login_url' => 'http://www.example.com/google',
    'login_button_image_uri' => '',
    'login_icon_image_uri' => '',
    'login_background_hex_color' => '#000000',
    'login_text_hex_color' => '#ffffff'
))->getBody();

$identity_provider_id_2 = $result_2['identity_provider_id'];

$result_2_1 = $logon->getProviderDetails($identity_provider_id_2)->getBody();

$result_3 = $logon->getProviders()->getBody();

//share with user
//$result_3_1 = $logon->shareProvider($identity_provider_id_1, $user_id)->getBody();
//unshare with user
//$result_3_2 = $logon->unshareProvider($identity_provider_id_1, $user_id)->getBody();

$result_3_3 = $logon->assignProvider($identity_provider_id_2, $app_id)->getBody();
$result_3_4 = $logon->enableAppProvider($app_id, $identity_provider_id_2)->getBody();
$result_3_5 = $logon->getAppProviders($app_id)->getBody();
$result_3_6 = $logon->disableAppProvider($app_id, $identity_provider_id_2)->getBody();
$result_3_7 = $logon->unassignProvider($identity_provider_id_2, $app_id)->getBody();
$result_3_8 = $logon->getAppProviders($app_id)->getBody();

$result_4 = $logon->removeProvider($identity_provider_id_1)->getBody();
$result_5 = $logon->removeProvider($identity_provider_id_2)->getBody();
$result_6 = $logon->getProviders()->getBody();

?>

<?php echo apiResult($app, 'getApp'); ?>
<?php echo apiResult($user, 'getAppUser'); ?>

<?php echo apiResult($result_1, 'createSocialProvider'); ?>
<?php echo apiResult($result_1_1, 'getProviderDetails'); ?>
<?php echo apiResult($result_2, 'createEnterpriseProvider'); ?>
<?php echo apiResult($result_2_1, 'getProviderDetails'); ?>
<?php echo apiResult($result_3, 'getProviders'); ?>
<?php //echo apiResult($result_3_1, 'shareProvider'); ?>
<?php //echo apiResult($result_3_2, 'unshareProvider'); ?>
<?php echo apiResult($result_3_3, 'assignProvider'); ?>
<?php echo apiResult($result_3_4, 'enableAppProviderr'); ?>
<?php echo apiResult($result_3_5, 'getAppProviders'); ?>
<?php echo apiResult($result_3_6, 'disableAppProvider'); ?>
<?php echo apiResult($result_3_7, 'unassignProvider'); ?>
<?php echo apiResult($result_3_8, 'getAppProviders'); ?>

<?php echo apiResult($result_4, 'removeProvider'); ?>
<?php echo apiResult($result_5, 'removeProvider'); ?>
<?php echo apiResult($result_6, 'getProviders'); ?>

<?php echo pageFooter(__FILE__) ?>
