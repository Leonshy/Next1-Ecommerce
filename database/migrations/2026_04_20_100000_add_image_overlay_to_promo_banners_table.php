<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('promo_banners', function (Blueprint $table) {
            $table->string('image_url')->nullable()->after('background_gradient');
            $table->string('overlay_color', 20)->nullable()->default('#000000')->after('image_url');
            $table->decimal('overlay_opacity', 3, 2)->nullable()->default(0.40)->after('overlay_color');
        });
    }

    public function down(): void
    {
        Schema::table('promo_banners', function (Blueprint $table) {
            $table->dropColumn(['image_url', 'overlay_color', 'overlay_opacity']);
        });
    }
};
