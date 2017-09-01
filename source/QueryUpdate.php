<?php

namespace Apishka\DbQuery\PgSql;

use Apishka\DbQuery\StdLib\QueryUpdateAbstract;

/**
 * Query update
 */

class QueryUpdate extends QueryUpdateAbstract
{
    /**
     * Traits
     */

    use QueryTrait;
    use QueryWhereTrait;
    use SqlTrait;

    /**
     * To string
     *
     * @return string
     */

    public function build()
    {
        $sqls = array();

        $sqls[] = 'UPDATE';
        $sqls[] = $this->buildTable();
        $sqls[] = $this->buildUpdateFields();
        $sqls[] = $this->buildWhere();

        $sql = implode(' ', array_filter($sqls)) . ';';

        return $sql;
    }

    /**
     * Build update fields
     *
     * @return string
     */

    public function buildUpdateFields()
    {
        $sqls = $this->buildUpdateFieldsParts();

        if (!$sqls)
            throw new \LogicException('Fields to update query not found');

        $sql = 'SET ' . implode(', ', $sqls);

        return $sql;
    }

    /**
     * Build update fields parts
     *
     * @return array
     */

    public function buildUpdateFieldsParts()
    {
        $sqls = array();

        if ($this->_set_fields)
        {
            foreach ($this->_set_fields as $field => $update)
                $sqls[] = $this->getField($update['field'])->buildUpdate($this, $update);
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
