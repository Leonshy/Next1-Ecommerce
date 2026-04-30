<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_settings', function (Blueprint $table) {
            $table->decimal('discount_percentage', 5, 2)->default(0)->after('settings');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('payment_discount', 15, 2)->default(0)->after('discount');
        });
    }

    public function down(): void
    {
        Schema::table('payment_settings', function (Blueprint $table) {
            $table->dropColumn('discount_percentage');
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('payment_discount');
        });
    }
};
