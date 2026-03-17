<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('web_contents', function (Blueprint $table) {
            $table->id();
            $table->string('section', 80)->index();
            $table->string('key', 120)->unique();
            $table->string('name', 160);
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->longText('body')->nullable();
            $table->string('image_path')->nullable();
            $table->string('banner_path')->nullable();
            $table->string('cta_text', 120)->nullable();
            $table->string('cta_url')->nullable();
            $table->json('meta')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['section', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('web_contents');
    }
};
