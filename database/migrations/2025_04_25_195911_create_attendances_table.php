<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('check_in')->nullable();
            $table->timestamp('check_out')->nullable();
            $table->date('date')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'date']); // Prevent duplicate attendance for same user on same day
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendances');
    }
};