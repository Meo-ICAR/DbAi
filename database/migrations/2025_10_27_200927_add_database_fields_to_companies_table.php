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
        Schema::table('companies', function (Blueprint $table) {
            $table->string('db_host')->nullable()->after('aibackground');
            $table->string('db_port')->default('3306')->after('db_host');
            $table->string('db_database')->nullable()->after('db_port');
            $table->string('db_username')->nullable()->after('db_database');
            $table->string('db_password')->nullable()->after('db_username');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'db_host',
                'db_port',
                'db_database',
                'db_username',
                'db_password'
            ]);
        });
    }
};
