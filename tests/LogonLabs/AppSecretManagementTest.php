<?php

use LogonLabs\Manage\LogonClient as LogonClient;

global $config;

if (empty($config['username']) || empty($config['password']) ) {
    $logon = false;
} else {
    $logon = new LogonClient(array(
        'username' => $config['username'],
        'password' => $config['password'],
        'api_path' => $config['api_path'],
        'gateway_id' => $config['gateway_id'],
    ));
}

class AppSecretManagementTest extends LogonLabsTest {
    public $logon;
    public $app_id;

    /**
     * @before
     */
    public function testInitialize() {
        global $logon;
        $this->logon = $logon;
        $apps = $this->logon->getApps()->getBody();
        $this->assertArrayNotHasKey('error', $apps);
        $this->assertIsIterable($apps);
        $this->assertNotEmpty($apps['results']);
        $app = $apps['results'][0];
        $this->app_id = $app['app_id'];
    }

    /**
     * @depends testInitialize
     */
    public function testCreateAppSecret() {
        $secret = $this->logon->createAppSecret($this->app_id)->getBody();
        $this->assertArrayNotHasKey('error', $secret);
        $this->assertIsIterable($secret);
        $this->assertIsString($secret['secret_id']);
        return $secret['secret_id'];
    }

    /**
     * @depends testCreateAppSecret
     */
    public function testGetAppSecrets($secret_id) {
        $secrets = $this->logon->getAppSecrets($this->app_id)->getBody();
        $this->paginations($secrets);
        return $secret_id;
    }

    /**
     * @depends testGetAppSecrets
     */
    public function testRemoveAppSecret($secret_id) {
        $response = $this->logon->removeAppSecret($this->app_id, $secret_id)->getBody();
        $this->assertArrayNotHasKey('error', $response);
        $this->assertIsIterable($response);
    }
}