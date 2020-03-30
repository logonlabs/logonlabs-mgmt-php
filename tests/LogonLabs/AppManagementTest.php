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

class AppManagementTest extends LogonLabsTest {
    public $logon;

    /**
     * @before
     */
    public function testInitialize() {
        global $logon;
        $this->logon = $logon;
        $apps = $this->logon->getApps()->getBody();
        $this->paginations($apps);
    }

    /**
     * @depends testInitialize
     */
    public function testCreateApp() {
        $app = $this->logon->createApp(array('name' => 'testing_app_management_1'))->getBody();
        $this->assertArrayNotHasKey('error', $app);
        $this->assertIsIterable($app);
        $this->assertIsString($app['app_id']);
        return $app['app_id'];
    }

    /**
     * @depends testCreateApp
     */
    public function testUpdateApp($app_id) {
        $app = $this->logon->updateApp($app_id, array("name" => "testing_app_update"))->getBody();
        $this->assertArrayNotHasKey('error', $app);
        $this->assertIsIterable($app);
        return $app_id;
    }

    /**
     * @depends testUpdateApp
     */
    public function testGetApp($app_id) {
        $app = $this->logon->getApp($app_id)->getBody();
        $this->assertArrayNotHasKey('error', $app);
        $this->assertIsIterable($app);
        $this->assertSame('testing_app_update', $app['name']);
        return $app_id;
    }

    /**
     * @depends testGetApp
     */
    public function testRemoveApp($app_id) {
        $response = $this->logon->removeApp($app_id)->getBody();
        $this->assertArrayNotHasKey('error', $response);
        $this->assertIsIterable($response);
    }

}