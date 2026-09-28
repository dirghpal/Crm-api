<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class QuotationItemMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_quotation_items_do_not_reference_a_missing_products_table(): void
    {
        $createSql = DB::selectOne("SHOW CREATE TABLE quotation_items");

        $this->assertNotNull($createSql);
        $this->assertStringNotContainsString('CONSTRAINT', $createSql->{'Create Table'});
    }
}
