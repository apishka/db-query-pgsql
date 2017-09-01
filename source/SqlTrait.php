<?php

namespace Apishka\DbQuery\PgSql;

use Apishka\DbQuery\StdLib\QueryAbstract;

/**
 * SQL trait
 */

trait SqlTrait
{
    /**
     * Build where default
     *
     * @param QueryAbstract $query
     * @param array         $query
     *
     * @return string
     */

    public function buildWhereDefault(QueryAbstract $query, array $where)
    {
        $sql = $query->quoteField($this->getName()) . ' ';

        if ($where['value'] === null && !in_array($where['operation'], ['<>', '=']))
            throw new \LogicException('Null comparision is supported only with <> and = operations, current is ' . var_export($where['operation'], true));

        $sql .= $where['value'] === null
            ? ($where['operation'] == '=' ? 'IS NULL' : 'IS NOT NULL')
            : $where['operation'] . ' ' . $this->quote($query, $where['value'])
        ;

        return $sql;
    }

    /**
     * Build where in
     *
     * @param QueryAbstract $quote
     * @param array         $where
     *
     * @return string
     */

    public function buildWhereIn(QueryAbstract $query, array $where)
    {
        if (!is_array($where['value']))
            throw new \BadMethodCallException('Key "value" in $where for IN operation should be an array, ' . var_export(gettype($where['value']), true) . ' given');

        if (!count($where['value']))
            throw new \BadMethodCallException('Value for IN operation should have at least on element');

        $sql = $query->quoteField($this->getName()) . ' ';
        $sql .= ($where['operation'] == 'in')
            ? 'IN'
            : 'NOT IN'
        ;

        $sql .= ' ';

        $values = [];
        foreach ($where['value'] as $value)
            $values[] = $this->quote($query, $value);

        $sql .= '(' . implode(', ', $values) . ')';

        return $sql;
    }

    /**
     * Build update default
     *
     * @param QueryAbstract $query
     * @param array         $update
     *
     * @return string
     */

    public function buildUpdateDefault(QueryAbstract $query, array $update)
    {
        $sql = $query->quoteField($this->getName()) . ' ';

        switch ($update['operation'])
        {
            case '=':
                $sql .= $update['operation'] . ' ' . $this->quote($query, $update['value']);

                return $sql;

            case '+':
            case '-':
                $sql .= ' = ' . $query->quoteField($this->getName()) . ' ' . $update['operation'] . ' ' . $this->quote($query, $update['value']);

                return $sql;
        }

        throw new \LogicException('Unknown operation ' . var_export($update['operation'], true) . ' in update');
    }

    /**
     * Build insert default
     *
     * @param QueryAbstract $query
     * @param array         $insert
     *
     * @return array
     */

    public function buildInsertDefault(QueryAbstract $query, array $insert)
    {
        $result = array(
            $this->getName(),
        );

        if ($insert['operation'] != '=')
            throw new \LogicException('Unknown operation ' . var_export($insert['operation'], true) . ' in insert');

        $result[] = $this->quote($query, $insert['value']);

        return $result;
    }
}
