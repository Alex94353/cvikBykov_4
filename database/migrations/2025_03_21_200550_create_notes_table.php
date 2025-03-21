<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title', 128);  // Название заметки
            $table->text('body');          // Содержимое заметки
            $table->timestamps();          // created_at и updated_at
        });
    }

    public function down(): void {
        Schema::dropIfExists('notes');
    }
};
