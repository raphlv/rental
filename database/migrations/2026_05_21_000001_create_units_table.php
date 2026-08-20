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
            $table->string('code')->unique(); // e.g., PS3-01, PS4-15
            $table->string('name'); // e.g., Unit 01 - PS3 Slim
            $table->enum('type', ['PS 3', 'PS 4', 'PS 5', 'Nintendo Switch', 'TV Only'])->default('PS 4');
            $table->enum('status', ['ada', 'disewa', 'maintenance'])->default('ada');
            $table->decimal('price_per_hour', 10, 2)->default(10000.00);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
