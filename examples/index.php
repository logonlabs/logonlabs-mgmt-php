<?php

include_once __DIR__ . '/../vendor/autoload.php';
include_once "templates/base.php";
$config = parse_ini_file('config.ini');

echo pageHeader("LogonLabs Management API");

if (empty($config['username']) || empty($config['password']) ) {
    echo pageRequire();
    return;
}
?>

<div><a href ="app_management.php">App Management<a/></div>
<div><a href ="user_management.php">User Management<a/></div>
<div><a href ="app_secret_management.php">App Secret Management<a/></div>
<div><a href ="user_secret_management.php">User Secret Management/Assignment<a/></div>
<div><a href ="provider_management.php">Provider Management/Assignment<a/></div>

<?php echo pageFooter(__FILE__) ?>
