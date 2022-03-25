<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Entries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entries', function (Blueprint $table) {
            $table->id();
            $table->string('upstream_uri')->nullable();
            $table->string('client_ip')->nullable();
            $table->unsignedBigInteger('started_at')->nullable();
            $table->unsignedBigInteger('request_id')->index();
            $table->unsignedBigInteger('response_id')->index();
            $table->unsignedBigInteger('authenticated_entity_id')->index();
            $table->unsignedBigInteger('route_id')->index();
            $table->unsignedBigInteger('service_id')->index();
            $table->unsignedBigInteger('latency_id')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entries');
    }
}
