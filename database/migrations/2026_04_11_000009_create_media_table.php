<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_files', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_url');
            $table->string('mime_type')->nullable();
            $table->integer('file_size')->nullable();
            $table->string('alt_text')->nullable();
            $table->uuid('uploaded_by')->nullable();
            $table->timestamps();
        });

        Schema::create('media_usages', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->uuid('media_id');
            $table->string('entity_type');
            $table->uuid('entity_id');
            $table->string('field_name')->nullable();
            $table->timestamps();

            $table->foreign('media_id')->references('id')->on('media_files')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_usages');
        Schema::dropIfExists('media_files');
    }
};
