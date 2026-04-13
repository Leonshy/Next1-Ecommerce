<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hero_slides', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->string('image_url');
            $table->string('button_text')->default('Ver más');
            $table->string('button_link')->default('/productos');
            $table->integer('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('promo_banners', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();
            $table->text('background_gradient')->nullable();
            $table->string('text_color')->default('white');
            $table->string('button_text')->default('Ver más');
            $table->string('button_link')->default('/productos');
            $table->string('button_text_color')->default('blue-700');
            $table->string('icon_type')->default('none');
            $table->string('watermark_text')->nullable();
            $table->integer('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('campaigns', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->string('name');
            $table->string('tag')->nullable();
            $table->text('description')->nullable();
            $table->string('banner_image')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('display_on_home')->default(false);
            $table->integer('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('gift_cards', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->string('code')->unique();
            $table->decimal('amount', 15, 2);
            $table->decimal('balance', 15, 2);
            $table->enum('status', ['pendiente', 'activa', 'usada', 'expirada', 'cancelada'])->default('pendiente');
            $table->string('buyer_name')->nullable();
            $table->string('buyer_email')->nullable();
            $table->string('buyer_phone')->nullable();
            $table->string('recipient_name')->nullable();
            $table->string('recipient_email')->nullable();
            $table->text('message')->nullable();
            $table->string('access_token')->nullable()->unique();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('purchased_at')->nullable();
            $table->timestamps();
        });

        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->string('email')->unique();
            $table->enum('status', ['pendiente', 'verificado'])->default('pendiente');
            $table->timestamp('subscribed_at')->useCurrent();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });

        Schema::create('site_content', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->string('key')->unique();
            $table->string('title')->nullable();
            $table->longText('content')->nullable();
            $table->json('metadata')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_content');
        Schema::dropIfExists('newsletter_subscribers');
        Schema::dropIfExists('gift_cards');
        Schema::dropIfExists('campaigns');
        Schema::dropIfExists('promo_banners');
        Schema::dropIfExists('hero_slides');
    }
};
