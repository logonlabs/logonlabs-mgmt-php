<?php

include_once __DIR__ . '/../vendor/autoload.php';
include_once "templates/base.php";
$config = parse_ini_file('config.ini');

echo pageHeader("User Secret Management/Assignment", "index");

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

$user = $logon->getProfile()->getBody();
$user_id = $user['user_id'];


$result_1 = $logon->createUserSecret($user_id)->getBody();

$secret_id = $result_1['secret_id'];

$result_2 = $logon->getUserSecrets($user_id)->getBody();

$result_2_1 = $logon->assignUserSecret($user_id, $secret_id, $app_id)->getBody();
$result_2_2 = $logon->getUserSecretApps($user_id, $secret_id)->getBody();
$result_2_3 = $logon->unassignUserSecret($user_id, $secret_id, $app_id)->getBody();
$result_2_4 = $logon->getUserSecretApps($user_id, $secret_id)->getBody();

$result_3 = $logon->removeUserSecret($user_id, $secret_id)->getBody();
$result_4 = $logon->getUserSecrets($user_id)->getBody();



?>


<?php echo apiResult($app, 'getApp'); ?>
<?php echo apiResult($user, 'getProfile'); ?>

<?php echo apiResult($result_1, 'createUserSecret'); ?>
<?php echo apiResult($result_2, 'getUserSecrets'); ?>

<?php echo apiResult($result_2_1, 'assignUserSecret'); ?>
<?php echo apiResult($result_2_2, 'getUserSecretApps'); ?>
<?php echo apiResult($result_2_3, 'unassignUserSecret'); ?>
<?php echo apiResult($result_2_4, 'getUserSecretApps'); ?>

<?php echo apiResult($result_3, 'removeUserSecret'); ?>
<?php echo apiResult($result_4, 'getUserSecrets'); ?>

<?php echo pageFooter(__FILE__) ?>
