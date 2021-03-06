<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKontrakMaterialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kontrak_materials', function (Blueprint $table) {
            $table->id();
            $table->string('kode_normalisasi');
            $table->string('nama_material');
            $table->integer('harga');
            $table->integer('jumlah');
            $table->string('satuan');
            $table->integer('kontrak_id')->unsigned();
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
        Schema::dropIfExists('kontrak_materials');
    }
}
