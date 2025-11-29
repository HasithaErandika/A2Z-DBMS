<?php
require_once 'src/core/Controller.php';

class HomeController extends Controller {
    public function index() {
        include_once "src/views/home/index.php";
    }
}