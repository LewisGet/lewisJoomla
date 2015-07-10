<?php

namespace Lewis\JoomlaBundle\Controller;

class DefaultFormController extends BaseController
{
    public function getForm()
    {
        $config = $this->config->getTableFormColumns($this->tableName);

        $dataset = array();

        $formData = $this->getFormData($config);

        foreach ($config as $fieldName => $fieldType)
        {
            $field = array();

            $field['name'] = $this->helper->getInputAlias($this->tableName, $fieldName);

            $field['type'] = $fieldType;

            $field['data'] =
                ! empty($formData) ? $formData->$fieldName : null;

            $dataset[] = $field;
        }

        return $dataset;
    }

    public function getFormData($columns)
    {
        // get configs columns that is id field
        $indexFields = array_keys($columns, "id");

        $formData = array();

        // they is no index to find data
        if (empty($indexFields))
        {
            return $formData;
        }

        $q = $this->db->getQuery(true);

        $whereSql = array();

        foreach ($indexFields as $fieldName)
        {
            $value = $this->helper->getInputValue($fieldName, "int", false);

            $whereSql[] = $this->helper->getSqlSame($fieldName, $value);
        }

        $q->select("*")->from(
            $this->helper->getTableSqlName()
        )->where($whereSql);

        $formData = $this->db->setQuery($q)->loadObject();

        return $formData;
    }

    public function execute()
    {
        $formData = $this->getForm();

        return $this->jsonRender($formData);
    }
}
