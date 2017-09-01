<?php

namespace ApishkaTest\DbQuery\PgSql;

use Apishka\DbQuery\StdLib\Expression;
use Apishka\DbQuery\PgSql\QuerySelect;

/**
 * Query select test
 */

class QuerySelectTest extends \PHPUnit\Framework\TestCase
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
     * Test build where empty
     */

    public function testBuildWhereEmpty()
    {
        $query = $this->getQuery();

        $this->assertSame(
            '',
            $query->buildWhere()
        );
    }

    /**
     * Test where with two arguments
     */

    public function testBuildWhereWithOneArgument()
    {
        $query = $this->getQuery()
            ->where('param', 'value')
        ;

        $this->assertSame(
            "WHERE param = 'value'",
            $query->buildWhere()
        );
    }

    /**
     * Test where with two arguments
     */

    public function testBuildWhereWithTwoArguments()
    {
        $query = $this->getQuery()
            ->where('param1', 'value1')
            ->where('param2', 'in', ['value2', 'value3'])
        ;

        $this->assertSame(
            "WHERE param1 = 'value1' AND param2 IN ('value2', 'value3')",
            $query->buildWhere()
        );
    }

    /**
     * Test build where with two arguments and alias
     */

    public function testBuildWhereWithTwoArgumentsAndAlias()
    {
        $query = $this->getQuery()
            ->alias('alias')
            ->where('param1', 'value1')
            ->where('param2', 'in', ['value2', 'value3'])
        ;

        $this->assertSame(
            "WHERE alias.param1 = 'value1' AND alias.param2 IN ('value2', 'value3')",
            $query->buildWhere()
        );
    }

    /**
     * Test build where with join
     */

    public function testBuildWhereWithJoin()
    {
        $query = $this->getQuery()
            ->table('table1')
            ->alias('table1_alias')
            ->join(
                $this->getQuery()
                    ->table('table2')
                    ->alias('table2_alias')
                    ->joinOn('id', 'id')
                    ->where('is_admin', 1)
            )
            ->where('id', 100)
        ;

        $this->assertSame(
            "WHERE table1_alias.id = '100' AND table2_alias.is_admin = '1'",
            $query->buildWhere()
        );
    }

    /**
     * Test build where with or
     */

    public function testBuildWhereWithOr()
    {
        $query = $this->getQuery()
            ->table('table1')
            ->where('id', 100)
            ->whereOr(
                $this->getQuery()
                    ->where('is_admin', 1)
                    ->where('is_super_admin', 1)
            )
        ;

        $this->assertSame(
            "WHERE table1.id = '100' AND (is_admin = '1' OR is_super_admin = '1')",
            $query->buildWhere()
        );
    }

    /**
     * Test build where only or
     */

    public function testBuildWhereOnlyOr()
    {
        $query = $this->getQuery()
            ->whereOr(
                $this->getQuery()
                    ->table('table1')
                    ->where('id', 100)
                    ->where('is_admin', 1)
            )
        ;

        $this->assertSame(
            "WHERE (table1.id = '100' OR table1.is_admin = '1')",
            $query->buildWhere()
        );
    }

    /**
     * Test build where with or complex
     */

    public function testBuildWhereWithOrComplex()
    {
        $query = $this->getQuery()
            ->table('table1')
            ->where('id', 100)
            ->whereOr(
                $this->getQuery()
                    ->where('is_admin', 1)
                    ->where(
                        $this->getQuery()
                            ->where('is_super_admin', 1)
                            ->where('can_be_super_admin', 1)
                    )
            )
        ;

        $this->assertSame(
            "WHERE table1.id = '100' AND (is_admin = '1' OR (is_super_admin = '1' AND can_be_super_admin = '1'))",
            $query->buildWhere()
        );
    }

    /**
     * Test build order by empty
     */

    public function testBuildOrderByEmpty()
    {
        $query = $this->getQuery();

        $this->assertSame(
            '',
            $query->buildOrderBy()
        );
    }

    /**
     * Test build order by with one argument
     */

    public function testBuildOrderByWithOneArgument()
    {
        $query = $this->getQuery()
            ->orderBy('param', '+')
        ;

        $this->assertSame(
            'ORDER BY param ASC',
            $query->buildOrderBy()
        );
    }

    /**
     * Test build order by with two arguments
     */

    public function testBuildOrderByWithTwoArguments()
    {
        $query = $this->getQuery()
            ->orderBy('param1', '+')
            ->orderBy('param2', '-')
        ;

        $this->assertSame(
            'ORDER BY param1 ASC, param2 DESC',
            $query->buildOrderBy()
        );
    }

    /**
     * Test build order by with two arguments and alias
     */

    public function testBuildOrderByWithTwoArgumentsAndAlias()
    {
        $query = $this->getQuery()
            ->alias('alias')
            ->orderBy('param1', '+')
            ->orderBy('param2', '-')
        ;

        $this->assertSame(
            'ORDER BY alias.param1 ASC, alias.param2 DESC',
            $query->buildOrderBy()
        );
    }

    /**
     * Test build order by with join
     */

    public function testBuildOrderByWithJoin()
    {
        $query = $this->getQuery()
            ->table('table1')
            ->alias('table1_alias')
            ->join(
                $this->getQuery()
                    ->table('table2')
                    ->alias('table2_alias')
                    ->joinOn('id', 'id')
                    ->orderBy('is_admin', 'desc')
            )
            ->orderBy('id', 'asc')
        ;

        $this->assertSame(
            'ORDER BY table1_alias.id ASC, table2_alias.is_admin DESC',
            $query->buildOrderBy()
        );
    }

    /**
     * Test build group by empty
     */

    public function testBuildGroupByEmpty()
    {
        $query = $this->getQuery();

        $this->assertSame(
            '',
            $query->buildGroupBy()
        );
    }

    /**
     * Test build group by with one argument
     */

    public function testBuildGroupByWithOneArgument()
    {
        $query = $this->getQuery()
            ->groupBy('param')
        ;

        $this->assertSame(
            'GROUP BY param',
            $query->buildGroupBy()
        );
    }

    /**
     * Test build group by with two arguments
     */

    public function testBuildGroupByWithTwoArguments()
    {
        $query = $this->getQuery()
            ->groupBy('param1')
            ->groupBy('param2')
        ;

        $this->assertSame(
            'GROUP BY param1, param2',
            $query->buildGroupBy()
        );
    }

    /**
     * Test build group by with two arguments and alias
     */

    public function testBuildGroupByWithTwoArgumentsAndAlias()
    {
        $query = $this->getQuery()
            ->alias('alias')
            ->groupBy('param1')
            ->groupBy('param2')
        ;

        $this->assertSame(
            'GROUP BY alias.param1, alias.param2',
            $query->buildGroupBy()
        );
    }

    /**
     * Test build group by with join
     */

    public function testBuildGroupByWithJoin()
    {
        $query = $this->getQuery()
            ->table('table1')
            ->alias('table1_alias')
            ->join(
                $this->getQuery()
                    ->table('table2')
                    ->alias('table2_alias')
                    ->joinOn('id', 'id')
                    ->groupBy('is_admin', 'desc')
            )
            ->groupBy('id', 'asc')
        ;

        $this->assertSame(
            'GROUP BY table1_alias.id, table2_alias.is_admin',
            $query->buildGroupBy()
        );
    }

    /**
     * Test build limit empty
     */

    public function testBuildLimitEmpty()
    {
        $query = $this->getQuery()
            ->limit(0, 100)
        ;

        $this->assertSame(
            'LIMIT 100 OFFSET 0',
            $query->buildLimit()
        );
    }

    /**
     * Test build limit with no offset
     */

    public function testBuildLimitWithNoOffset()
    {
        $query = $this->getQuery()
            ->limit(null, 100)
        ;

        $this->assertSame(
            'LIMIT 100',
            $query->buildLimit()
        );
    }

    /**
     * Test build limit with no limit
     */

    public function testBuildLimitWithNoLimit()
    {
        $query = $this->getQuery()
            ->limit(10, null)
        ;

        $this->assertSame(
            'OFFSET 10',
            $query->buildLimit()
        );
    }

    /**
     * Test build select fields empty
     */

    public function testBuildSelectFieldsEmpty()
    {
        $query = $this->getQuery();

        $this->assertSame(
            '*',
            $query->buildSelectFields()
        );
    }

    /**
     * Test build select fields empty with join
     */

    public function testBuildSelectFieldsEmptyWithJoin()
    {
        $query = $this->getQuery()
            ->table('table1')
            ->alias('table1_alias')
            ->join(
                $this->getQuery()
                    ->table('table2')
                    ->alias('table2_alias')
                    ->joinOn('id', 'id')
                    ->groupBy('is_admin', 'desc')
            )
            ->groupBy('id', 'asc')
        ;

        $this->assertSame(
            'table1_alias.*, table2_alias.*',
            $query->buildSelectFields()
        );
    }

    /**
     * Test build select with fields
     */

    public function testBuildSelectWithFields()
    {
        $query = $this->getQuery()
            ->fields('id', 'name', 'email')
        ;

        $this->assertSame(
            'id, name, email',
            $query->buildSelectFields()
        );
    }

    /**
     * Test build select with fields
     */

    public function testBuildSelectWithAddField()
    {
        $query = $this->getQuery()
            ->addField('user@email', 'email')
        ;

        $this->assertSame(
            "*, 'user@email' AS email",
            $query->buildSelectFields()
        );
    }

    /**
     * Test build select with fields
     */

    public function testBuildSelectWithAddFieldExpression()
    {
        $query = $this->getQuery()
            ->addField(Expression::apishka('NOW()'), 'current_time')
        ;

        $this->assertSame(
            '*, NOW() AS current_time',
            $query->buildSelectFields()
        );
    }

    /**
     * Test build select with fields and alias
     */

    public function testBuildSelectWithAddFieldAndAlias()
    {
        $query = $this->getQuery()
            ->alias('alias')
            ->addField(Expression::apishka('NOW()'), 'current_time')
        ;

        $this->assertSame(
            'alias.*, NOW() AS alias__current_time',
            $query->buildSelectFields()
        );
    }

    /**
     * Test build select with fields
     */

    public function testBuildSelectWithFieldsAndAlias()
    {
        $query = $this->getQuery()
            ->alias('alias')
            ->fields('id', 'name', 'email')
        ;

        $this->assertSame(
            'alias.id AS alias__id, alias.name AS alias__name, alias.email AS alias__email',
            $query->buildSelectFields()
        );
    }

    /**
     * Test build select with empty fields and add field
     */

    public function testBuildSelectWithEmptyFieldsAndAddField()
    {
        $query = $this->getQuery()
            ->fields()
            ->addField('user@email', 'email')
        ;

        $this->assertSame(
            "'user@email' AS email",
            $query->buildSelectFields()
        );
    }

    /**
     * Test build select with join
     */

    public function testBuildSelectWithJoin()
    {
        $query = $this->getQuery()
            ->table('table1')
            ->fields()
            ->addField('user@email', 'email')
            ->join(
                $this->getQuery()
                    ->table('table2')
                    ->fields()
                    ->addField('user@email', 'email')
                    ->joinOn('id', 'id')
            )
        ;

        $this->assertSame(
            "'user@email' AS table1__email, 'user@email' AS table2__email",
            $query->buildSelectFields()
        );
    }

    /**
     * Test build select with join empty fields
     */

    public function testBuildSelectWithJoinEmptyFields()
    {
        $query = $this->getQuery()
            ->table('table1')
            ->fields()
            ->addField('user@email', 'email')
            ->join(
                $this->getQuery()
                    ->table('table2')
                    ->fields()
                    ->joinOn('id', 'id')
            )
        ;

        $this->assertSame(
            "'user@email' AS table1__email",
            $query->buildSelectFields()
        );
    }

    /**
     * Test build select with join empty fields
     */

    public function testBuildSelectWithJoinDefaultFields()
    {
        $query = $this->getQuery()
            ->table('table1')
            ->fields()
            ->addField('user@email', 'email')
            ->join(
                $this->getQuery()
                    ->table('table2')
                    ->joinOn('id', 'id')
            )
        ;

        $this->assertSame(
            "'user@email' AS table1__email, table2.*",
            $query->buildSelectFields()
        );
    }

    /**
     * Test build from empty
     *
     * @expectedException \LogicException
     */

    public function testBuildFromEmpty()
    {
        $query = $this->getQuery()
            ->buildFrom()
        ;
    }

    /**
     * Test build from
     */

    public function testBuildFrom()
    {
        $query = $this->getQuery()
            ->table('test')
        ;

        $this->assertSame(
            'FROM test',
            $query->buildFrom()
        );
    }

    /**
     * Test build from with alias
     */

    public function testBuildFromWithAlias()
    {
        $query = $this->getQuery()
            ->table('test')
            ->alias('test_alias')
        ;

        $this->assertSame(
            'FROM test AS test_alias',
            $query->buildFrom()
        );
    }

    /**
     * Test build join outer
     */

    public function testBuildJoinDefault()
    {
        $query = $this->getQuery()
            ->table('table1')
            ->join(
                $this->getQuery()
                    ->table('table2')
                    ->joinOn('id', 'id')
            )
        ;

        $this->assertSame(
            'LEFT JOIN table2 ON table1.id = table2.id',
            $query->buildJoin()
        );
    }

    /**
     * Test build join default with two join on
     */

    public function testBuildJoinDefaultWithTwoJoinOn()
    {
        $query = $this->getQuery()
            ->table('table1')
            ->join(
                $this->getQuery()
                    ->table('table2')
                    ->joinOn('id', 'id')
                    ->joinOn('user_id', 'type_id')
            )
        ;

        $this->assertSame(
            'LEFT JOIN table2 ON table1.id = table2.id AND table1.user_id = table2.type_id',
            $query->buildJoin()
        );
    }

    /**
     * Test build join outer with aliases
     */

    public function testBuildJoinDefaultWithAliases()
    {
        $query = $this->getQuery()
            ->table('table1')
            ->alias('table1_alias')
            ->join(
                $this->getQuery()
                    ->table('table2')
                    ->alias('table2_alias')
                    ->joinOn('id', 'id')
            )
        ;

        $this->assertSame(
            'LEFT JOIN table2 AS table2_alias ON table1_alias.id = table2_alias.id',
            $query->buildJoin()
        );
    }

    /**
     * Test build select
     */

    public function testBuildSelect()
    {
        $query = $this->getQuery()
            ->table('test')
            ->where('email', 'name@example.com')
            ->orderBy('id', 'desc')
            ->groupBy('type_id')
            ->limit(5, 15)
        ;

        $this->assertSame(
            "SELECT test.* FROM test WHERE test.email = 'name@example.com' GROUP BY test.type_id ORDER BY test.id DESC LIMIT 15 OFFSET 5;",
            $query->build()
        );
    }

    /**
     * Test build complex select
     */

    public function testBuildComplexSelect()
    {
        $query = $this->getQuery()
            ->table('photos')
            ->join(
                $this->getQuery()
                    ->table('photo_albums')
                    ->joinOn('album_id', 'id')
                    ->where('is_deleted', 0)
                    ->orderBy('album_weight', '-', 1)
                    ->orderBy('album_weight1', '-', 2)
                    ->join(
                        $this->getQuery()
                            ->fields()
                            ->table('users')
                            ->joinOn('user_id', 'id')
                            ->where('is_deleted', 0)
                            ->orderBy('user_weight', '-')
                            ->orderBy('user_weight1', '-')
                    )
            )
            ->where('is_deleted', 0)
            ->orderBy('photo_weight', '-')
            ->limit(5, 15)
        ;

        $this->assertSame(
            "SELECT photos.*, photo_albums.* FROM photos LEFT JOIN photo_albums ON photos.album_id = photo_albums.id LEFT JOIN users ON photo_albums.user_id = users.id WHERE photos.is_deleted = '0' AND photo_albums.is_deleted = '0' AND users.is_deleted = '0' ORDER BY photo_albums.album_weight DESC, photo_albums.album_weight1 DESC, photos.photo_weight DESC, users.user_weight DESC, users.user_weight1 DESC LIMIT 15 OFFSET 5;",
            $query->build()
        );
    }
}
