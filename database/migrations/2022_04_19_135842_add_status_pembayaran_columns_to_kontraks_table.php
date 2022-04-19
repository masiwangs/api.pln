<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusPembayaranColumnsToKontraksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kontraks', function (Blueprint $table) {
            $table->enum('status', [
                'DALAM PELAKSANAAN', 
                'ADMINISTRASI PROYEK',
                'OUTSTANDING',
                'SELESAI BAYAR'
            ])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kontraks', function (Blueprint $table) {
            //
        });
    }
}
