<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('warga', function (Blueprint $table) {
            $table->string('no_kk')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('warga', function (Blueprint $table) {
            $table->string('no_kk')->nullable(false)->change();
        });
    }
};