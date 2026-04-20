<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->string('filter_type')->default('tag')->after('tag'); // tag | category | brand
            $table->uuid('category_id')->nullable()->after('filter_type');
            $table->uuid('brand_id')->nullable()->after('category_id');
        });
    }

    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn(['filter_type', 'category_id', 'brand_id']);
        });
    }
};
