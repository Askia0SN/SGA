<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('candidatures', function (Blueprint $table) {
            $table->string('code_suivi', 20)->nullable()->change();
            $table->string('statut')->default('brouillon')->change();
        });
    }

    public function down(): void
    {
        Schema::table('candidatures', function (Blueprint $table) {
            $table->string('code_suivi', 20)->nullable(false)->change();
            $table->string('statut')->default('soumise')->change();
        });
    }
};
