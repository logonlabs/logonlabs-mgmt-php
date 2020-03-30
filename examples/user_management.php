<?php

include_once __DIR__ . '/../vendor/autoload.php';
include_once "templates/base.php";
$config = parse_ini_file('config.ini');

echo pageHeader("User Management", "index");

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
$email = 'ccooollll+' . time() . '@gmail.com';

$result_1 = $logon->addAppUser($app_id, array(
    'email_address' => $email,
    'role' => 'administrator'
))->getBody();

$user_id = $result_1['user_id'];

$result_2 = $logon->getAppUsers($app_id)->getBody();
$result_5 = $logon->removeAppUser($app_id, $user_id)->getBody();
$result_6 = $logon->getAppUsers($app_id)->getBody();

?>


<?php echo apiResult($app, 'getApp'); ?>
<?php echo apiResult($result_1, 'addAppUser'); ?>
<?php echo apiResult($result_2, 'getAppUsers'); ?>
<?php //echo apiResult($result_3, 'updateAppUser'); ?>
<?php //echo apiResult($result_4, 'getAppUsers'); ?>
<?php echo apiResult($result_5, 'removeAppUser'); ?>
<?php echo apiResult($result_6, 'getAppUsers'); ?>

<?php echo pageFooter(__FILE__) ?>
