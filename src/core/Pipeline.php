<?php
// src/core/Pipeline.php

namespace App\Core;

use App\Helpers\Request;
use App\Helpers\Response;

class Pipeline {
    private $middleware = [];

    public function send(Request $request, Response $response) {
        return new class($request, $response, $this->middleware) {
            private $request;
            private $response;
            private $middleware;

            public function __construct($request, $response, $middleware) {
                $this->request = $request;
                $this->response = $response;
                $this->middleware = $middleware;
            }

            public function through($middleware) {
                $this->middleware = is_array($middleware) ? $middleware : func_get_args();
                return $this;
            }

            public function then(callable $destination) {
                $pipeline = array_reduce(
                    array_reverse($this->middleware),
                    function ($stack, $pipe) {
                        return function ($passableReq, $passableRes) use ($stack, $pipe) {
                            $middlewareInstance = new $pipe();
                            return $middlewareInstance->handle($passableReq, $passableRes, $stack);
                        };
                    },
                    function ($passableReq, $passableRes) use ($destination) {
                        return $destination($passableReq, $passableRes);
                    }
                );

                return $pipeline($this->request, $this->response);
            }
        };
    }
}
