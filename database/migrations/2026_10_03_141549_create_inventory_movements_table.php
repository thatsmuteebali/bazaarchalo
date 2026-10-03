<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
            $table->string('variant_title')->nullable();          // snapshot, survives variant deletion
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // who made the change

            $table->string('reason', 20);                         // initial | restock | sale | return | damaged | correction
            $table->integer('quantity_change');                   // +50 / -3
            $table->unsignedInteger('stock_before');
            $table->unsignedInteger('stock_after');
            $table->string('note')->nullable();

            $table->nullableMorphs('reference');                  // later: link to an Order

            $table->timestamp('created_at')->useCurrent();        // history rows are never edited

            $table->index(['product_id', 'created_at']);
            $table->index(['product_variant_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
};