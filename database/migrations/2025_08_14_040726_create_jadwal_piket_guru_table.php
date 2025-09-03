<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('jadwal_piket_guru', function (Blueprint $table) {
            $table->id('id_piket');

            // Sesuaikan tipe dengan id_guru di tabel guru
            $table->unsignedInteger('id_guru'); // kalau guru.id_guru pakai increments()
            // Kalau guru.id_guru pakai bigIncrements(), ganti ke unsignedBigInteger()

            $table->string('hari'); // Senin, Selasa, dst
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->string('keterangan')->nullable(); // misalnya "Piket pagi"
            $table->timestamps();

            $table->foreign('id_guru')
                  ->references('id_guru')
                  ->on('guru')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_piket_guru');
    }
};
