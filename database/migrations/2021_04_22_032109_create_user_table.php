<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id');
            $table->string('username');
            $table->string('password');
            $table->string('password_tmp')->nullable();
            $table->string('email')->unique();
            $table->string('profile_picture')->nullable();
            $table->integer('is_paying')->default(0);
            $table->integer('first_pay')->default(0);
            $table->integer('has_seen_10_days_left_popup')->default(0);
            $table->string('stripe_subscription_id')->nullable();
            $table->string('stripe_customer_id')->nullable();
            $table->string('stripe_plan')->nullable();
            $table->integer('plan_id')->nullable();
            $table->integer('show_direct_link')->default(0);
            $table->integer('show_html_code')->default(0);
            $table->integer('show_forum_code')->default(0);
            $table->integer('show_social_share')->default(0);
            $table->integer('is_account_public')->default(0);
            $table->integer('email_verified')->default(0);
            $table->string('email_activation_code')->nullable();
            $table->integer('rank')->nullable();
            $table->integer('status')->nullable();
            $table->integer('first_pay_admin')->default(0);
            $table->string('stripe_plan_admin')->nullable();
            $table->string('payment_tax')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->nullable();
            $table->string('locality')->nullable();

            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('user');
    }
}
