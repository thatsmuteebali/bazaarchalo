<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')->constrained()->cascadeOnDelete();

            // WHO handles this item: the seller. Sellers only ever see / change rows where seller_id is theirs.
            $table->foreignId('seller_id')->constrained('users')->restrictOnDelete();

            // For information only (shop name on the order page). Not used for permissions or grouping.
            $table->foreignId('shop_id')->nullable()->constrained('shops')->nullOnDelete();

            // The product may be edited or deleted later, so the order keeps its own copy of the details.
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
            $table->string('product_name');
            $table->string('variant_title')->nullable();               // "Red / S"
            $table->string('sku', 100)->nullable();
            $table->string('image_path')->nullable();                  // first product image at order time
            $table->string('shop_name')->nullable();

            $table->decimal('unit_price', 12, 2);
            $table->unsignedInteger('quantity');
            $table->decimal('line_total', 12, 2);                      // unit_price x quantity

            // pending -> accepted | rejected ;  accepted -> shipped -> delivered ;  pending/accepted -> cancelled
            $table->string('status', 20)->default('pending');
            $table->string('status_reason', 255)->nullable();          // why it was rejected / cancelled
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            // Delivery of this item (a seller usually ships all their items of one order together)
            $table->string('courier_name', 80)->nullable();            // TCS, Leopards, ...
            $table->string('tracking_number', 100)->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();

            // Set when the stock of a rejected / cancelled item was given back (prevents giving it back twice)
            $table->timestamp('stock_released_at')->nullable();

            $table->timestamps();

            $table->index(['seller_id', 'status']);
            $table->index(['seller_id', 'created_at']);
            $table->index('order_id');
            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
