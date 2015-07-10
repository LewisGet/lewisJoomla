<?php

namespace Lewis\JoomlaBundle;

class Config
{
    /**
     * array(
     *     item, cat
     * )
     *
     * @var array
     */
    public $table = array();

    /**
     * array(
     *     item => array(read, write, delete)
     *     cat  => array(read, write, delete)
     * )
     *
     * @var array
     */
    public $functions = array();

    /**
     * array(
     *     item => array(
     *         array(id => int)
     *         array(name => string)
     *         array(cat => int)
     *     )
     * )
     *
     * @var array
     */
    public $columns = array();

    /**
     * array(
     *     item => array(
     *         array(id => int)
     *         array(name => string)
     *         array(cat => int)
     *     )
     * )
     *
     * @var array
     */
    public $forms = array();

    /**
     * array(
     *     item => array(
     *         array(id => int)
     *         array(name => string)
     *         array(cat => int)
     *     )
     * )
     *
     * @var array
     */
    public $reads = array();

    /**
     * array(
     *     item => array(
     *         array(id => int)
     *         array(name => string)
     *         array(cat => int)
     *     )
     * )
     *
     * @var array
     */
    public $writes = array();

    public $original = array();

    public function __construct($config)
    {
        $this->original = $config;

        foreach ($config as $tableName => $tableColumns)
        {
            // add table name
            $this->table[] = $tableName;

            $this->forms[$tableName] = array();
            $this->writes[$tableName] = array();
            $this->columns[$tableName] = array();

            foreach ($tableColumns as $columName => $columnFunctions)
            {
                // column type
                if (isset ($columnFunctions['write']))
                {
                    $columType = $columnFunctions['write'];
                } else
                {
                    $columType = $columnFunctions['read'];
                }

                $this->columns[$tableName][$columName] = $columType;

                if (isset ($columnFunctions['write']))
                {
                    $this->writes[$tableName][$columName] = $columType;
                }

                if ($columnFunctions['read'])
                {
                    $this->reads[$tableName][$columName] = $columType;
                }

                if ($columnFunctions['form'])
                {
                    $this->forms[$tableName][$columName] = $columType;
                }

                // table functions
                if (! isset($this->functions[$tableName]))
                {
                    $this->functions[$tableName] = array();
                }

                // merge columns functions
                $this->functions[$tableName] = array_merge(
                    $this->functions[$tableName],
                    array_fill_keys(array_keys($columnFunctions), true)
                );
            }

            // clear up array
            $this->functions[$tableName] = array_keys($this->functions[$tableName]);
        }
    }

    public function getTableFunctions($tableName)
    {
        return $this->functions[$tableName];
    }

    public function getTableColumns($tableName)
    {
        return $this->columns[$tableName];
    }

    public function getTableColumnFunctions($tableName)
    {
        return $this->original[$tableName];
    }

    public function getTableWriteColumns($tableName)
    {
        return $this->writes[$tableName];
    }

    public function getTableFormColumns($tableName)
    {
        return $this->forms[$tableName];
    }

    public function getTableReadColumns($tableName)
    {
        return $this->reads[$tableName];
    }
}