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
        Schema::table('users', function (Blueprint $table) {
            $table->string('reset_token')->nullable()->after('password'); // Token for resetting password
            $table->timestamp('reset_token_expires_at')->nullable()->after('reset_token'); // Expiration time of the reset token
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['reset_token', 'reset_token_expires_at']);
        });
    }
};
