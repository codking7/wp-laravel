<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password', 60);
            $table->rememberToken();

            // Two-Factor Authentication Columns...
            $table->string('phone_country_code')->nullable();
            $table->string('phone_number')->nullable();
            $table->text('two_factor_options')->nullable();

            // Team Columns...
            $table->integer('current_team_id')->nullable();

            // Cashier Columns...
            $table->tinyInteger('stripe_active')->default(0);
            $table->string('stripe_id')->nullable();
            $table->string('stripe_subscription')->nullable();
            $table->string('stripe_plan', 100)->nullable();
            $table->string('last_four', 4)->nullable();
            $table->text('extra_billing_info')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('subscription_ends_at')->nullable();

            $table->timestamps();

            /* First Name Field */
            $table->string('first_name')->nullable()->default(null);

            /* Last Name Field */
            $table->string('last_name')->nullable()->default(null);

            /* Name of Company - nullable if not a client */
            $table->string('company_name')->nullable()->default(null);

            /* This is used to store the ssh key of a developer */
            $table->text('developer_key')->nullable()->default(null);

            /* This is a boolean to control who gets mastermind access */
            $table->boolean('is_mastermind')->default(false);

            /* This is a boolean to control who gets admin access */
            $table->boolean('is_admin')->default(false);

            /* This is a boolean to control if a user is a contractor or not */
            $table->boolean('is_contractor')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users');
    }
}
