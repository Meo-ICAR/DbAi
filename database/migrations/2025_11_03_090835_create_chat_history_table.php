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
        Schema::connection('dbai')->create('chat_history', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->string('thread_id', 255);
            $table->longText('messages');
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();
            
            $table->unique('thread_id', 'uk_thread_id');
            $table->index('thread_id', 'idx_thread_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('dbai')->dropIfExists('chat_history');
    }
};
