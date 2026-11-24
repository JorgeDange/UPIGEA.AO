<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('uploads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['image', 'document']);
            $table->string('original_name');
            $table->string('file_name')->unique();
            $table->string('mime_type');
            $table->unsignedBigInteger('size'); // bytes
            $table->string('path');             // caminho relativo no disco de armazenamento
            $table->string('url');              // URL pública final
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uploads');
    }
};
