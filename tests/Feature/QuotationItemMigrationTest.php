<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Illuminate\Support\Facades\Schema;

class QuotationItemMigrationTest extends TestCase
{
    use RefreshDatabase;

   public function test_quotation_items_do_not_reference_a_missing_products_table(): void
{
    $this->assertTrue(
        Schema::hasTable('quotation_items')
    );

    $this->assertFalse(
        Schema::hasColumn('quotation_items', 'product_id')
    );

    $this->assertFalse(
        Schema::hasTable('products')
    );
}
}
