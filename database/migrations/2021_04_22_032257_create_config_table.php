<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('config', function (Blueprint $table) {
            // $table->id();
            // $table->timestamps();
            $table->string('config_name');
            $table->text('config_value');
        });
        DB::table('config')->insert([
            ['config_name' => 'website_name', 'config_value' => 'RadTriads'],
            ['config_name' => 'website_tagline', 'config_value' => 'Digital Media Hosting'],
            ['config_name' => 'ads_code', 'config_value' => ''],
            ['config_name' => 'allow_button', 'config_value' => '1'],
            ['config_name' => 'allow_drag', 'config_value' => '1'],
            ['config_name' => 'allow_webcam', 'config_value' => '0'],
            ['config_name' => 'analytics_code', 'config_value' => ''],
            ['config_name' => 'max_upload_size', 'config_value' => '1000'],
            ['config_name' => 'max_files_upload', 'config_value' => '100'],
            ['config_name' => 'auto_deletion', 'config_value' => '0'],
            ['config_name' => 'auto_deletion_days', 'config_value' => '14'],
            ['config_name' => 'auto_deletion_last_date', 'config_value' => '2020-01-15'],
            ['config_name' => 'website_logo', 'config_value' => 'uploads/20200811/79879859505a5a7e81cc087b3747a4d530711a5b.png'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('config');
    }
}
