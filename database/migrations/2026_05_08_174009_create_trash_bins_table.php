<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('trash_bins', function (Blueprint $table) {
        $table->id();
        $table->string('name');                      // nama tempat sampah
        $table->string('location')->nullable();      // nama lokasi/area
        $table->decimal('latitude', 10, 7);          // dari sensor GPS
        $table->decimal('longitude', 10, 7);         // dari sensor GPS
        $table->integer('percentage')->default(0);   // % kepenuhan dari ultrasonik
        $table->float('distance_cm')->default(0);    // jarak dari sensor ultrasonik (cm)
        $table->integer('max_depth_cm');             // tinggi max tempat sampah (cm)
        $table->enum('status', ['empty', 'half', 'full'])->default('empty');
        $table->boolean('is_active')->default(true); // apakah bin aktif
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('trash_bins');
}
};
