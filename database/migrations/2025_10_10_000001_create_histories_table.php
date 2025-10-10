<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoriesTable extends Migration
{
    public function up()
    {
        Schema::create('histories', function (Blueprint $table) {
            $table->id();
            $table->timestamp('submission_date');
            $table->text('message');
            $table->text('sqlstatement');
            $table->string('charttype')->default('Pie Chart');
            $table->timestamps();
            
            // Add index on sqlstatement for faster lookups
            $table->index('sqlstatement', 'sqlstatement_index');
        });
    }

    public function down()
    {
        Schema::dropIfExists('histories');
    }
}
