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

class UserSecretManagementTest extends LogonLabsTest {
    public $logon;
    public $app_id;
    public $user_id;

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
        $profile = $this->logon->getProfile()->getBody();
        $this->assertArrayNotHasKey('error', $profile);
        $this->assertIsIterable($profile);
        $this->assertNotEmpty($profile['user_id']);
        $this->user_id = $profile['user_id'];
    }

    /**
     * @depends testInitialize
     */
    public function testCreateUserSecret() {
        $secret = $this->logon->createUserSecret($this->user_id)->getBody();
        $this->assertArrayNotHasKey('error', $secret);
        $this->assertIsIterable($secret);
        $this->assertIsString($secret['secret_id']);
        return $secret['secret_id'];
    }

    /**
     * @depends testCreateUserSecret
     */
    public function testAssignUserSecret($secret_id) {
        $response = $this->logon
            ->assignUserSecret($this->user_id, $secret_id, $this->app_id)->getBody();
        $this->assertArrayNotHasKey('error', $response);
        $this->assertIsIterable($response);
        return $secret_id;
    }

    /**
     * @depends testAssignUserSecret
     */
    public function testGetUserSecretApps($secret_id) {
        $response = $this->logon
            ->getUserSecretApps($this->user_id, $secret_id)->getBody();
        $this->paginations($response);
        return $secret_id;
    }

    /**
     * @depends testGetUserSecretApps
     */
    public function testUnassignUserSecret($secret_id) {
        $response = $this->logon
            ->unassignUserSecret($this->user_id, $secret_id, $this->app_id)->getBody();
        $this->assertArrayNotHasKey('error', $response);
        $this->assertIsIterable($response);
        return $secret_id;
    }

    /**
     * @depends testUnassignUserSecret
     */
    public function testRemoveUserSecret($secret_id) {
        $response = $this->logon
            ->removeUserSecret($this->user_id, $secret_id)->getBody();
        $this->assertArrayNotHasKey('error', $response);
        $this->assertIsIterable($response);
        return $secret_id;
    }
}