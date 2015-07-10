<?php

namespace Lewis\JoomlaBundle\Controller;

class DefaultReadController extends BaseController
{
    public function getItems()
    {
        $q = $this->db->getQuery(true);

        $select = array();
        $whereSql = array();
        $canReadColumns = $this->config->getTableReadColumns($this->tableName);

        foreach ($canReadColumns as $columnName => $columnType)
        {
            $select[] = $this->helper->getSelect($columnName);

            if ("id" == $columnType)
            {
                $whereValue = $this->helper->getInputValue($columnName, "id", false);

                if (! empty($whereValue))
                {
                    $whereSql[] = $this->helper->getSqlIn($columnName, $whereValue);
                }
            }
        }

        $q->select(
            implode(",", $select)
        )->from(
            $this->helper->getTableSqlName()
        );

        if (! empty($whereSql))
        {
            $q->where($whereSql);
        }

        return $this->db->setQuery($q)->loadObjectList();
    }

    public function execute()
    {
        $items = $this->getItems();

        return $this->jsonRender($items);
    }
}
