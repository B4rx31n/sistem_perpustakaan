<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('anggota')->after('email')->index();
            $table->string('nomor_anggota')->nullable()->unique()->after('role');
            $table->string('no_hp', 20)->nullable()->after('nomor_anggota');
            $table->string('program_studi')->nullable()->after('no_hp');
            $table->date('tanggal_lahir')->nullable()->after('program_studi');
            $table->text('alamat')->nullable()->after('tanggal_lahir');
            $table->string('status')->default('aktif')->after('alamat');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role',
                'nomor_anggota',
                'no_hp',
                'program_studi',
                'tanggal_lahir',
                'alamat',
                'status',
            ]);
        });
    }
};
