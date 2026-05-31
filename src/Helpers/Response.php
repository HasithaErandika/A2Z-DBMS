<?php
// src/helpers/Response.php

namespace App\Helpers;

class Response {
    public function json($data, $statusCode = 200) {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }

    public function redirect($url) {
        header("Location: " . $url);
        exit;
    }
}
