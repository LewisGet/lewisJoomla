<?php

namespace Lewis\JoomlaBundle\Controller;

class DefaultWriteController extends BaseController
{
    public function setItems()
    {
        $q = $this->db->getQuery(true);

        $columns = $this->config->getTableWriteColumns($this->tableName);

        $insertColumns = array();
        $insertValue   = array();
        $updateValue   = array();
        $whereSql      = array();

        foreach ($columns as $columnName => $columnType)
        {
            $value = $this->helper->getInputValue($columnName, $columnType, false);

            if (empty($value))
            {
                continue;
            }

            if ("id" === $columnType)
            {
                if (is_array($value))
                {
                    $whereSql[] = $this->helper->getSqlIn($columnName, $value, false);
                }
                else
                {
                    $whereSql[] = $this->helper->getSqlSame($columnName, $value, false);
                }

                continue;
            }

            $insertColumns[] = $this->db->quoteName($columnName);
            $insertValue[]   = $this->db->quote($value);
            $updateValue[]   = $this->helper->getSqlSame($columnName, $value, false);
        }

        if (empty($insertValue))
        {
            return true;
        }

        if (empty($whereSql))
        {
            $q->insert($this->helper->getTableSqlName(false))
                ->columns($insertColumns)
                ->values(implode(',', $insertValue));
        }
        else
        {
            $q->update($this->helper->getTableSqlName(false))
                ->set($updateValue)
                ->where($whereSql);
        }

        $this->db->setQuery($q);
        $this->db->execute();

        return true;
    }

    public function execute()
    {
        return $this->setItems();
    }
}
