<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prks', function (Blueprint $table) {
            $table->id();
            $table->string('nama_project')->nullable();
            $table->string('nomor_prk')->nullable();
            $table->string('lot_number')->nullable();
            $table->tinyInteger('prioritas')->default(0);
            $table->integer('project_id');
            $table->enum('basket', [1, 2, 3])->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prks');
    }
}
