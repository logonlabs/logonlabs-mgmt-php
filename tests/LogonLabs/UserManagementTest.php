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

class UserManagementTest extends LogonLabsTest {
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
    public function testAddAppUser() {
        $email = 'ccooollll+' . time() . '@gmail.com';

        $user = $this->logon->addAppUser($this->app_id, array(
            'email_address' => $email,
            'role' => 'administrator'
        ))->getBody();
        $this->assertArrayNotHasKey('error', $user);
        $this->assertIsIterable($user);
        $this->assertIsString($user['user_id']);
        return $user['user_id'];
    }

    /**
     * @depends testAddAppUser
     */
    public function testGetAppUsers($user_id) {
        $users = $this->logon->getAppUsers($this->app_id)->getBody();
        $this->paginations($users);
        return $user_id;
    }

    /**
     * @depends testGetAppUsers
     */
    public function testRemoveAppUser($user_id) {
        $response = $this->logon->removeAppUser($this->app_id, $user_id)->getBody();
        $this->assertArrayNotHasKey('error', $response);
        $this->assertIsIterable($response);
    }
}