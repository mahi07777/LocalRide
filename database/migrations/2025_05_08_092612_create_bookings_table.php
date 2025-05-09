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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('pickup_address');
            $table->string('drop_address');
            $table->decimal('distance_km', 6, 2);
            $table->decimal('duration_hr', 6, 2);
            $table->foreignId('package_id')->constrained();
            $table->unsignedInteger('extra_km');
            $table->unsignedInteger('extra_hr');
            $table->decimal('final_price', 10, 2);
            $table->timestamps();            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
