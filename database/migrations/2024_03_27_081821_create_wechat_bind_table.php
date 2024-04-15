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
        Schema::create('wechat_bind', function (Blueprint $table) {
            $table->comment('微信授权');
            $table->increments('id');
            $table->bigInteger('user_id')->index()->nullable()->comment('用户ID');
            $table->string('unionid')->index()->default('')->comment('平台ID');
            $table->string('openid')->index()->nullable()->comment('开放ID');
            $table->enum('platform', ['mp-wechat','mini-wechat','app-wechat','web-wechat'])->index()->comment('授权平台');
            $table->enum('status', ['enable','disable'])->index()->default('enable')->comment('状态');
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
        Schema::dropIfExists('wechat_bind');
    }
};
