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
    Schema::table('users', function (Blueprint $table) {
        $table->enum('role', ['admin', 'user'])->default('user');
        $table->decimal('latitude', 10, 7)->nullable();  // lokasi user
        $table->decimal('longitude', 10, 7)->nullable(); // lokasi user
        $table->integer('notification_radius')->default(100); // radius notifikasi (meter)
        $table->boolean('is_active')->default(true);
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['role', 'latitude', 'longitude', 'notification_radius', 'is_active']);
    });
}
};
