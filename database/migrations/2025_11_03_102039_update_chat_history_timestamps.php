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
        Schema::connection('dbai')->table('chat_history', function (Blueprint $table) {
            // First drop the existing timestamps if they exist
            if (Schema::connection('dbai')->hasColumn('chat_history', 'created_at')) {
                $table->dropColumn('created_at');
            }
            if (Schema::connection('dbai')->hasColumn('chat_history', 'updated_at')) {
                $table->dropColumn('updated_at');
            }
            
            // Add them back as nullable
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This is a destructive operation - we can't reliably rollback
        // as we don't know the original state of these columns
    }
};
