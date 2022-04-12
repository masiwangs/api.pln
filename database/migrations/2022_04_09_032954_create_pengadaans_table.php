<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePengadaansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pengadaans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_prk_skkis')->nullable();
            $table->string('nodin')->nullable();
            $table->string('nomor_pr')->nullable();
            $table->string('nama_project')->nullable();
            $table->date('tanggal_awal')->nullable();
            $table->date('tanggal_akhir')->nullable();
            $table->enum('status', ['proses', 'terkontrak'])->nullable();
            $table->string('nomor_wbs_jasas')->nullable();
            $table->string('nomor_wbs_materials')->nullable();
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
        Schema::dropIfExists('pengadaans');
    }
}
