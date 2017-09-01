<?php

namespace Apishka\DbQuery\PgSql;

use Apishka\DbQuery\StdLib\QuerySelectAbstract;

/**
 * Query select
 */

class QuerySelect extends QuerySelectAbstract
{
    /**
     * Traits
     */

    use QueryTrait;
    use QueryWhereTrait {
        buildWhereParts as buildWherePartsTrait;
    }
    use SqlTrait;

    /**
     * Join constants
     */

    const JOIN_OUTER = 'join_outer';
    const JOIN_INNER = 'join_inner';
    const JOIN_RIGHT = 'join_right';

    /**
     * To string
     *
     * @return string
     */

    public function build()
    {
        $sqls = array();

        $sqls[] = 'SELECT';
        $sqls[] = $this->buildSelectFields();
        $sqls[] = $this->buildFrom();
        $sqls[] = $this->buildWhere();
        $sqls[] = $this->buildGroupBy();
        $sqls[] = $this->buildOrderBy();
        $sqls[] = $this->buildLimit();

        $sql = implode(' ', array_filter($sqls)) . ';';

        return $sql;
    }

    /**
     * Build select fields
     *
     * @return string
     */

    public function buildSelectFields()
    {
        $sqls = $this->buildSelectFieldsParts();

        $sql = implode(', ', $sqls);
        if ($sql)
            return $sql;

        return '*';
    }

    /**
     * Build select fields parts
     *
     * @return array
     */

    public function buildSelectFieldsParts()
    {
        $sqls = array();
        if ($this->_fields !== null)
        {
            foreach ($this->_fields as $field => $details)
            {
                // Special check on all fields
                if ($field === '*')
                {
                    $alias = $this->getAliasName() ?? $this->getTableName();
                    $sqls[] = ($alias ? $alias . '.' : '') . '*';

                    continue;
                }

                $sql = array_key_exists('include', $details) && $details['include']
                    ? $this->quoteField($details['field'])
                    : $this->getField($details['alias'] ?? $details['field'])->quote($this, $details['field'])
                ;

                if (array_key_exists('alias', $details))
                {
                    $sql .= ' AS ' . $this->quoteField($details['alias'], '__');
                }
                elseif ($this->_alias)
                {
                    $sql .= ' AS ' . $this->quoteField($details['field'], '__');
                }

                $sqls[] = $sql;
            }
        }
        else
        {
            $alias = $this->getAliasName() ?? $this->getTableName();
            $sqls[] = ($alias ? $alias . '.' : '') . '*';
        }

        foreach ($this->getJoinRelations() as $details)
        {
            $relation = $details['query'];
            $sqls = array_merge($sqls, $relation->buildSelectFieldsParts());
        }


        return $sqls;
    }

    /**
     * Build from
     *
     * @return string
     */

    public function buildFrom()
    {
        $sqls = array();

        if (!$this->_table)
            throw new \LogicException('Table settings are required to build query');

        $sqls[] = 'FROM ' . $this->buildTables();

        $sql = implode(' ', $sqls);

        return $sql;
    }

    /**
     * Build tables
     *
     * @return string
     */

    public function buildTables()
    {
        $sqls[] = $this->buildFromTable();
        $sqls[] = $this->buildJoin();

        return implode(' ', array_filter($sqls));
    }

    /**
     * Build from table
     *
     * @return string
     */

    public function buildFromTable()
    {
        $sql = $this->checkName($this->_table['name']);

        if ($this->_alias)
            $sql .= ' AS ' . $this->checkName($this->_alias['name']);

        return $sql;
    }

    /**
     * Build join
     *
     * @return string
     */

    public function buildJoin()
    {
        $sqls = $this->buildJoinParts();

        $sql = implode(' ', $sqls);
        if ($sql)
            return $sql;

        return '';
    }

    /**
     * Build join parts
     *
     * @return array
     */

    public function buildJoinParts()
    {
        $sqls = array();

        foreach ($this->getJoinRelations() as $details)
        {
            $relation = $details['query'];

            $sqls[] = 'LEFT JOIN ' . $relation->buildFromTable() . ' ON';

            $join_on = $relation->getJoinOn();
            if (!$join_on)
                throw new \LogicException('Join options not defined');

            $sqls_on = array();
            foreach ($join_on as $key => $join_details)
                $sqls_in[] = $this->quoteField($join_details['field_from']) . ' = ' . $relation->quoteField($join_details['field_to']);

            $sqls[] = implode(' AND ', $sqls_in);

            $sqls = array_merge($sqls, $relation->buildJoinParts());
        }

        return $sqls;
    }

    /**
     * Get join relations
     */

    protected function getJoinRelations()
    {
        if ($this->_relations)
        {
            foreach ($this->_relations as $details)
            {
                if ($details['type'] != self::RELATION_JOIN)
                    continue;

                yield $details;
            }
        }
    }

    /**
     * Build where conditions
     *
     * @return array
     */

    public function buildWhereParts()
    {
        $sqls = $this->buildWherePartsTrait();

        foreach ($this->getJoinRelations() as $details)
        {
            $relation = $details['query'];
            $sqls = array_merge($sqls, $relation->buildWhereParts());
        }

        return $sqls;
    }

    /**
     * Build order by
     *
     * @return string
     */

    public function buildOrderBy()
    {
        $parts = $this->buildOrderByParts();
        $sqls = array();
        foreach ($parts as $sql)
            $sqls[] = $sql['sql'];

        $sql = implode(', ', $sqls);
        if ($sql)
            $sql = 'ORDER BY ' . $sql;

        return $sql;
    }

    /**
     * Build order by parts
     *
     * @return array
     */

    public function buildOrderByParts()
    {
        $sqls = array();
        if ($this->_order_by)
        {
            foreach ($this->_order_by as $order)
            {
                if (!in_array($order['sort'], ['asc', 'desc']))
                    throw new \LogicException('Sort operation ' . var_export($order['sort'], true) . ' not supported');

                $sqls[] = array(
                    'sql'      => $this->quoteField($order['field']) . ' ' . strtoupper($order['sort']),
                    'position' => $order['position'],
                );
            }
        }

        foreach ($this->getJoinRelations() as $details)
        {
            $relation = $details['query'];
            $sqls = array_merge($sqls, $relation->buildOrderByParts());
        }

        usort(
            $sqls,
            function ($a, $b)
            {
                if ($a['position'] === $b['position'])
                    return 0;

                if ($a['position'] === null)
                    return 1;

                if ($b['position'] === null)
                    return -1;

                return $a['position'] < $b['position'] ? -1 : 1;
            }
        );

        return $sqls;
    }

    /**
     * Build group by
     *
     * @return string
     */

    public function buildGroupBy()
    {
        $sqls = $this->buildGroupByParts();

        $sql = implode(', ', $sqls);
        if ($sql)
            $sql = 'GROUP BY ' . $sql;

        return $sql;
    }

    /**
     * Build group by parts
     *
     * @return array
     */

    public function buildGroupByParts()
    {
        $sqls = array();
        if ($this->_group_by)
        {
            foreach ($this->_group_by as $group)
                $sqls[] = $this->quoteField($group['field']);
        }

        foreach ($this->getJoinRelations() as $details)
        {
            $relation = $details['query'];
            $sqls = array_merge($sqls, $relation->buildGroupByParts());
        }

        return $sqls;
    }

    /**
     * Build limit
     *
     * @return string
     */

    public function buildLimit()
    {
        $sqls = array();
        if ($this->_limit)
        {
            if ($this->_limit['limit'] !== null)
                $sqls[] = 'LIMIT ' . intval($this->_limit['limit']);

            if ($this->_limit['offset'] !== null)
                $sqls[] = 'OFFSET ' . intval($this->_limit['offset']);
        }

        $sql = implode(' ', $sqls);

        return $sql;
    }
}
