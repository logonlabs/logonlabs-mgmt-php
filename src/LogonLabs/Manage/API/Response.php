<?php

namespace LogonLabs\Manage\API;
use LogonLabs\Manage\API\ResponseObject as ResponseObject;


class Response {

    private $body;
    private $status;
    private $object;

    public function __construct($options) {
        $this->body = $options['body'];
        $this->status = $options['status'];
        if ($this->success()) {
            $this->object = new ResponseObject($this->body);
        } else {
            $this->object = false;
        }
    }

    public function getBody() {
        return $this->body;
    }

    public function getObject() {
        return $this->object;
    }

    public function getStatus() {
        return $this->status;
    }

    public function success() {
        return $this->status == 200;
    }
}