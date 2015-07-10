<?php

namespace Lewis\JoomlaBundle\Controller;

use Lewis\Di\Container;
use Lewis\JoomlaBundle\Helper;

class BaseController
{
    /**
     * @var \JApplicationCms
     */
    public $app;

    /**
     * @var \JDatabaseDriverMysqli
     */
    public $db;

    /**
     * @var \Lewis\JoomlaBundle\Config
     */
    public $config;

    /**
     * @var \Lewis\Di\Container
     */
    public $container;

    /**
     * @var \Lewis\JoomlaBundle\Helper
     */
    public $helper;

    /**
     * @var string
     */
    public $tableName;

    /**
     * @var string
     */
    public $action;

    public function __construct($tableName, $action)
    {
        $this->tableName = $tableName;
        $this->action    = $action;

        $this->app = \JFactory::getApplication();
        $this->container = new Container();

        $this->db = $this->container->get("db");
        $this->config = $this->container->get("config");

        $this->container->set("tableName", $this->tableName);
        $this->container->set("action", $this->action);

        $this->helper = new Helper();
    }

    public function jsonRender($value)
    {
        $app = \JFactory::getApplication();
        $doc = $app->getDocument();

        // show component only
        $app->input->set("tmpl", "component");

        // Set the MIME type for JSON output.
        $doc->setMimeEncoding('application/json');

        // Change the suggested filename.
        \JResponse::setHeader('Content-Disposition','attachment;filename="' . $this->tableName . '.json"');

        // Output the JSON data.
        return json_encode($value);
    }
}