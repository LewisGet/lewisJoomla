<?php

namespace Lewis\TableCreater;

class TableCreater
{
    public $dbName;
    public $dbPrefix;
    public $db;

    public function __construct($db, $databaseName, $databasePrefix)
    {
        $this->dbName = $databaseName;
        $this->dbPrefix = $databasePrefix;
        $this->db = $db;
    }

    public function tableIsset($tableName)
    {
        $row = $this->db->setQuery("show Tables from `{$this->dbName}` LIKE \"{$tableName}\"")->loadObjectList();

        return ! empty($row);
    }

    public function createTables($tableDataset)
    {

        foreach ($tableDataset as $tableName => $columns)
        {
            if ($this->tableIsset("{$this->dbPrefix}_{$tableName}"))
            {
                continue;
            }

            $sql = "CREATE TABLE `{$this->dbPrefix}_{$tableName}` ( \n";

            $columnSql = array();

            foreach ($columns as $columnName => $columnType)
            {
                switch ($columnType)
                {
                    case("id"):
                        $typeSql = " INT(11) ";
                        break;
                    case("int"):
                        $typeSql = " INT(11) ";
                    break;
                    case("string"):
                        $typeSql = " VARCHAR(255) ";
                    break;
                    default:
                        $typeSql = " VARCHAR(255) ";
                    break;
                }

                if ("id" == $columnType)
                {
                    $typeSql .= " UNSIGNED AUTO_INCREMENT PRIMARY KEY";
                }

                $columnSql[] = "    `{$columnName}` {$typeSql}";
            }

            $sql .= implode(",\n", $columnSql);

            $sql .= "\n)\n";

            $this->db->setQuery($sql);
            $this->db->execute();
        }
    }
}