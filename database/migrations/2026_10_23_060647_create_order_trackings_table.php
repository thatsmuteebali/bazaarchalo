<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The timeline of an order: one row for every important thing that happens.
        Schema::create('order_tracking', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_item_id')->nullable()->constrained('order_items')->cascadeOnDelete(); // empty = about the whole order
            $table->foreignId('seller_id')->nullable()->constrained('users')->nullOnDelete();              // the seller this event is about

            // placed | accepted | rejected | shipped | delivered | cancelled | payment_received | note
            $table->string('status', 30);
            $table->string('title', 150);                              // shown to the customer: "Seller accepted your item"
            $table->text('description')->nullable();                   // e.g. the rejection reason
            $table->string('courier_name', 80)->nullable();
            $table->string('tracking_number', 100)->nullable();
            $table->string('location', 150)->nullable();               // "Lahore sorting centre"
            $table->boolean('is_public')->default(true);               // false = internal note, hidden from the customer

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();             // events are never edited

            $table->index(['order_id', 'created_at']);
            $table->index('order_item_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_tracking');
    }
};
