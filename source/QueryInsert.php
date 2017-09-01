<?php

namespace Apishka\DbQuery\PgSql;

use Apishka\DbQuery\StdLib\QueryInsertAbstract;

/**
 * Query insert
 */

class QueryInsert extends QueryInsertAbstract
{
    /**
     * Traits
     */

    use QueryTrait;
    use SqlTrait;

    /**
     * To string
     *
     * @return string
     */

    public function build()
    {
        $sqls = array();

        $sqls[] = 'INSERT INTO';
        $sqls[] = $this->buildTable();
        $sqls[] = $this->buildInsertFields();

        $sql = implode(' ', array_filter($sqls)) . ';';

        return $sql;
    }

    /**
     * Build insert fields
     *
     * @return string
     */

    public function buildInsertFields()
    {
        $sqls = $this->buildInsertFieldsParts();

        if (!$sqls)
            throw new \LogicException('Fields to insert query not found');

        $sql = '(' . implode(', ', array_keys($sqls)) . ') VALUES (' . implode(', ', array_values($sqls)) . ')';

        return $sql;
    }

    /**
     * Build update fields parts
     *
     * @return array
     */

    public function buildInsertFieldsParts()
    {
        $sqls = array();

        if ($this->_set_fields)
        {
            foreach ($this->_set_fields as $field => $insert)
            {
                list($field, $value) = $this->getField($insert['field'])->buildInsert($this, $insert);
                $sqls[$field] = $value;
            }
        }

        return $sqls;
    }

    /**
     * Build table
     *
     * @return string
     */

    public function buildTable()
    {
        $sql = $this->checkName($this->_table['name']);

        if ($this->_alias)
            $sql .= ' AS ' . $this->checkName($this->_alias['name']);

        return $sql;
    }
}
