<?php

namespace Apishka\DbQuery\PgSql;

/**
 * Query where trait
 */

trait QueryWhereTrait
{
    /**
     * Build where
     *
     * @return string
     */

    public function buildWhere()
    {
        $sqls = $this->buildWhereParts();

        $sql = implode(' AND ', $sqls);
        if ($sql)
            $sql = 'WHERE ' . $sql;

        return $sql;
    }

    /**
     * Build where conditions
     *
     * @return array
     */

    public function buildWhereParts()
    {
        $sqls = array();
        if ($this->_where)
        {
            foreach ($this->_where as $where)
            {
                if (array_key_exists('query', $where) && $where['query'] instanceof self)
                {
                    $sqls[] = '(' . implode(array_key_exists('glue', $where) && $where['glue'] == 'or' ? ' OR ' : ' AND ', $where['query']->buildWhereParts()) . ')';
                }
                else
                {
                    $sqls[] = $this->getField($where['field'])->buildWhere($this, $where);
                }
            }
        }

        return $sqls;
    }
}
