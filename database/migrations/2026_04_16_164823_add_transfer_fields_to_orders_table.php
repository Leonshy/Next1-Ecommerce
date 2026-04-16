<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('payment_method', 50)->nullable()->after('notes');
                $table->string('transfer_receipt')->nullable()->after('payment_method');
            });

            DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM(
                'pendiente',
                'pendiente_transferencia',
                'confirmado',
                'procesando',
                'enviado',
                'entregado',
                'cancelado'
            ) DEFAULT 'pendiente'");

        } elseif (DB::getDriverName() === 'sqlite') {
            // SQLite no permite modificar CHECK constraints ni columnas fácilmente.
            // Recreamos la tabla con la estructura actualizada.
            DB::statement('PRAGMA foreign_keys = OFF');

            DB::statement('ALTER TABLE orders RENAME TO orders_backup');

            DB::statement('CREATE TABLE "orders" (
                "id" varchar not null default (UUID()),
                "order_number" varchar not null,
                "user_id" varchar,
                "status" varchar check ("status" in (
                    \'pendiente\', \'pendiente_transferencia\', \'confirmado\',
                    \'procesando\', \'enviado\', \'entregado\', \'cancelado\'
                )) not null default \'pendiente\',
                "customer_name" varchar not null,
                "customer_email" varchar not null,
                "customer_phone" varchar,
                "shipping_address" text,
                "shipping_city" varchar,
                "subtotal" numeric not null,
                "discount" numeric not null default \'0\',
                "shipping_cost" numeric not null default \'0\',
                "total" numeric not null,
                "notes" text,
                "payment_method" varchar,
                "transfer_receipt" varchar,
                "bancard_process_id" integer,
                "guest_access_token" varchar,
                "created_at" datetime,
                "updated_at" datetime,
                foreign key("user_id") references "users"("id") on delete set null,
                primary key ("id")
            )');

            DB::statement('INSERT INTO orders (
                id, order_number, user_id, status, customer_name, customer_email,
                customer_phone, shipping_address, shipping_city, subtotal, discount,
                shipping_cost, total, notes, bancard_process_id, guest_access_token,
                created_at, updated_at
            ) SELECT
                id, order_number, user_id, status, customer_name, customer_email,
                customer_phone, shipping_address, shipping_city, subtotal, discount,
                shipping_cost, total, notes, bancard_process_id, guest_access_token,
                created_at, updated_at
            FROM orders_backup');

            DB::statement('DROP TABLE orders_backup');
            DB::statement('PRAGMA foreign_keys = ON');
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn(['payment_method', 'transfer_receipt']);
            });

            DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM(
                'pendiente','confirmado','procesando','enviado','entregado','cancelado'
            ) DEFAULT 'pendiente'");
        }
    }
};
