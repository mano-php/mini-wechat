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
        Schema::dropIfExists('member_address');
        Schema::create('member_address', function (Blueprint $table) {
            $table->comment('收货地址');
            $table->increments('id');
            $table->bigInteger('user_id')->index()->comment('用户');
            $table->string('name')->default('')->comment('姓名');
            $table->string('mobile')->default('')->comment('手机号');
            $table->string('address')->default('')->comment('详细地址');
            $table->string('city')->default('')->comment('城市');
            $table->enum('default', ['yes','no'])->default('no');
            $table->integer('sort')->index()->comment('排序');
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
        Schema::dropIfExists('member_address');
    }
};
