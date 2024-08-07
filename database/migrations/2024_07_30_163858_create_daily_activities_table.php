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
        Schema::create('daily_activities', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('sales_id')->foreign('sales_id')->references('id')->on('sales')->onDelete('cascade')->unsigned();
            $table->bigInteger('user_id')->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->unsigned();
            $table->string('platform');
            $table->string('jenis_konten');
            $table->string('keterangan')->nullable();
            $table->integer('jumlah')->unsigned()->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_activities');
    }
};
