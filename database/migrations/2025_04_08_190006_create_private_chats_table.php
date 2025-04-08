<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('private_chats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_one_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->foreignId('user_two_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->timestamp('created_at')
                  ->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('private_chats');
    }
};
