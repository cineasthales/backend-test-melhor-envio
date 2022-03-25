<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Routes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('routes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_at')->nullable();
            $table->string('hosts')->nullable();
            $table->string('id_string')->nullable();
            $table->boolean('preserve_host')->default(false);
            $table->unsignedTinyInteger('regex_priority')->nullable();
            $table->boolean('strip_path')->default(true);
            $table->unsignedBigInteger('updated_at')->nullable();
            $table->unsignedBigInteger('service_id')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('routes');
    }
}
