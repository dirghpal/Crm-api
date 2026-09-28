<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::create('quotation_items', function (Blueprint $table) {
    $table->id();

    $table->foreignId('quotation_id')
        ->constrained('quotations')
        ->cascadeOnDelete();

    $table->string('item_name');
    $table->decimal('quantity', 10, 2)->default(1);
    $table->decimal('price', 12, 2)->default(0);
    $table->decimal('total_amount', 12, 2)->default(0);

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_items');
    }
};
