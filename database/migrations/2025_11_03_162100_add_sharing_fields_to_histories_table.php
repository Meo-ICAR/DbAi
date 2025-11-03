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
        Schema::connection('dbai')->table('histories', function (Blueprint $table) {
            $table->string('share_token')->nullable()->unique()->after('user_id');
            $table->timestamp('share_expires_at')->nullable()->after('share_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('dbai')->table('histories', function (Blueprint $table) {
            $table->dropColumn(['share_token', 'share_expires_at']);
        });
    }
};
