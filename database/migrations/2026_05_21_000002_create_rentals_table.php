<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rentals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained('units')->onDelete('cascade');
            $table->string('customer_name');
            $table->integer('duration'); // in hours
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->enum('payment_method', ['Cash', 'Transfer', 'QRIS']);
            $table->string('photo_proof')->nullable();
            $table->enum('status', ['active', 'completed'])->default('active');
            $table->decimal('total_price', 10, 2)->default(0.00);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rentals');
    }
};
