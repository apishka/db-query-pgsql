<?php

namespace ApishkaTest\DbQuery\PgSql;

use Apishka\DbQuery\PgSql\FieldInt;
use Apishka\DbQuery\PgSql\QueryInsert;

/**
 * Query insert test
 */

class QueryInsertTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Get query
     *
     * @return Query
     */

    protected function getQuery()
    {
        return new QueryInsert();
    }

    /**
     * Test build insert
     */

    public function testBuildInsert()
    {
        $query = $this->getQuery()
            ->table('photos')
            ->registerField(
                FieldInt::apishka('counter_views')
            )
            ->set('name', 'foo')
            ->set('user_id', null)
            ->set('counter_views', 1)
        ;

        $this->assertSame(
            "INSERT INTO photos (name, user_id, counter_views) VALUES ('foo', NULL, 1);",
            $query->build()
        );
    }
}
