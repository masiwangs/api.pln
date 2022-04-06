<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSkkiMaterialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('skki_materials', function (Blueprint $table) {
            $table->id();
            $table->string('kode_normalisasi');
            $table->string('nama_material');
            $table->integer('jumlah');
            $table->integer('harga');
            $table->integer('skki_id');
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
        Schema::dropIfExists('skki_materials');
    }
}
