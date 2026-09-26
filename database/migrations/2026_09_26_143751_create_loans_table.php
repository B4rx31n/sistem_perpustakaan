<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('petugas_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('dipinjam_at');
            $table->date('harus_kembali_at');
            $table->timestamp('dikembalikan_at')->nullable();
            $table->unsignedTinyInteger('perpanjangan')->default(0);
            $table->string('status')->default('dipinjam');
            $table->unsignedInteger('denda')->default(0);
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index(['status', 'harus_kembali_at']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
