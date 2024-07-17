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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('nama_task');
            $table->text('rincian')->nullable();
            $table->text('hasil')->nullable();
            $table->dateTime('batas_waktu')->nullable();
            $table->dateTime('akhir_batas_waktu')->nullable();
            $table->string('status')->default('Belum Dimulai'); // Belum Dimulai, Sedang Berlangsung, Selesai
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('customer_id')->nullable();
            $table->string('priority')->nullable();
            $table->string('category')->nullable();
            $table->point('gps_location')->nullable();
            $table->timestamps();
        });

        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->string('file_path');
            $table->timestamps();
        });

        // Associated Records Table
        Schema::create('associated_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->string('record_type'); // 'kontak', 'perusahaan', atau 'deal'
            $table->string('record_name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('attachments');
        Schema::dropIfExists('associated_records');
    }
};
