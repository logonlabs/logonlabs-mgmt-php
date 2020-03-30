<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/LogonLabsTest.php';
$config = parse_ini_file('config.ini');

if (empty($config['username']) || empty($config['password']) ) {
    echo "\nMissing parameters in the config.ini\n";
    echo "Please put in username and password in the config.ini.\n\n";
}

# ./vendor/bin/phpunit --bootstrap tests/bootstrap.php tests/LogonLabs
/*
PHPUnit 7.5.20 by Sebastian Bergmann and contributors.

.........................                                         25 / 25 (100%)

Time: 19.51 seconds, Memory: 4.00 MB

OK (25 tests, 240 assertions)
*/