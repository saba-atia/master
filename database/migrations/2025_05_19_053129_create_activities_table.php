<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->text('details')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // المستخدم الذي قام بالنشاط
            $table->morphs('subject'); // يمكن ربط النشاط بأي نموذج (مثل User, Vacation...)
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('activities');
    }
};
