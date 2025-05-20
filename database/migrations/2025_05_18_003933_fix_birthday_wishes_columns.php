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
    Schema::table('birthday_wishes', function (Blueprint $table) {
        if (Schema::hasColumn('birthday_wishes', 'user_id')) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        }
        
        if (!Schema::hasColumn('birthday_wishes', 'receiver_id')) {
            $table->foreignId('receiver_id')->constrained('users');
        }
    });
}

public function down()
{
    Schema::table('birthday_wishes', function (Blueprint $table) {
        $table->foreignId('user_id')->constrained('users');
    });
}
};
