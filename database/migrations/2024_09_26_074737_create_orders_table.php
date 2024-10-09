<?php

use App\Enums\OrderStatus;
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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->enum('status', array_column(OrderStatus::cases(), 'value'))->default(OrderStatus::PINDING->value);
            $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('delivery_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedDouble('price');
            $table->date('delivery_date')->nullable();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
