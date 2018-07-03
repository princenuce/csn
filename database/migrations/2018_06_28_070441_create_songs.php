<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSongs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('songs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('unique_id', 50)->unique();
            $table->string('name', 255);
            $table->string('slug', 255);
            $table->integer('listen')->default(0);
            $table->integer('duration')->nullable();
            $table->integer('size')->nullable();
            $table->string('single', 255);
            $table->text('link_play')->nullable();
            $table->text('lyric')->nullable();
            $table->text('url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('songs');
    }
}
