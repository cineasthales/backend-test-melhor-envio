<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResponseHeaders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('response_headers', function (Blueprint $table) {
            $table->id();
            $table->string('content_length')->nullable();
            $table->string('via')->nullable();
            $table->string('connection')->nullable();
            $table->string('access_control_allow_credentials')->nullable();
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
        Schema::dropIfExists('response_headers');
    }
}
