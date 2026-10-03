<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete(); // seller who created the product
            $table->foreignId('shop_id')->constrained('shops')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->foreignId('collection_id')->nullable()->constrained('collections')->nullOnDelete();

            $table->string('name');
            $table->string('short_description', 255)->nullable();
            $table->longText('description')->nullable();

            // Default price / total stock (stock = sum of variants when has_variants = true)
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('compare_price', 10, 2)->nullable();
            $table->unsignedInteger('stock')->default(0);

            $table->string('status', 20)->default('active'); // active | draft | inactive
            $table->boolean('is_featured')->default(false);
            $table->boolean('has_variants')->default(false);

            $table->timestamps();

            $table->index(['shop_id', 'status']);
            $table->index('is_featured');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
