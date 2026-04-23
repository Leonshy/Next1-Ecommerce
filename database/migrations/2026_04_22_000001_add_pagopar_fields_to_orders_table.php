<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('pagopar_hash')->nullable()->after('bancard_process_id');
            $table->string('pagopar_order_id')->nullable()->after('pagopar_hash');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['pagopar_hash', 'pagopar_order_id']);
        });
    }
};
