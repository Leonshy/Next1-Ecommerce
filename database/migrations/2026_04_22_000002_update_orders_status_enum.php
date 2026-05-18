<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') return;

        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM(
            'pendiente',
            'pendiente_transferencia',
            'pendiente_pagopar',
            'confirmado',
            'procesando',
            'enviado',
            'entregado',
            'cancelado'
        ) NOT NULL DEFAULT 'pendiente'");
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') return;

        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM(
            'pendiente',
            'confirmado',
            'procesando',
            'enviado',
            'entregado',
            'cancelado'
        ) NOT NULL DEFAULT 'pendiente'");
    }
};
