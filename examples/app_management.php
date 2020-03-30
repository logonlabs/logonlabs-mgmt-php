<?php

include_once __DIR__ . '/../vendor/autoload.php';
include_once "templates/base.php";
$config = parse_ini_file('config.ini');

echo pageHeader("App Management", "index");

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

$result_1 = $logon->createApp(array('name' => 'testing_app_management_1'))->getBody();
$app_id = $result_1['app_id'];

$result_2 = $logon->getApp($app_id)->getBody();

$result_3 = $logon->updateApp($app_id, array("name"=>"testing_app_update"))->getBody();
$result_4 = $logon->getApp($app_id)->getBody();

$result_5 = $logon->removeApp($app_id)->getBody();
$result_6 = $logon->getApps()->getBody();
?>


<?php echo apiResult($result_1, 'createApp'); ?>
<?php echo apiResult($result_2, 'getApp'); ?>
<?php echo apiResult($result_3, 'updateApp'); ?>
<?php echo apiResult($result_4, 'getApp'); ?>
<?php echo apiResult($result_5, 'removeApp'); ?>
<?php echo apiResult($result_6, 'getApps'); ?>

<?php echo pageFooter(__FILE__) ?>
