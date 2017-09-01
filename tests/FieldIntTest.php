<?php

namespace ApishkaTest\DbQuery\PgSql;

use Apishka\DbQuery\PgSql\FieldInt;
use Apishka\DbQuery\PgSql\QuerySelect;
use Apishka\DbQuery\StdLib\Expression;

/**
 * Query test
 */

class FieldIntTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Get query
     *
     * @return Query
     */

    protected function getQuery()
    {
        return new QuerySelect();
    }

    /**
     * Get field
     *
     * @param string $name
     *
     * @return Field
     */

    protected function getField($name = 'foo')
    {
        return new FieldInt($name);
    }

    /**
     * Test build where equals
     *
     * @dataProvider providerTestBuildWhereEquals
     *
     * @param string $expected
     * @param mixed  $value
     */

    public function testBuildWhereEquals($expected, $value)
    {
        $this->assertSame(
            $expected,
            $this->getField()->buildWhere(
                $this->getQuery(),
                array(
                    'field'     => 'foo',
                    'operation' => '=',
                    'value'     => $value,
                )
            )
        );
    }

    /**
     * Provider test build where equals
     *
     * @return array
     */

    public function providerTestBuildWhereEquals()
    {
        return array(
            ['foo = 10',    10],
            ['foo = 0',     false],
            ['foo = 1',     true],
            ['foo IS NULL', null],
        );
    }

    /**
     * Test build where not equals
     *
     * @dataProvider providerTestBuildWhereNotEquals
     *
     * @param string $expected
     * @param mixed  $value
     */

    public function testBuildWhereNotEquals($expected, $value)
    {
        $this->assertSame(
            $expected,
            $this->getField()->buildWhere(
                $this->getQuery(),
                array(
                    'field'     => 'foo',
                    'operation' => '<>',
                    'value'     => $value,
                )
            )
        );
    }

    /**
     * Provider test build where equals
     *
     * @return array
     */

    public function providerTestBuildWhereNotEquals()
    {
        return array(
            ['foo <> 10',       10],
            ['foo <> 0',        false],
            ['foo <> 1',        true],
            ['foo IS NOT NULL', null],
        );
    }

    /**
     * Test build where in
     */

    public function testBuildWhereIn()
    {
        $this->assertSame(
            'foo IN (1, 2, 3)',
            $this->getField()->buildWhere(
                $this->getQuery(),
                array(
                    'field'     => 'foo',
                    'operation' => 'in',
                    'value'     => [1, 2, 3],
                )
            )
        );
    }

    /**
     * Test expression
     */

    public function testExpression()
    {
        $this->assertSame(
            'foo = 2*2',
            $this->getField()->buildWhere(
                $this->getQuery(),
                array(
                    'field'     => 'foo',
                    'operation' => '=',
                    'value'     => Expression::apishka('2*2'),
                )
            )
        );
    }
}
