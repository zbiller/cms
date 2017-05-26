<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAclTables extends Migration
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

            $table->string('username')->unique();
            $table->string('password');
            $table->tinyInteger('super')->default(0);

            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('persons', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index();

            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name')->unique();
            $table->tinyInteger('type')->default(1);

            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name')->unique();
            $table->string('group');
            $table->string('label');

            $table->timestamps();
        });

        Schema::create('user_role', function (Blueprint $table) {
            $table->integer('user_id')->unsigned();
            $table->integer('role_id')->unsigned();

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');

            $table->primary(['user_id', 'role_id']);
        });

        Schema::create('user_permission', function (Blueprint $table) {
            $table->integer('user_id')->unsigned();
            $table->integer('permission_id')->unsigned();

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');

            $table->primary(['user_id', 'permission_id']);
        });

        Schema::create('role_permission', function (Blueprint $table) {
            $table->integer('role_id')->unsigned();
            $table->integer('permission_id')->unsigned();

            $table->timestamps();

            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');

            $table->primary(['role_id', 'permission_id']);
        });

        Schema::create('password_resets', function (Blueprint $table) {
            $table->string('email')->index();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('activity', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index()->nullable();

            $table->nullableMorphs('subject');

            $table->string('name');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activity');
        Schema::dropIfExists('password_resets');
        Schema::dropIfExists('role_permission');
        Schema::dropIfExists('user_permission');
        Schema::dropIfExists('user_role');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('persons');
        Schema::dropIfExists('users');
    }
}
