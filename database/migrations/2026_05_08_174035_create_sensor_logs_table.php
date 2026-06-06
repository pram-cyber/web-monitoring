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
    Schema::create('sensor_logs', function (Blueprint $table) {
        $table->id();
        $table->foreignId('trash_bin_id')->constrained()->onDelete('cascade');
        
        // Data sensor ultrasonik
        $table->float('distance_cm');               // jarak benda ke sensor (cm)
        $table->integer('percentage');              // hasil konversi % kepenuhan
        $table->enum('status', ['empty', 'half', 'full']);
        
        // Data sensor GPS
        $table->decimal('latitude', 10, 7);         // koordinat saat log dibuat
        $table->decimal('longitude', 10, 7);        // koordinat saat log dibuat
        $table->float('gps_accuracy')->nullable();  // akurasi GPS dalam meter
        
        $table->timestamp('recorded_at')->useCurrent(); // waktu pembacaan sensor
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('sensor_logs');
}
};
