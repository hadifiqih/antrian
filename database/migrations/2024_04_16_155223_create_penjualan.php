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
        Schema::create('penjualan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('restrict');
            $table->foreignId('sales_id')->constrained('sales')->onDelete('restrict');
            $table->string('no_invoice')->unique();
            $table->string('total');
            $table->string('diskon');
            $table->string('bayar');
            $table->text('keterangan');
            $table->string('metode_pembayaran');
            $table->string('ekspedisi');
            $table->string('ongkir');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penjualan');
    }
};
