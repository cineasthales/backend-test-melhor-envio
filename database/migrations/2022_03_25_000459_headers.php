<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Headers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('headers', function (Blueprint $table) {
            $table->id();
            $table->string('accept')->nullable();
            $table->string('host')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('content_length')->nullable();
            $table->string('via')->nullable();
            $table->string('connection')->nullable();
            $table->string('access_control_allow_credencials')->nullable();
            $table->string('content_type')->nullable();
            $table->string('server')->nullable();
            $table->string('access_control_allow_origin')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('headers');
    }
}
