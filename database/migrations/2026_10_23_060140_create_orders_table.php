<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->string('order_number', 30)->unique();               // BC-261024-48392
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // the customer

            // Customer details as they were at checkout (kept even if the account changes or is deleted)
            $table->string('customer_name', 100);
            $table->string('customer_phone', 11);                       // 03000000000
            $table->string('customer_email', 150)->nullable();
            $table->string('delivery_address', 255);
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->text('notes')->nullable();

            // Payment
            $table->string('payment_method', 20);                       // cod | online
            $table->string('online_payment_method', 30)->nullable();    // jazzcash | easypaisa | gopayfast | bank_account
            $table->string('payment_status', 20)->default('unpaid');    // unpaid | paid | failed | refunded
            $table->timestamp('paid_at')->nullable();

            // Amounts (all copied from the server-side checkout totals)
            $table->decimal('subtotal', 12, 2);                         // all items when the order was placed
            $table->decimal('shipping_total', 12, 2)->default(0);
            $table->decimal('total', 12, 2);                            // subtotal + shipping_total (never changes)
            $table->decimal('payable_total', 12, 2);                    // what the customer still has to pay: goes down when items are rejected

            // pending | processing | completed | cancelled  (worked out from the status of the items)
            $table->string('status', 20)->default('pending');
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index('status');
            $table->index('payment_status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
