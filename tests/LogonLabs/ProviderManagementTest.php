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

class ProviderManagementTest extends LogonLabsTest {
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
        $users = $this->logon->getAppUsers($this->app_id)->getBody();
        $this->assertArrayNotHasKey('error', $users);
        $this->assertIsIterable($users);
        $this->assertNotEmpty($users['results']);
        $user = $users['results'][1];
        $this->user_id = $user['user_id'];
    }

    /**
     * @depends testInitialize
     */
    public function testCreateProviders() {
        $provider = $this->logon->createSocialProvider(array(
            'identity_provider' => 'google',
            'protocol' => 'oauth',
            'name' => 'sample provider',
            'description' => 'descriptions',
            'client_id' => 'client_sample_id',
            'client_secret' => 'client_sample_secret'
        ))->getBody();
        $this->assertArrayNotHasKey('error', $provider);
        $this->assertIsIterable($provider);
        $this->assertIsString($provider['identity_provider_id']);
        $data = array(
            'social_provider_id' => $provider['identity_provider_id']
        );
        $provider = $this->logon->createEnterpriseProvider(array(
            'identity_provider' => 'google',
            'protocol' => 'saml',
            'name' => 'google saml provider',
            'description' => 'descriptions',
            'client_id' => 'client_sample_id',
            'login_url' => 'http://www.example.com/google',
            'login_button_image_uri' => '',
            'login_icon_image_uri' => '',
            'login_background_hex_color' => '#000000',
            'login_text_hex_color' => '#ffffff'
        ))->getBody();
        $this->assertArrayNotHasKey('error', $provider);
        $this->assertIsIterable($provider);
        $this->assertIsString($provider['identity_provider_id']);
        $data['enterprise_provider_id'] = $provider['identity_provider_id'];
        return $data;
    }

    /**
     * @depends testCreateProviders
     */
    public function testGetProviderDetails($data) {
        $provider = $this->logon->getProviderDetails($data['social_provider_id'])->getBody();

        $this->assertArrayNotHasKey('error', $provider);
        $this->assertIsIterable($provider);
        return $data;
    }

    /**
     * @depends testGetProviderDetails
     */
    public function testProviderAssignments($data) {
        $provider_id = $data['enterprise_provider_id'];
        $response = $this->logon
            ->assignProvider($provider_id, $this->app_id)->getBody();
        $this->assertArrayNotHasKey('error', $response);
        $this->assertIsIterable($response);

        $response = $this->logon
            ->enableAppProvider($this->app_id, $provider_id)->getBody();
        $this->assertArrayNotHasKey('error', $response);
        $this->assertIsIterable($response);

        $response = $this->logon
            ->getAppProviders($this->app_id)->getBody();
        $this->paginations($response);

        $response = $this->logon
            ->disableAppProvider($this->app_id, $provider_id)->getBody();
        $this->assertArrayNotHasKey('error', $response);
        $this->assertIsIterable($response);

        $response = $this->logon
            ->unassignProvider($provider_id, $this->app_id)->getBody();
        $this->assertArrayNotHasKey('error', $response);
        $this->assertIsIterable($response);

        return $data;
    }

    /**
     * @depends testProviderAssignments
     */
    public function testRemoveProvider($data) {
        $enterprise_provider_id = $data['enterprise_provider_id'];
        $social_provider_id = $data['social_provider_id'];

        $response = $this->logon
            ->removeProvider($enterprise_provider_id)->getBody();
        $this->assertArrayNotHasKey('error', $response);
        $this->assertIsIterable($response);

        $response = $this->logon
            ->removeProvider($social_provider_id)->getBody();
        $this->assertArrayNotHasKey('error', $response);
        $this->assertIsIterable($response);
    }

    /**
     * @depends testRemoveProvider
     */
    public function testGetProviders() {
        $response = $this->logon
            ->getProviders()->getBody();
        $this->paginations($response);
    }
}