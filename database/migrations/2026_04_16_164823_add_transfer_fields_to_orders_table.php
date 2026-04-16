<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_method', 50)->nullable()->after('notes');
            $table->string('transfer_receipt')->nullable()->after('payment_method');
        });

        // Ampliar el ENUM de status solo en MySQL (SQLite usa varchar, acepta cualquier valor)
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM(
                'pendiente',
                'pendiente_transferencia',
                'confirmado',
                'procesando',
                'enviado',
                'entregado',
                'cancelado'
            ) DEFAULT 'pendiente'");
        }
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'transfer_receipt']);
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM(
                'pendiente','confirmado','procesando','enviado','entregado','cancelado'
            ) DEFAULT 'pendiente'");
        }
    }
};
