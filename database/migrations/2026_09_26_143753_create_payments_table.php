<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('loan_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('petugas_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('jumlah');
            $table->string('metode')->default('tunai');
            $table->string('bukti')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamp('dibayar_pada');
            $table->timestamps();

            $table->index(['user_id', 'dibayar_pada']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
