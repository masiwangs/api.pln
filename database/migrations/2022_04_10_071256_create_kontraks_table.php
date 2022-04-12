<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKontraksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kontraks', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_kontrak')->nullable();
            $table->date('tanggal_kontrak')->nullable();
            $table->date('tanggal_awal')->nullable();
            $table->date('tanggal_akhir')->nullable();
            $table->string('pelaksana')->nullable();
            $table->string('direksi_pelaksana')->nullable();
            $table->integer('pengadaan_id')->unsigned();
            $table->boolean('is_amandemen')->default(false);
            $table->integer('versi_amandemen')->nullable();
            $table->integer('amandemen_id')->nullable();
            $table->integer('basket_id')->default(1);
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
        Schema::dropIfExists('kontraks');
    }
}
