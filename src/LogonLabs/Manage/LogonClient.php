<?php

namespace LogonLabs\Manage;

use \Exception as Exception;
use LogonLabs\Manage\API\API as API;
use LogonLabs\Manage\API\Connection as Connection;

/*
 *  LogonLabs Management API Client
 */

class LogonClient {

    private $request;

    private $api_request;

    private $api_path = 'https://manage.logonlabs.com/';
    private $gateway_id = 'c0d4e56d-3dbc-4b49-b0c9-bcb415656814';
    private $username;
    private $password;

    const SOCIAL = 'social';
    const ENTERPRISE = 'enterprise';

    /*
     *  Configure API client with required settings
     *  $settins array will required the following keys
     *
     */

    public function __construct($settings) {
        if (!isset($settings['username'])) {
            throw new Exception("'username' must be provided");
        }
        if (!isset($settings['password'])) {
            throw new Exception("'password' must be provided");
        }

        $this->username = $settings['username'];
        $this->password = $settings['password'];

        if (isset($settings['api_path'])) {
            if (substr($settings['api_path'], -1) != '/') {
                $settings['api_path'] .= '/';
            }
            $this->api_path = $settings['api_path'];
        }

        if (isset($settings['gateway_id'])) {
            $this->gateway_id = $settings['gateway_id'];
        }
    }

    private function connection() {
        if (!$this->request) {
            $this->request = new Connection($this->api_path, $this->username, $this->password);
        }
        return $this->request;
    }

    private function api() {
        if (!$this->api_request) {
            $connection = $this->connection();
            $this->api_request = new API($connection);
        }
        return $this->api_request;
    }

    private function parsePagingOptions($options = array()) {
        $optional = array('page', 'page_size');
        $data = array();
        foreach($optional as $key) {
            if (isset($options[$key])) {
                $data[$key] = $options[$key];
            }
        }
        return $data;
    }

    public function getProfile() {
        return $this->api()->getProfile();
    }

    //App Management

    public function getApps($options = array()) {
        $data = $this->parsePagingOptions($options);
        return $this->api()->getApps($data);
    }

    public function getApp($app_id) {
        return $this->api()->getApp($app_id);
    }

    public function createApp($options) {
        $gateway_id = isset($options['gateway_id']) ? $options['gateway_id'] : $this->gateway_id;
        $data = array(
            'name' => $options['name'],
            'gateway_id' => $gateway_id
        );
        return $this->api()->createApp($data);
    }

    public function updateApp($app_id, $options) {
        return $this->api()->updateApp($app_id, $options);
    }

    public function removeApp($app_id) {
        return $this->api()->removeApp($app_id);
    }

    //User Management

    public function getAppUsers($app_id, $options = array()) {
        $data = $this->parsePagingOptions($options);
        return $this->api()->getAppUsers($app_id, $data);
    }

    public function addAppUser($app_id, $options) {
        return $this->api()->addAppUser($app_id, $options);
    }

    public function updateAppUser($app_id, $user_id, $options) {
        return $this->api()->updateAppUser($app_id, $user_id, $options);
    }

    public function removeAppUser($app_id, $user_id) {
        return $this->api()->removeAppUser($app_id, $user_id);
    }

    //App Secret Management

    public function getAppSecrets($app_id, $options = array()) {
        $data = $this->parsePagingOptions($options);
        return $this->api()->getAppSecrets($app_id, $data);
    }

    public function createAppSecret($app_id) {
        return $this->api()->createAppSecret($app_id);
    }

    public function removeAppSecret($app_id, $secret_id) {
        return $this->api()->removeAppSecret($app_id, $secret_id);
    }

    //User Secret Management

    public function getUserSecrets($user_id, $options = array()) {
        $data = $this->parsePagingOptions($options);
        return $this->api()->getUserSecrets($user_id, $data);
    }

    public function createUserSecret($user_id) {
        return $this->api()->createUserSecret($user_id);
    }

    public function removeUserSecret($user_id, $secret_id) {
        return $this->api()->removeUserSecret($user_id, $secret_id);
    }

    //User Secret Assignment

    public function getUserSecretApps($user_id, $secret_id, $options = array()) {
        $data = $this->parsePagingOptions($options);
        return $this->api()->getUserSecretApps($user_id, $secret_id, $data);
    }

    public function assignUserSecret($user_id, $secret_id, $app_id) {
        return $this->api()->assignUserSecret($user_id, $secret_id, $app_id);
    }

    public function unassignUserSecret($user_id, $secret_id, $app_id) {
        return $this->api()->unassignUserSecret($user_id, $secret_id, $app_id);
    }


    //Provider Management

    public function createSocialProvider($options) {
        $options['type'] = self::SOCIAL;
        return $this->api()->createProvider($options);
    }

    public function createEnterpriseProvider($options) {
        $options['type'] = self::ENTERPRISE;
        return $this->api()->createProvider($options);
    }

    public function getProviderDetails($identity_provider_id) {
        return $this->api()->getProviderDetails($identity_provider_id);
    }

    public function getProviders($options = array()) {
        $data = $this->parsePagingOptions($options);
        return $this->api()->getProviders($data);
    }


    public function updateProvider($identity_provider_id, $options) {
        return $this->api()->updateProvider($identity_provider_id, $options);
    }

    public function removeProvider($identity_provider_id) {
        return $this->api()->removeProvider($identity_provider_id);
    }

    public function shareProvider($identity_provider_id, $user_id) {
        return $this->api()->shareProvider($identity_provider_id, $user_id);
    }

    public function unshareProvider($identity_provider_id, $user_id) {
        return $this->api()->unshareProvider($identity_provider_id, $user_id);
    }

    //Provider Assignment

    public function getAppProviders($app_id, $options = array()) {
        $data = $this->parsePagingOptions($options);
        return $this->api()->getAppProviders($app_id, $data);
    }

    public function assignProvider($identity_provider_id, $app_id) {
        return $this->api()->assignProvider($identity_provider_id, $app_id);
    }

    public function unassignProvider($identity_provider_id, $app_id) {
        return $this->api()->unassignProvider($identity_provider_id, $app_id);
    }

    public function enableAppProvider($app_id, $identity_provider_id) {
        return $this->api()->enableAppProvider($app_id, $identity_provider_id);
    }

    public function disableAppProvider($app_id, $identity_provider_id) {
        return $this->api()->disableAppProvider($app_id, $identity_provider_id);
    }
}