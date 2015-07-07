<?php

namespace Lewis\JoomlaBundle;

class App
{
    public function execute()
    {
        $router = new Router();

        return $router->match();
    }
}
