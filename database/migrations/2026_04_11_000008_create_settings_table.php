<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_settings', function (Blueprint $table) {
            $table->string('id')->primary()->default('default');
            $table->boolean('free_shipping_enabled')->default(true);
            $table->integer('free_shipping_min_amount')->default(500000);
            $table->boolean('store_pickup_enabled')->default(true);
            $table->boolean('envio_propio_enabled')->default(true);
            $table->json('zones')->default('[]');
            $table->boolean('aex_enabled')->default(false);
            $table->string('aex_api_user')->nullable();
            $table->string('aex_api_password')->nullable();
            $table->string('aex_environment')->default('sandbox');
            $table->boolean('aex_is_validated')->default(false);
            $table->string('aex_webhook_url')->nullable();
            $table->timestamps();
        });

        Schema::create('payment_settings', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->string('provider');
            $table->boolean('is_enabled')->default(false);
            $table->boolean('is_validated')->default(false);
            $table->text('public_key')->nullable();
            $table->text('private_key')->nullable();
            $table->text('webhook_secret')->nullable();
            $table->string('environment')->default('sandbox');
            $table->json('settings')->default('{}');
            $table->timestamps();

            $table->unique('provider');
        });

        Schema::create('hcaptcha_settings', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->boolean('is_enabled')->default(false);
            $table->boolean('is_validated')->default(false);
            $table->string('site_key')->nullable();
            $table->string('secret_key')->nullable();
            $table->boolean('protect_login')->default(true);
            $table->boolean('protect_register')->default(true);
            $table->boolean('protect_newsletter')->default(false);
            $table->timestamps();
        });

        Schema::create('smtp_settings', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->string('host')->nullable();
            $table->integer('port')->default(587);
            $table->string('username')->nullable();
            $table->text('password')->nullable();
            $table->enum('encryption', ['none', 'ssl', 'tls'])->default('tls');
            $table->string('from_email')->nullable();
            $table->string('from_name')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('email_templates', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->string('template_key')->unique();
            $table->string('name');
            $table->string('subject');
            $table->longText('body_html');
            $table->json('variables')->default('[]');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('analytics_settings', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->boolean('ga4_enabled')->default(false);
            $table->string('ga4_measurement_id')->nullable();
            $table->boolean('meta_pixel_enabled')->default(false);
            $table->string('meta_pixel_id')->nullable();
            $table->boolean('gtm_enabled')->default(false);
            $table->string('gtm_container_id')->nullable();
            $table->boolean('track_view_item')->default(true);
            $table->boolean('track_add_to_cart')->default(true);
            $table->boolean('track_begin_checkout')->default(true);
            $table->boolean('track_purchase')->default(true);
            $table->timestamps();
        });

        Schema::create('seo_settings', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->string('page_key')->unique();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('keywords')->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('og_image')->nullable();
            $table->timestamps();
        });

        // Seed shipping_settings with default record
        DB::table('shipping_settings')->insert([
            'id' => 'default',
            'free_shipping_enabled' => true,
            'free_shipping_min_amount' => 500000,
            'store_pickup_enabled' => true,
            'envio_propio_enabled' => true,
            'zones' => '[]',
            'aex_enabled' => false,
            'aex_environment' => 'sandbox',
            'aex_is_validated' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_settings');
        Schema::dropIfExists('analytics_settings');
        Schema::dropIfExists('email_templates');
        Schema::dropIfExists('smtp_settings');
        Schema::dropIfExists('hcaptcha_settings');
        Schema::dropIfExists('payment_settings');
        Schema::dropIfExists('shipping_settings');
    }
};
