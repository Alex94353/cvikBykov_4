<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 64)->unique();
            $table->timestamps(); // created_at a updated_at
        });
    }

    public function down(): void {
        Schema::dropIfExists('categories');
    }
};
