<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePelaksanaanMaterialTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pelaksanaan_material_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('kode_normalisasi');
            $table->string('nama_material');
            $table->integer('harga');
            $table->integer('jumlah');
            $table->string('satuan');
            $table->enum('transaction', ['in', 'out'])->nullable()->default('out');
            $table->string('kontrak_id');
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
        Schema::dropIfExists('pelaksanaan_material_transactions');
    }
}
