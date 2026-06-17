<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['PS2', 'PS3', 'PS4', 'Nintendo Switch', 'TV 32 Inch']);
            $table->enum('status', ['ada', 'disewa', 'maintenance'])->default('ada');
            $table->decimal('price_per_hour', 10, 2)->default(0.00);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
