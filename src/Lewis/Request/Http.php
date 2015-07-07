<?php

namespace Lewis\Request;

class Http implements RequestInterface
{
    public $post;

    public $get;

    public $method;

    public $hostName;

    public $clientIp;

    public $uri;

    public function __construct()
    {
        $this->post     = $_POST;
        $this->get      = $_GET;
        $this->method   = $_SERVER['REQUEST_METHOD'];
        $this->hostName = $_SERVER['SERVER_ADDR'];
        $this->clientIp = $_SERVER['REMOTE_ADDR'];
        $this->uri      = $_SERVER['PATH_INFO'];
    }
}