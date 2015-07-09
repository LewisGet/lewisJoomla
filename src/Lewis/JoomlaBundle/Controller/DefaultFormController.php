<?php

namespace Lewis\JoomlaBundle\Controller;

use Lewis\Di\Container;

class DefaultFormController
{
    public $container;

    /**
     * return json used angular build form
     *
     * @param $tasks
     * @return array
     */
    public function getFormData($tasks)
    {
        $tableName = $tasks[0];

        $this->container = new Container();

        $app = \JFactory::getApplication();

        /**
         * @var \JDatabaseDriverMysqli
         */
        $db = $this->container->get("db");

        /**
         * @var \Lewis\JoomlaBundle\Config
         */
        $config = $this->container->get("config");

        $forms = $config->getTableFormColumns($tableName);

        $formIdFildName = array_keys($forms, "id");

        $formData = array();

        if (! empty($formIdFildName))
        {
            $q = $db->getQuery(true);

            $whereSql = array();

            foreach ($formIdFildName as $fieldName)
            {
                $whereSql[] = $db->quoteName($tableName) . "." . $db->quoteName($fieldName) . " = " .
                    $db->quote(
                        $app->input->getString($tableName . ucfirst($fieldName))
                    );
            }

            $q->select("*")->from($db->quoteName("#__{$tableName}") . " AS " . $db->quoteName($tableName))->where($whereSql);

            $formData = $db->setQuery($q)->loadObject();
        }

        $dataset = array();

        foreach ($forms as $fieldName => $fieldType)
        {
            $field = array();

            $field['name'] = $tableName . ucfirst($fieldName);

            $field['type'] = $fieldType;

            $field['data'] =
                ! empty($formData) ? $formData->$fieldName : null;

            $dataset[] = $field;
        }

        return $dataset;
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
        $formData = $this->getFormData($tasks);

        return $this->render($tasks, $formData);
    }
}
