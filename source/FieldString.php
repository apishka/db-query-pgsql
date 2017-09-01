<?php

namespace Apishka\DbQuery\PgSql;

use Apishka\DbQuery\StdLib\FieldString as BaseFieldString;

/**
 * String field type
 *
 * @easy-extend-base
 */

class FieldString extends BaseFieldString
{
    /**
     * Traits
     */

    use SqlTrait;

    /**
     * Prepare build where functions
     *
     * @param array $build_where
     *
     * @return array
     */

    protected function prepareBuildWhereFunctions(array $build_where)
    {
        return array_replace(
            $build_where,
            array(
                '='      => 'buildWhereDefault',
                '<>'     => 'buildWhereDefault',
                '>'      => 'buildWhereDefault',
                '>='     => 'buildWhereDefault',
                '<'      => 'buildWhereDefault',
                '<='     => 'buildWhereDefault',
                'in'     => 'buildWhereIn',
                'not in' => 'buildWhereIn',
            )
        );
    }

    /**
     * Prepare build update functions
     *
     * @param array $build_update
     *
     * @return array
     */

    protected function prepareBuildUpdateFunctions(array $build_update)
    {
        return array_replace(
            $build_update,
            array(
                '='     => 'buildUpdateDefault',
            )
        );
    }

    /**
     * Prepare build insert functions
     *
     * @param array $build_update
     *
     * @return array
     */

    protected function prepareBuildInsertFunctions(array $build_update)
    {
        return array_replace(
            $build_update,
            array(
                '='     => 'buildInsertDefault',
            )
        );
    }
}
