<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTug9ColumnToPelaksanaanMaterialTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pelaksanaan_material_transactions', function (Blueprint $table) {
            $table->string('tug9')->nullable()->after('transaction');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pelaksanaan_material_transactions', function (Blueprint $table) {
            //
        });
    }
}
