<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promo_slides', function (Blueprint $table) {
            $table->id();
            $table->string('title');                 // internal label, shown in admin list
            $table->string('image_path');            // storage path on the public disk
            $table->string('alt_text')->nullable();  // accessibility label for the rendered <img>
            $table->string('cta_label')->nullable(); // optional button label
            $table->string('cta_url')->nullable();   // optional button target (https:// or wa.me/...)
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promo_slides');
    }
};
