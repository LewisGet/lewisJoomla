<?php

namespace Lewis\JoomlaBundle;

use \Lewis\Request\RequestInterface;

class Router extends \Lewis\Router\Router
{
    public function __construct()
    {
    }

    public function match()
    {
        jimport('joomla.environment.request');

        $task = \JRequest::getCmd("task");

        $tasks = explode(".", $task);

        $namespace = implode("\\", $tasks);

        if (! property_exists($namespace, "display"))
        {
            // controller not exist

            return call_user_func("\\Lewis\\JoomlaBundle\\DefaultController::display");
        }

        return call_user_func("{$namespace}::display");
    }
}