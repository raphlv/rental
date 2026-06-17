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
        Schema::create('rainfall_data', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->double('tavg')->nullable()->comment('Suhu Rata-rata (°C)');
            $table->double('rh_avg')->nullable()->comment('Kelembapan Rata-rata (%)');
            $table->double('rr')->nullable()->comment('Curah Hujan (mm)');
            $table->integer('class_actual')->default(0)->comment('0: Normal, 1: Ekstrem');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rainfall_data');
    }
};
