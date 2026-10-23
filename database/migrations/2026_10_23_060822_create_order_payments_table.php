<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Money movements of an order: the cash collected on delivery now, online payments later.
        Schema::create('order_payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')->constrained()->cascadeOnDelete();

            $table->string('method', 20);                              // cod | online
            $table->string('gateway', 30)->nullable();                 // jazzcash | easypaisa | gopayfast | bank_account
            $table->decimal('amount', 12, 2);
            $table->string('status', 20)->default('pending');          // pending | paid | failed | refunded
            $table->string('reference', 100)->nullable();              // gateway transaction id / bank slip number
            $table->json('payload')->nullable();                       // raw answer from the gateway
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();

            $table->index(['order_id', 'status']);
            $table->index('reference');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_payments');
    }
};
