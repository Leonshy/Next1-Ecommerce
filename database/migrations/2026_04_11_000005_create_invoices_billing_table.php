<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->string('invoice_number')->unique();
            $table->uuid('order_id');
            $table->uuid('user_id')->nullable();
            $table->string('taxpayer_type')->default('persona_fisica');
            $table->string('ruc')->nullable();
            $table->string('ruc_dv')->nullable();
            $table->string('business_name');
            $table->string('trade_name')->nullable();
            $table->text('fiscal_address')->nullable();
            $table->string('billing_city')->nullable();
            $table->string('billing_department')->nullable();
            $table->string('billing_email')->nullable();
            $table->string('billing_phone')->nullable();
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone')->nullable();
            $table->text('shipping_address')->nullable();
            $table->string('shipping_city')->nullable();
            $table->decimal('subtotal', 15, 2);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('shipping_cost', 15, 2)->default(0);
            $table->decimal('total', 15, 2);
            $table->json('items')->default('[]');
            $table->string('status')->default('emitida');
            $table->timestamp('issued_at')->useCurrent();
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });

        Schema::create('billing_data', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->uuid('user_id');
            $table->string('taxpayer_type')->default('persona_fisica');
            $table->string('ruc')->nullable();
            $table->string('ruc_dv')->nullable();
            $table->string('business_name');
            $table->string('trade_name')->nullable();
            $table->text('fiscal_address')->nullable();
            $table->string('city')->nullable();
            $table->string('department')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billing_data');
        Schema::dropIfExists('invoices');
    }
};
