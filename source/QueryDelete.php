<?php

namespace Apishka\DbQuery\PgSql;

use Apishka\DbQuery\StdLib\QueryDeleteAbstract;

/**
 * Query delete
 */

class QueryDelete extends QueryDeleteAbstract
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

        $sqls[] = 'DELETE FROM';
        $sqls[] = $this->buildTable();
        $sqls[] = $this->buildWhere();

        $sql = implode(' ', array_filter($sqls)) . ';';

        return $sql;
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
