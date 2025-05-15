<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('branches', function (Blueprint $table) {
        $table->float('latitude')->nullable();
        $table->float('longitude')->nullable();
    });
}

public function down()
{
    Schema::table('branches', function (Blueprint $table) {
        $table->dropColumn('latitude');
        $table->dropColumn('longitude');
    });
}

};
