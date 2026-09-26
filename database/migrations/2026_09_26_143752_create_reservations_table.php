<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('menunggu');
            $table->unsignedSmallInteger('antrean')->default(1);
            $table->timestamp('berlaku_sampai');
            $table->timestamp('diselesaikan_at')->nullable();
            $table->text('alasan_batal')->nullable();
            $table->timestamps();

            $table->index(['book_id', 'status', 'antrean']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
