<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('dbai')->create('medical_query_audits', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('action', 20)->index();          // ask | execute
            $table->text('domanda')->nullable();
            $table->longText('sql')->nullable();
            $table->string('esito', 20)->default('ok');      // ok | needs_clarification | rejected | error
            $table->unsignedInteger('righe')->nullable();
            $table->text('warning')->nullable();
            $table->text('assunzioni')->nullable();
            $table->text('errore')->nullable();
            $table->string('ip', 45)->nullable();
            $table->string('database_name')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection('dbai')->dropIfExists('medical_query_audits');
    }
};
