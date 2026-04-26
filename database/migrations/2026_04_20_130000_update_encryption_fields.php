<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add new encryption fields (tag, hkdf_salt, kem_private_key) to both
     * chat_messages and private_messages tables, and make existing encryption
     * columns nullable on private_messages (to match chat_messages and support
     * media-only messages).
     */
    public function up(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->text('tag')->nullable()->after('iv');
            $table->text('hkdf_salt')->nullable()->after('tag');
            $table->text('kem_private_key')->nullable()->after('hkdf_salt');
        });

        Schema::table('private_messages', function (Blueprint $table) {
            // Make existing encryption columns nullable for media-only messages
            $table->text('encrypted_message')->nullable()->change();
            $table->string('iv')->nullable()->change();
            $table->text('kyber_ciphertext')->nullable()->change();

            // Add new encryption fields
            $table->text('tag')->nullable()->after('iv');
            $table->text('hkdf_salt')->nullable()->after('tag');
            $table->text('kem_private_key')->nullable()->after('hkdf_salt');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropColumn(['tag', 'hkdf_salt', 'kem_private_key']);
        });

        Schema::table('private_messages', function (Blueprint $table) {
            $table->dropColumn(['tag', 'hkdf_salt', 'kem_private_key']);

            $table->text('encrypted_message')->nullable(false)->change();
            $table->string('iv')->nullable(false)->change();
            $table->text('kyber_ciphertext')->nullable(false)->change();
        });
    }
};
