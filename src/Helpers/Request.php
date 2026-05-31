<?php
// src/helpers/Request.php

namespace App\Helpers;

class Request {
    private $get;
    private $post;
    private $server;

    public function __construct() {
        $this->get = $_GET;
        $this->post = $_POST;
        $this->server = $_SERVER;
    }

    public function getMethod() {
        return $this->server['REQUEST_METHOD'] ?? 'GET';
    }

    public function isPost() {
        return $this->getMethod() === 'POST';
    }

    public function isGet() {
        return $this->getMethod() === 'GET';
    }

    public function get($key, $default = null) {
        return $this->get[$key] ?? $default;
    }

    public function post($key, $default = null) {
        return $this->post[$key] ?? $default;
    }

    public function allPost() {
        return $this->post;
    }

    public function allGet() {
        return $this->get;
    }

    public function getUri() {
        return $this->server['REQUEST_URI'] ?? '';
    }
}
