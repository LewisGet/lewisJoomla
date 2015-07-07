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

    public function match()
    {
        jimport('joomla.environment.request');

        $task = \JRequest::getCmd("task");

        $tasks = explode(".", $task);

        $namespace = implode("\\", $tasks);

        if (! $this->canRequest($tasks))
        {
            return call_user_func("\\Lewis\\JoomlaBundle\\NotFoundController::execute");
        }

        if (! property_exists($namespace, "execute"))
        {
            // controller not exist
            $requestDoing = end($tasks);
            $requestDoing = ucfirst($requestDoing);

            return call_user_func("\\Lewis\\JoomlaBundle\\Controller\\Default{$requestDoing}Controller::execute", $tasks);
        }

        return call_user_func("{$namespace}::execute");
    }

    public function canRequest($tasks)
    {
        $requestClass = current($tasks);
        $requestDoing = end($tasks);

        $functions = $this->config->getTableFunctions($requestClass);

        if (! in_array($requestDoing, $functions))
        {
            return false;
        }

        return true;
    }
}
