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
        // إذا كنت تستخدم Laravel 10+
        $table->foreignId('receiver_id')->constrained('users')->after('sender_id');
        
        // أو للنسخ الأقدم:
        // $table->unsignedBigInteger('receiver_id');
        // $table->foreign('receiver_id')->references('id')->on('users');
    });
}

public function down()
{
    Schema::table('birthday_wishes', function (Blueprint $table) {
        $table->dropForeign(['receiver_id']);
        $table->dropColumn('receiver_id');
    });
}
};
