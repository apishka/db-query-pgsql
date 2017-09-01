<?php

namespace Apishka\DbQuery\PgSql;

/**
 * Query trait
 */

trait QueryTrait
{
    /**
     * Alias
     *
     * @var array
     */

    protected $_alias = array();

    /**
     * Table
     *
     * @var array
     */

    protected $_table = array();

    /**
     * Alias
     *
     * @param string $name
     *
     * @return Query
     */

    public function alias($name)
    {
        if ($this->_alias)
            throw new \LogicException('Alias already set');

        $this->_alias = array(
            'name' => $name,
        );

        return $this;
    }

    /**
     * Table
     *
     * @param string $table
     *
     * @return Query this
     */

    public function table($table)
    {
        if ($this->_table)
            throw new \LogicException('Table already set');

        $this->_table = array(
            'name' => $table,
        );

        return $this;
    }

    /**
     * Get table name
     *
     * @return string
     */

    public function getTableName()
    {
        return $this->_table['name'] ?? null;
    }

    /**
     * Get alias name
     *
     * @return string
     */

    public function getAliasName()
    {
        return $this->_alias['name'] ?? null;
    }

    /**
     * Add quotes
     *
     * @param mixed $string
     */

    public function quote($string)
    {
        if ($string === null)
            return 'NULL';

        return "'" . $this->escape($string) . "'";
    }

    /**
     * Quote field
     *
     * @param mixed  $string
     * @param string $glue
     *
     * @return string
     */

    public function quoteField($string, $glue = '.')
    {
        $this->checkName($string);

        if ($this->_alias)
            return $this->_alias['name'] . $glue . $string;

        if ($this->_table)
            return $this->_table['name'] . $glue . $string;

        return $string;
    }

    /**
     * Check name
     *
     * @param string $string
     *
     * @return string
     */

    public function checkName($string)
    {
        if (!preg_match('#^[a-z0-9_]+$#', $string))
            throw new \LogicException('Value ' . var_export($string, true) . ' can not be used as field name');

        return $string;
    }

    /**
     * Escape
     *
     * @param string $string
     *
     * @return string
     */

    public function escape($string)
    {
        return pg_escape_string($string);
    }

    /**
     * Escape bytea
     *
     * @param string $string
     *
     * @return string
     */

    public function escapeBytea($string)
    {
        return pg_escape_bytea($string);
    }
    /**
     * Get field
     *
     * @param string $name
     *
     * @return Field
     */

    public function getField($name)
    {
        if (!$this->hasField($name))
            $this->createField($name);

        return parent::getField($name);
    }

    /**
     * Create field
     *
     * @param string $name
     *
     * @return QueryTrait this
     */

    protected function createField($name)
    {
        return $this->registerField(
            FieldString::apishka($name)
        );
    }
}
