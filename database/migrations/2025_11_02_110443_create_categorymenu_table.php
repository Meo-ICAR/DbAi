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
        if (!Schema::connection('dbai')->hasTable('categorymenu')) {
            Schema::connection('dbai')->create('categorymenu', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('description')->nullable();
                $table->string('icon')->nullable();
                $table->integer('order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });

            // Add foreign key to history table if it exists
            if (Schema::connection('dbai')->hasTable('history')) {
                Schema::connection('dbai')->table('history', function (Blueprint $table) {
                    $table->foreignId('categorymenu_id')
                          ->nullable()
                          ->constrained('categorymenu')
                          ->onDelete('SET NULL');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::connection('dbai')->hasTable('history')) {
            Schema::connection('dbai')->table('history', function (Blueprint $table) {
                if (Schema::connection('dbai')->hasColumn('history', 'categorymenu_id')) {
                    $table->dropForeign(['categorymenu_id']);
                    $table->dropColumn('categorymenu_id');
                }
            });
        }
        
        Schema::connection('dbai')->dropIfExists('categorymenu');
    }
};
