<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Categorias geridas pelo admin nas Definições
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        // uploads.category passa de enum fixo para string (slug da categoria)
        Schema::table('uploads', function (Blueprint $table) {
            $table->dropIndex(['type', 'category']);
            $table->dropColumn('category');
        });
        Schema::table('uploads', function (Blueprint $table) {
            $table->string('category')->nullable()->index()->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('uploads', function (Blueprint $table) {
            $table->dropColumn('category');
        });
        Schema::table('uploads', function (Blueprint $table) {
            $table->enum('category', ['autores', 'noticias', 'usuarios', 'publicidade'])->nullable()->after('type');
        });
        Schema::dropIfExists('categories');
    }
};
