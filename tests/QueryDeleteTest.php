<?php

namespace ApishkaTest\DbQuery\PgSql;

use Apishka\DbQuery\PgSql\FieldInt;
use Apishka\DbQuery\PgSql\QueryDelete;

/**
 * Query insert test
 */

class QueryDeleteTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Get query
     *
     * @return Query
     */

    protected function getQuery()
    {
        return new QueryDelete();
    }

    /**
     * Test build insert
     */

    public function testBuildDelete()
    {
        $query = $this->getQuery()
            ->table('photos')
            ->registerField(
                FieldInt::apishka('is_deleted')
            )
            ->where('name', 'Foo')
            ->where('is_deleted', 0)
        ;

        $this->assertSame(
            "DELETE FROM photos WHERE photos.name = 'Foo' AND photos.is_deleted = 0;",
            $query->build()
        );
    }
}
