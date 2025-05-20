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
        // إذا كنت تريد استخدام receiver_id بدلاً من user_id
        if (!Schema::hasColumn('birthday_wishes', 'receiver_id')) {
            $table->foreignId('receiver_id')->constrained('users')->after('sender_id');
        }
        
        // أو إذا كنت تريد استخدام user_id بدلاً من receiver_id
        // $table->renameColumn('receiver_id', 'user_id');
    });
}

public function down()
{
    Schema::table('birthday_wishes', function (Blueprint $table) {
        // التراجع عن التغييرات
        $table->dropForeign(['receiver_id']);
        $table->dropColumn('receiver_id');
    });
}
};
