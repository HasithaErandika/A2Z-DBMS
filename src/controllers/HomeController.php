<?php
require_once 'src/core/Controller.php';

class HomeController extends Controller {
    public function support() {
        $this->render('home/support');
    }
}