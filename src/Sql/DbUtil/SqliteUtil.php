<?php

namespace Phactory\Sql\DbUtil;

class SqliteUtil extends AbstractDbUtil
{
    protected $_quoteChar = '`';

    public function getPrimaryKey($table)
    {
        $stmt = $this->_pdo->prepare("SELECT * FROM sqlite_master WHERE type='table' AND name=:name");
        $stmt->execute([':name' => $table]);
        $result = $stmt->fetch();
        $sql = $result['sql'];

        $matches = [];
        preg_match('/(\w+?)\s+\w+?\s+PRIMARY KEY/', $sql, $matches);

        if (!isset($matches[1])) {
            return;
        }

        return $matches[1];
    }

    public function getColumns($table)
    {
        $stmt = $this->_pdo->query("PRAGMA table_info($table)");
        $columns = [];
        while ($row = $stmt->fetch()) {
            $columns[] = $row['name'];
        }

        return $columns;
    }
}
