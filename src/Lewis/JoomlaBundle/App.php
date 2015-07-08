<?php

namespace Lewis\JoomlaBundle;

use Lewis\TableCreater\TableCreater;

class App
{
    /**
     * @var \Lewis\JoomlaBundle\Config
     */
    public $config;

    public $jconfig;

    public $appRootPath;

    public $projectName;

    public $db;

    public function __construct($appRoot, $projectName, $config, $checkDatabase = false)
    {
        $this->appRootPath = $appRoot;
        $this->projectName = $projectName;

        $this->jconfig = new \JConfig();
        $this->setConfig($config);

        $this->db = \JFactory::getDbo();

        if ($checkDatabase)
        {
            $tableCreater = new TableCreater($this->db, $this->jconfig->db, $this->jconfig->dbprefix);

            $tableCreater->createTables($this->config->columns);
        }
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
