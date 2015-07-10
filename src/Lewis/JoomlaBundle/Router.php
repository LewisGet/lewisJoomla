<?php

namespace Lewis\JoomlaBundle;

class Router extends \Lewis\Router\Router
{
    /**
     * @var \Lewis\JoomlaBundle\Config
     */
    public $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * return match controller, by config
     *
     * @return object
     */
    public function match()
    {
        jimport('joomla.environment.request');

        $task = \JRequest::getCmd("task");

        $tasks = explode(".", $task);

        $namespace = implode("\\", $tasks);

        $requestClass = $tasks[0];
        $requestDoing = $tasks[1];
        $requestDoing = ucfirst($requestDoing);

        if ("template" == $requestClass)
        {
            $namespace = "\\Lewis\\JoomlaBundle\\Controller\\TemplateController";

            $controller = new $namespace();

            return $controller->execute($requestDoing);
        }

        if (! $this->canRequest($tasks))
        {
            return call_user_func("\\Lewis\\JoomlaBundle\\NotFoundController::execute");
        }

        if (! property_exists($namespace, "execute"))
        {
            $namespace = "\\Lewis\\JoomlaBundle\\Controller\\Default{$requestDoing}Controller";
        }

        $controller = new $namespace($tasks[0], $tasks[1]);

        return $controller->execute();
    }

    /**
     * check task is register in config
     *
     * @param $tasks
     * @return bool
     */
    public function canRequest($tasks)
    {
        $requestClass = $tasks[0];
        $requestDoing = $tasks[1];

        $functions = $this->config->getTableFunctions($requestClass);

        if (! in_array($requestDoing, $functions))
        {
            return false;
        }

        return true;
    }
}
