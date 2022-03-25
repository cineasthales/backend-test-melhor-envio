<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Services extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('connect_timeout')->nullable();
            $table->unsignedBigInteger('created_at')->nullable();
            $table->string('host')->nullable();
            $table->string('id_string')->nullable();
            $table->string('name')->nullable();
            $table->string('path')->nullable();
            $table->unsignedSmallInteger('port')->nullable();
            $table->string('protocol')->nullable();
            $table->unsignedSmallInteger('read_timeout')->nullable();
            $table->unsignedSmallInteger('retries')->nullable();
            $table->unsignedBigInteger('updated_at')->nullable();
            $table->unsignedSmallInteger('write_timeout')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('services');
    }
}
