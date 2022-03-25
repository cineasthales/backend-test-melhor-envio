<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLatencies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('latencies', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('proxy')->nullable();
            $table->unsignedSmallInteger('kong')->nullable();
            $table->unsignedSmallInteger('request')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('latencies');
    }
}
