<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_seo_content', function (Blueprint $table) {
            $table->id();
            $table->string('page_key');
            $table->string('locale', 10)->default('en');
            $table->string('title')->nullable();
            $table->text('content');
            $table->text('keywords')->nullable();
            $table->enum('position', ['top', 'bottom'])->default('bottom');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['page_key', 'locale']);
            $table->index('page_key');
            $table->index('locale');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_seo_content');
    }
};
