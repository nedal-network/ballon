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
        Schema::table('aircraft_location_pilots', function (Blueprint $table) {
            $table->integer('region_id')->unsigned()->default(1)->after('aircraft_id');
            $table->foreign('region_id')->references('id')->on('regions');
            $table->integer('location_id')->nullable()->unsigned()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aircraft_location_pilots', function (Blueprint $table) {
            $table->dropColumn(['region_id']);
            $table->integer('location_id')->unsigned()->change();
        });
    }
};
