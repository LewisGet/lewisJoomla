<?php

namespace Lewis\JoomlaBundle;

class App
{
    /**
     * @var \Lewis\JoomlaBundle\Config
     */
    public $config;

    public $appRootPath;

    public $projectName;

    public function __construct($appRoot, $projectName)
    {
        $this->appRootPath = $appRoot;
        $this->projectName = $projectName;
    }

    public function execute()
    {
        $router = new Router($this->config);

        return $router->match();
    }

    public function setConfig($config)
    {
        $this->config = new Config($config);
    }
}
