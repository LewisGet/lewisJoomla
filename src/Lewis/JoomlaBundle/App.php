<?php

namespace Lewis\JoomlaBundle;

use Lewis\Di\Container;
use Lewis\TableCreater\TableCreater;

class App
{
    /**
     * @var \Lewis\JoomlaBundle\Config
     */
    public $container;

    public $appRootPath;

    public $projectName;

    public function __construct($appRoot, $projectName, $config, $checkDatabase = false)
    {
        $this->container = new Container();

        $this->container->set("db", \JFactory::getDbo());
        $this->container->set("jconfig", new \JConfig());
        $this->container->set("appRootPath", $appRoot);
        $this->container->set("projectName", $projectName);
        $this->container->set("config", new Config($config));

        $db = $this->container->get("db");
        $config = $this->container->get("config");
        $jconfig = $this->container->get("jconfig");

        if ($checkDatabase)
        {
            $tableCreater = new TableCreater(
                $db,
                $jconfig->db,
                $jconfig->dbprefix
            );

            $tableCreater->createTables($config->columns);
        }
    }

    public function execute()
    {
        $this->container = new Container();

        $router = new Router($this->container->get("config"));

        return $router->match();
    }
}
