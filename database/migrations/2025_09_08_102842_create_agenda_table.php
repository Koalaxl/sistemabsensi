<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('agenda', function (Blueprint $table) {
            $table->id('id_agenda');
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->date('tanggal');
            $table->string('mapel');
            $table->unsignedInteger('id_guru')->nullable(); // ✅ pakai unsignedInteger
            $table->timestamps();

            $table->foreign('id_guru')
                ->references('id_guru')
                ->on('guru')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agenda');
    }
};
