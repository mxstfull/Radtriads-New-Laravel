<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreatePlanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plan', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->integer('bandwidth');
            $table->integer('diskspace');
            $table->integer('images');
            $table->double('monthly_price');
            $table->double('yearly_price');
            // $table->timestamps();
        });
        DB::table('plan')->insert([
            ['title' => 'SILVER', 'bandwidth' => '100', 'diskspace' => '30000', 'images' => '3000', 'monthly_price' => '5.49', 'yearly_price' => '59'],
            ['title' => 'GOLD', 'bandwidth' => '200', 'diskspace' => '300000', 'images' => '30000', 'monthly_price' => '7.49', 'yearly_price' => '80.88'],
            ['title' => 'PLATINUM', 'bandwidth' => '500', 'diskspace' => '0', 'images' => '0', 'monthly_price' => '12.59', 'yearly_price' => '135.96'],
            ['title' => 'FREE', 'bandwidth' => '50', 'diskspace' => '100', 'images' => '0', 'monthly_price' => '0', 'yearly_price' => '0']
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('plan')->delete();
        Schema::dropIfExists('plan');
    }
}
