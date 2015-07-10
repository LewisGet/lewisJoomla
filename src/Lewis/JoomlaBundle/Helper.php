<?php

namespace Lewis\JoomlaBundle;

use Lewis\Di\Container;

class Helper
{
    public $tableName;
    public $container;

    public function __construct()
    {
        $this->container = new Container();
        $this->tableName = $this->container->get("tableName");
    }

    /**
     * get table name sql
     *
     * @param bool $as select display is #__abc or #__abc AS abc
     * @return string
     */
    public function getTableSqlName($as = true)
    {
        $db = $this->container->get("db");

        if (! $as)
        {
            return $db->quoteName("#__{$this->tableName}");
        }

        return $db->quoteName("#__{$this->tableName}") . " AS " . $db->quoteName($this->tableName);
    }

    /**
     * @param $columnName string column name
     * @return string
     */
    public function getTableColumns($columnName)
    {
        $db = $this->container->get("db");

        return $db->quoteName($this->tableName) . "." . $db->quoteName($columnName);
    }

    /**
     * get column input default name,
     * ex.
     *
     * #__item.post => itemPost
     *
     * @param $columnName string column name
     * @return string
     */
    public function getInputAlias($columnName)
    {
        return $this->tableName . ucfirst($columnName);
    }

    /**
     * get input value by column name
     * ex.
     *
     * #__item.post => $_GET['itemPost'], $_POST['itemPost']
     *
     * @param $columnName string column name
     * @param string $type set type that you get form input
     * @param bool|string $quote you can use true, false, both to set return style
     * @return array|int|string
     */
    public function getInputValue($columnName, $type = "string", $quote = true)
    {
        $app = \JFactory::getApplication();
        $db = $this->container->get("db");
        $inputName = $this->getInputAlias($columnName);

        $return = $app->input->getString($inputName);

        if ("int" == $type)
        {
            $return = $app->input->getInt($inputName);
        }

        if ("cmd" == $type)
        {
            $return = $app->input->getCmd($inputName);
        }

        if ("id" == $type)
        {
            $return = $app->input->getString($inputName, null);

            if (null !== $return)
            {
                $return = array_map('intval', explode(',', $return));
            }

            if (sizeof($return) == 1)
            {
                $return = $return[0];
            }
        }

        if (false === $quote)
        {
            return $return;
        }

        if ("both" === $quote)
        {
            return array(
                "default" => $return,
                "sql" => $db->quote($return)
            );
        }

        return $db->quote($return);
    }

    /**
     * get sql in
     *
     * ex.
     * abc, [1,2,3] => abc in ('1', '2', '3')
     *
     * @param $columnName
     * @param $value
     * @param bool $tableName prefix table name
     * @return string
     */
    public function getSqlIn($columnName, $value, $tableName = true)
    {
        $db = $this->container->get("db");

        if ($tableName)
        {
            $columnName = $this->getTableColumns($columnName);
        }
        else
        {
            $columnName = $db->quoteName($columnName);
        }

        return "{$columnName} in(" . implode(",", $db->quote($value)) . ")";
    }

    /**
     * get sql same
     *
     * ex.
     * abc = '123'
     *
     * @param $columnName
     * @param $value
     * @param bool $tableName
     * @return string
     */
    public function getSqlSame($columnName, $value, $tableName = true)
    {
        $db = $this->container->get("db");

        if ($tableName)
        {
            $columnName = $this->getTableColumns($columnName);
        }
        else
        {
            $db->quoteName($columnName);
        }

        return "{$columnName} = " . $db->quote($value);
    }

    /**
     * get sql select as alias
     *
     * ex.
     * post => item.post AS itemPost
     *
     * @param $columnName
     * @return string
     */
    public function getSelect($columnName)
    {
        $columnAlias = $this->getInputAlias($columnName);

        $columnField = $this->getTableColumns($columnName);

        return "{$columnField} AS {$columnAlias}";
    }
}
