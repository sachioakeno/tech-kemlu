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
    Schema::create('wisatawans', function (Blueprint $table) {
        $table->id();
        $table->string('kode_iso', 3);
        $table->string('nama_negara');
        $table->integer('tahun'); 
        $table->decimal('lat', 10, 8)->nullable(); 
        $table->decimal('lng', 11, 8)->nullable(); 
        $table->bigInteger('januari')->default(0);
        $table->bigInteger('februari')->default(0);
        $table->bigInteger('maret')->default(0);
        $table->bigInteger('april')->default(0);
        $table->bigInteger('mei')->default(0);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wisatawans');
    }
};
