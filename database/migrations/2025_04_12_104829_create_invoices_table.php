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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->integer('client_id')->nullable();
            $table->integer('category')->nullable();
            $table->integer('quantity')->nullable();
            $table->integer('total_price')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('screenshot')->nullable();
            $table->integer('isDeleted')->default(0)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
