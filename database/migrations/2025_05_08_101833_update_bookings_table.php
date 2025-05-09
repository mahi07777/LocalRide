<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Change extra_km and extra_hr to decimal
            $table->decimal('extra_km', 6, 2)->default(0)->change();
            $table->decimal('extra_hr', 6, 2)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Revert changes to extra_km and extra_hr to unsignedInteger
            $table->unsignedInteger('extra_km')->change();
            $table->unsignedInteger('extra_hr')->change();
        });
    }
}
