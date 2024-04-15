<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member', function (Blueprint $table) {
            $table->comment('用户管理');
            $table->increments('id');
            $table->string('nickname')->default('')->nullable()->comment('昵称');
            $table->string('avatar')->default('')->nullable()->comment('头像');
            $table->string('mobile')->index()->default('')->nullable()->comment('手机号');
            $table->string('leader_ids')->default('')->comment('负责人');
            $table->enum('status', ['enable','disable'])->index()->default('enable');
            $table->enum('sex',['1','2'])->default('1')->index()->default('性别');
            $table->date('luck_date')->index()->nullable()->comment('幸运日');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('member');
    }
};
