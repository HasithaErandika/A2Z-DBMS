<?php
require_once 'src/core/Controller.php';

class ErrorController extends Controller {
    public function notFound() {
        $data = [
            'errorCode' => '404',
            'errorMessage' => 'The page you are looking for does not exist.'
        ];
        $this->render('errors/error', $data); // Uses render(), not view()
    }
}