<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->string('status')->default('draft');
            $table->timestamp('planned_pickup_at');
            $table->timestamp('planned_return_at');
            $table->timestamp('actual_pickup_at')->nullable();
            $table->timestamp('actual_return_at')->nullable();
            $table->unsignedBigInteger('calculated_base_amount')->default(0);
            $table->unsignedBigInteger('calculated_overage_amount')->default(0);
            $table->unsignedBigInteger('charged_amount')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['vehicle_id', 'status']);
            $table->index(['planned_pickup_at', 'planned_return_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
