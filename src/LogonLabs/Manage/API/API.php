<?php
/**
 * Created by PhpStorm.
 * User: hlee
 * Date: 2019-02-25
 * Time: 2:17 PM
 */

namespace LogonLabs\Manage\API;


class API {

    private $connection;

    private $options;

    const ROUTE_APPS = 'apps';
    const ROUTE_USERS = 'users';
    const ROUTE_PROVIDERS = 'providers';
    const ROUTE_DETAILS = 'details';
    const ROUTE_SECRETS = 'secrets';
    const ROUTE_PROFILE = 'profile';

    public function __construct($connection, $options = array()) {
        if (!$this->connection) {
            $this->connection = $connection;
        }
        $this->options = $options;
    }

    public function getProfile() {
        $cmd = self::ROUTE_PROFILE;
        return $this->connection->get($cmd);
    }

    //App Management

    public function getApps($data) {
        $cmd = self::ROUTE_APPS;
        return $this->connection->get($cmd, $data);
    }

    public function getApp($app_id) {
        $cmd = self::ROUTE_APPS . '/' . $app_id;
        return $this->connection->get($cmd);
    }

    public function createApp($data) {
        $cmd = self::ROUTE_APPS;
        return $this->connection->post($cmd, $data);
    }

    public function updateApp($app_id, $data) {
        $cmd = self::ROUTE_APPS . '/' . $app_id;
        return $this->connection->patch($cmd, $data);
    }

    public function removeApp($app_id) {
        $cmd = self::ROUTE_APPS . '/' . $app_id;
        return $this->connection->delete($cmd);
    }

    //User Management

    public function getAppUsers($app_id, $data) {
        $cmd = self::ROUTE_APPS . '/' . $app_id . '/' . self::ROUTE_USERS;
        return $this->connection->get($cmd, $data);
    }

    public function addAppUser($app_id, $data) {
        $cmd = self::ROUTE_APPS . '/' . $app_id . '/' . self::ROUTE_USERS;
        return $this->connection->post($cmd, $data);
    }

    public function updateAppUser($app_id, $user_id, $data) {
        $cmd = self::ROUTE_APPS . '/' . $app_id . '/' . self::ROUTE_USERS . '/' . $user_id;
        return $this->connection->patch($cmd, $data);
    }

    public function removeAppUser($app_id, $user_id) {
        $cmd = self::ROUTE_APPS . '/' . $app_id . '/' . self::ROUTE_USERS . '/' . $user_id;
        return $this->connection->delete($cmd);
    }

    //App Secret Management

    public function createAppSecret($app_id) {
        $cmd = self::ROUTE_APPS . '/' . $app_id . '/' . self::ROUTE_SECRETS;
        return $this->connection->post($cmd);
    }

    public function getAppSecrets($app_id, $data) {
        $cmd = self::ROUTE_APPS . '/' . $app_id . '/' . self::ROUTE_SECRETS;
        return $this->connection->get($cmd, $data);
    }

    public function removeAppSecret($app_id, $secret_id) {
        $cmd = self::ROUTE_APPS . '/' . $app_id . '/' . self::ROUTE_SECRETS . '/' . $secret_id;
        return $this->connection->delete($cmd);
    }

    //User Secret Management

    public function createUserSecret($user_id) {
        $cmd = self::ROUTE_USERS . '/' . $user_id . '/' . self::ROUTE_SECRETS;
        return $this->connection->post($cmd);
    }

    public function getUserSecrets($user_id, $data) {
        $cmd = self::ROUTE_USERS . '/' . $user_id . '/' . self::ROUTE_SECRETS;
        return $this->connection->get($cmd, $data);
    }

    public function removeUserSecret($user_id, $secret_id) {
        $cmd = self::ROUTE_USERS . '/' . $user_id . '/' . self::ROUTE_SECRETS . '/' . $secret_id;
        return $this->connection->delete($cmd);
    }

    //User Secret Assignment

    public function getUserSecretApps($user_id, $secret_id, $data) {
        $cmd = self::ROUTE_USERS . '/' . $user_id . '/' . self::ROUTE_SECRETS . '/' . $secret_id . '/' . self::ROUTE_APPS;
        return $this->connection->get($cmd, $data);
    }

    public function assignUserSecret($user_id, $secret_id, $app_id) {
        $cmd = self::ROUTE_USERS . '/' . $user_id . '/' . self::ROUTE_SECRETS . '/' . $secret_id . '/' . self::ROUTE_APPS . '/' . $app_id;
        return $this->connection->post($cmd);
    }

    public function unassignUserSecret($user_id, $secret_id, $app_id) {
        $cmd = self::ROUTE_USERS . '/' . $user_id . '/' . self::ROUTE_SECRETS . '/' . $secret_id . '/' . self::ROUTE_APPS . '/' . $app_id;
        return $this->connection->delete($cmd);
    }
    //Provider Management

    public function createProvider($data) {
        $cmd = self::ROUTE_PROVIDERS;
        return $this->connection->post($cmd, $data);
    }

    public function getProviderDetails($identity_provider_id) {
        $cmd = self::ROUTE_PROVIDERS . '/' . $identity_provider_id . '/' . self::ROUTE_DETAILS;
        return $this->connection->get($cmd);
    }

    public function getProviders($data) {
        $cmd = self::ROUTE_PROVIDERS;
        return $this->connection->get($cmd, $data);
    }

    public function updateProvider($identity_provider_id, $data) {
        $cmd = self::ROUTE_PROVIDERS . '/' . $identity_provider_id;
        return $this->connection->patch($cmd, $data);
    }

    public function removeProvider($identity_provider_id) {
        $cmd = self::ROUTE_PROVIDERS . '/' . $identity_provider_id;
        return $this->connection->delete($cmd);
    }

    public function shareProvider($identity_provider_id, $user_id) {
        $cmd = self::ROUTE_PROVIDERS . '/' . $identity_provider_id . '/' . self::ROUTE_USERS . '/' . $user_id;
        return $this->connection->post($cmd);
    }

    public function unshareProvider($identity_provider_id, $user_id) {
        $cmd = self::ROUTE_PROVIDERS . '/' . $identity_provider_id . '/' . self::ROUTE_USERS . '/' . $user_id;
        return $this->connection->delete($cmd);
    }

    //Provider Assignment

    public function getAppProviders($app_id, $data) {
        $cmd = self::ROUTE_APPS . '/' . $app_id . '/' . self::ROUTE_PROVIDERS;
        return $this->connection->get($cmd, $data);
    }

    public function assignProvider($identity_provider_id, $app_id) {
        $cmd = self::ROUTE_PROVIDERS . '/' . $identity_provider_id . '/' . self::ROUTE_APPS . '/' . $app_id;
        return $this->connection->post($cmd);
    }

    public function unassignProvider($identity_provider_id, $app_id) {
        $cmd = self::ROUTE_PROVIDERS . '/' . $identity_provider_id . '/' . self::ROUTE_APPS . '/' . $app_id;
        return $this->connection->delete($cmd);
    }

    public function enableAppProvider($app_id, $identity_provider_id) {
        $cmd = self::ROUTE_APPS . '/' . $app_id . '/' . self::ROUTE_PROVIDERS . '/' . $identity_provider_id;
        return $this->connection->post($cmd);
    }

    public function disableAppProvider($app_id, $identity_provider_id) {
        $cmd = self::ROUTE_APPS . '/' . $app_id . '/' . self::ROUTE_PROVIDERS . '/' . $identity_provider_id;
        return $this->connection->delete($cmd);
    }
}