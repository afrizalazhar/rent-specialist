<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->string('type');              // 'car' | 'suv' | 'motorcycle'
            $table->string('status')->default('available');
            $table->string('make');
            $table->string('model');
            $table->unsignedSmallInteger('year');
            $table->string('plate_number')->unique();
            $table->string('color')->nullable();
            $table->json('attributes_json')->nullable();   // type-conditional specs
            $table->json('photos')->nullable();            // list of storage paths
            $table->unsignedBigInteger('daily_rate');      // IDR, integer
            $table->unsignedBigInteger('weekly_rate')->nullable();
            $table->unsignedBigInteger('monthly_rate')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
