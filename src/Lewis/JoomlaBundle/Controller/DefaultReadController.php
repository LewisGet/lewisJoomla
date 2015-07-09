<?php

namespace Lewis\JoomlaBundle\Controller;

use Lewis\Di\Container;

class DefaultReadController
{
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

    public function getItems($tasks)
    {
        $app = \JFactory::getApplication();

        $this->container = new Container();
        $this->db = $this->container->get("db");
        $this->config = $this->container->get("config");

        $tableName = $tasks[0];

        $q = $this->db->getQuery(true);

        $select = array();
        $whereSql = array();

        foreach ($this->config->getTableReadColumns($tableName) as $columnName => $columnType)
        {
            $columnAlias = $tableName . ucfirst($columnName);
            $columnField = $this->db->quoteName($tableName) . "." . $this->db->quoteName($columnName);

            $select[] = "{$columnField} AS {$columnAlias}";

            $whereValue = $app->input->getString($columnAlias);

            if ("id" == $columnType and $whereValue !== null)
            {
                $whereValue = explode(",", $whereValue);
                $whereValue = array_map(array($this->db, "quote"), $whereValue);

                $whereSql[] = $columnField . "in(" . implode(",", $whereValue) . ")";
            }
        }

        $q->select(implode(",", $select))->from("#__{$tableName} as {$tableName}");

        if (! empty($whereSql))
        {
            $q->where($whereSql);
        }

        return $this->db->setQuery($q)->loadObjectList();
    }

    public function render($tasks, $value)
    {
        $tableName = $tasks[0];

        $app = \JFactory::getApplication();
        $doc = $app->getDocument();

        // show component only
        $app->input->set("tmpl", "component");

        // Set the MIME type for JSON output.
        $doc->setMimeEncoding('application/json');

        // Change the suggested filename.
        \JResponse::setHeader('Content-Disposition','attachment;filename="' . $tableName . '.json"');

        // Output the JSON data.
        return json_encode($value);
    }

    public function execute($tasks)
    {
        $items = $this->getItems($tasks);

        return $this->render($tasks, $items);
    }
}
