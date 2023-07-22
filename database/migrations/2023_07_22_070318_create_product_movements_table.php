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
        Schema::create('product_movements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('warehouse_from_id')->nullable();
            $table->unsignedBigInteger('warehouse_to_id')->nullable();
            $table->integer('quantity');
            $table->string('movement_type'); // Поле для указания типа движения (прибытие, продажа, перенос).
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('warehouse_from_id')->references('id')->on('warehouses')->onDelete('set null');
            $table->foreign('warehouse_to_id')->references('id')->on('warehouses')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_movements');
    }
};
