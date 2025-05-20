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
    Schema::table('attendances', function (Blueprint $table) {
        $table->decimal('required_hours', 5, 2)->default(8.00)->after('working_hours');
        $table->integer('break_minutes')->nullable()->after('required_hours');
        $table->text('notes')->nullable()->after('leave_id');
        $table->string('status')->default('not_checked_in')->change();
    });
}

public function down()
{
    Schema::table('attendances', function (Blueprint $table) {
        $table->dropColumn(['required_hours', 'break_minutes', 'leave_id', 'notes']);
        $table->string('status')->default('In Progress')->change();
    });
}
};
