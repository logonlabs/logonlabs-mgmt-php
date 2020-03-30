<?php

include_once __DIR__ . '/../vendor/autoload.php';
include_once "templates/base.php";
$config = parse_ini_file('config.ini');

echo pageHeader("App Secret Management", "index");

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

$result_1 = $logon->createAppSecret($app_id)->getBody();

$secret_id = $result_1['secret_id'];

$result_2 = $logon->getAppSecrets($app_id)->getBody();

$result_3 = $logon->removeAppSecret($app_id, $secret_id)->getBody();

$result_4 = $logon->getAppSecrets($app_id)->getBody();

?>


<?php echo apiResult($app, 'getApp'); ?>
<?php echo apiResult($result_1, 'createAppSecret'); ?>
<?php echo apiResult($result_2, 'getAppSecrets'); ?>
<?php echo apiResult($result_3, 'removeAppSecret'); ?>
<?php echo apiResult($result_4, 'getAppSecrets'); ?>

<?php echo pageFooter(__FILE__) ?>
