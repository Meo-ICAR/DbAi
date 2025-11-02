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
        if (Schema::connection('dbai')->hasTable('history') && 
            !Schema::connection('dbai')->hasColumn('history', 'categorymenu_id')) {
            
            Schema::connection('dbai')->table('history', function (Blueprint $table) {
                $table->foreignId('categorymenu_id')
                      ->nullable()
                      ->after('database_name')
                      ->constrained('categorymenu')
                      ->onDelete('SET NULL');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::connection('dbai')->hasTable('history') && 
            Schema::connection('dbai')->hasColumn('history', 'categorymenu_id')) {
            
            Schema::connection('dbai')->table('history', function (Blueprint $table) {
                $table->dropForeign(['categorymenu_id']);
                $table->dropColumn('categorymenu_id');
            });
        }
    }
};
