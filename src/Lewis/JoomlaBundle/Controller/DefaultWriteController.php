<?php

namespace Lewis\JoomlaBundle\Controller;

use Lewis\Di\Container;

class DefaultWriteController
{
    public $container;

    public function setItems($tasks)
    {
        $app = \JFactory::getApplication();

        $tableName = $tasks[0];
        $this->container = new Container();

        /**
         * @var \JDatabaseDriverMysqli
         */
        $db = $this->container->get("db");

        /**
         * @var \Lewis\JoomlaBundle\Config
         */
        $config = $this->container->get("config");

        $q = $db->getQuery(true);

        $columns = $config->getTableColumnFunctions($tableName);

        $insertColumns = array();
        $insertValue   = array();
        $updateValue   = array();
        $whereSql      = array();

        foreach ($columns as $columnName => $functions)
        {
            if (! in_array("write", array_keys($functions)))
            {
                continue;
            }

            $columnType  = $functions['write'];
            $columnAlias = $tableName . ucfirst($columnName);

            switch ($columnType)
            {
                case("id"):
                    $value = $app->input->getString($columnAlias, null);

                    $value = array_map('intval', explode(',', $value));
                break;
                case("int"):
                    $value = $app->input->getInt($columnAlias, null);
                break;
                case("string"):
                    $value = $app->input->getString($columnAlias, null);
                break;
                default:
                    $value = $app->input->getString($columnAlias, null);
                break;
            }

            if (empty($value))
            {
                continue;
            }

            if ("id" === $columnType)
            {
                if (is_array($value))
                {
                    $whereSql[] = "{$columnName} in(" . implode(",", $db->quote($value)) . ")";
                }
                else
                {
                    $whereSql[] = "{$columnName} = " . $db->quote($value);
                }

                continue;
            }

            $insertColumns[] = $columnName;
            $insertValue[] = $value;
            $updateValue[] = $db->quoteName($columnName) . " = " . $db->quote($value);
        }

        if (empty($insertValue))
        {
            return true;
        }

        if (empty($whereSql))
        {
            $q->insert($db->quoteName("#__{$tableName}"))
                ->columns($db->quoteName($insertColumns))
                ->values(implode(',', $insertValue));
        }
        else
        {
            $q->update($db->quoteName("#__{$tableName}"))
                ->set($updateValue)
                ->where($whereSql);
        }

        $db->setQuery($q);
        $db->execute();

        return true;
    }

    public function execute($tasks)
    {
        return $this->setItems($tasks);
    }
}
