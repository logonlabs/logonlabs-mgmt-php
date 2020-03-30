<?php

namespace LogonLabs\Manage\API;

use LogonLabs\Manage\API\Response as Response;
use Requests;

/*
 *  Logon Labs API Client
 */

class Connection {
    private $api_url = 'https://manage.logonlabs.com/';
    private $username = false;
    private $password = false;

    private $headers = array();
    private $options = array();

    const JSON = 'application/json';
    const URLENCODED = 'application/x-www-form-urlencoded';

    public function __construct($api_path, $username, $password) {
        if ($api_path) {
            $this->api_url = $api_path;
        }

        if ($username) {
            $this->username = $username;
        }

        if ($password) {
            $this->password = $password;
        }
    }

    public function post($cmd, $data = false, $headers = array()) {
        $url = $this->api_url . $cmd;

        $this->initCall();
        $this->applyHeaders($headers);
        $this->headers['Content-Type'] = self::JSON;

        if ($data) {
            $post_string = json_encode($data);
        } else {
            $post_string = false;
        }

        $response = Requests::post($url, $this->headers, $post_string, $this->options);

        return $this->handleResponse($response);
    }

    public function patch($cmd, $data = false, $headers = array()) {
        $url = $this->api_url . $cmd;

        $this->initCall();
        $this->applyHeaders($headers);
        $this->headers['Content-Type'] = self::JSON;

        if ($data) {
            $post_string = json_encode($data);
        } else {
            $post_string = false;
        }

        $response = Requests::patch($url, $this->headers, $post_string, $this->options);

        return $this->handleResponse($response);
    }

    public function get($cmd, $query = false, $headers = array()) {
        $url = $this->api_url . $cmd;

        $this->initCall();
        $this->applyHeaders($headers);

        if (is_array($query)) {
            $url = sprintf("%s?%s", $url, http_build_query($query));
        }

        $response = Requests::get($url, $this->headers, $this->options);

        return $this->handleResponse($response);
    }

    public function delete($cmd, $headers = array()) {
        $url = $this->api_url . $cmd;

        $this->initCall();
        $this->applyHeaders($headers);

        $response = Requests::delete($url, $this->headers, $this->options);

        return $this->handleResponse($response);
    }

    private function handleResponse($response) {
        try {
            $body = json_decode($response->body, true);
        } catch(Exception $ex) {
            $body = $response->body;
        }

        $res = array(
            'status' => $response->status_code,
            'body' => $body
        );

        return new Response($res);
    }


    private function initCall() {
        $this->headers = array();
        $this->options = array();

        $this->headers[] = 'Accept: ' . self::JSON;
        $this->options['auth'] = array($this->username, $this->password);
    }

    private function applyHeaders($headers = array()) {
        foreach($headers as $key => $value) {
            $this->headers[$key] = $value;
        }
    }
}