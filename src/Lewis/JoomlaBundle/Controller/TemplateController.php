<?php

namespace Lewis\JoomlaBundle\Controller;

use Lewis\Di\Container;

class TemplateController
{
    public $container;

    public function load($template)
    {
        $app = \JFactory::getApplication();
        $input  = $app->input;

        $this->container = new Container();

        $bundlePath = $this->container->get("bundlePath");
        $templatePath = $this->container->get("templatePath");
        $rootPath = $this->container->get("rootPath");

        $loadPath = "{$bundlePath}/Resource/Template/{$template}/index.php";
        $loadTemplatePath = "{$templatePath}/Resource/Template/{$template}/index.php";
        $loadRootPath = "{$rootPath}/media/Resource/Template/{$template}/index.php";

        if (file_exists($loadTemplatePath))
        {
            $loadPath = $loadTemplatePath;
        }

        if (file_exists($loadRootPath))
        {
            $loadPath = $loadRootPath;
        }

        if (! file_exists($loadPath))
        {
            return ;
        }

        require_once $loadPath;

        return ;
    }

    public function execute($template)
    {
        return $this->load($template);
    }
}
