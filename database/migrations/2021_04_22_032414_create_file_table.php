<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file', function (Blueprint $table) {
            $table->id();
            $table->string('short_id')->nullable();
            $table->string('title')->nullable()->default('');
            $table->string('unique_id');
            $table->string('url');
            $table->string('thumb_url')->nullable();
            $table->string('folder_path')->nullable();
            $table->string('filename');
            $table->string('ext');
            $table->integer('diskspace');
            $table->integer('bandwidth');
            $table->string('ip_address')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('in_community')->default(0);
            $table->integer('upvotes')->default(0);
            $table->integer('comments')->default(0);
            $table->integer('is_picture')->default(0);
            $table->integer('is_deleted')->default(0);
            $table->integer('status')->default(0);
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
        Schema::dropIfExists('file');
    }
}
