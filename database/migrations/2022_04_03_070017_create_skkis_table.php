<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSkkisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('skkis', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_skki');
            $table->string('nomor_prk_skki');
            $table->string('nomor_wbs_jasa');
            $table->string('nomor_wbs_material');
            $table->string('prks');
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
        Schema::dropIfExists('skkis');
    }
}
