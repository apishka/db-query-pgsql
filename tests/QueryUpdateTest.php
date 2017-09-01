<?php

namespace ApishkaTest\DbQuery\PgSql;

use Apishka\DbQuery\PgSql\FieldInt;
use Apishka\DbQuery\PgSql\QueryUpdate;

/**
 * Query update test
 */

class QueryUpdateTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Get query
     *
     * @return Query
     */

    protected function getQuery()
    {
        return new QueryUpdate();
    }

    /**
     * Test build update
     */

    public function testBuildUpdate()
    {
        $query = $this->getQuery()
            ->table('photos')
            ->registerField(
                FieldInt::apishka('counter_views')
            )
            ->set('name', 'foo')
            ->set('user_id', null)
            ->set('counter_views', '+', 1)
            ->where('is_deleted', 0)
        ;

        $this->assertSame(
            "UPDATE photos SET photos.name = 'foo', photos.user_id = NULL, photos.counter_views  = photos.counter_views + 1 WHERE photos.is_deleted = '0';",
            $query->build()
        );
    }
}
