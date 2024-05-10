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
        Schema::create('wechat_setting', function (Blueprint $table) {
            $table->comment('微信设置');
            $table->increments('id');
            $table->enum('type',['mini-wechat','wechat-payment','other'])->comment('类型');
            $table->string('key',50)->index()->comment('键');
            $table->text('value')->comment('值');
            $table->text('label')->comment('标签');
            $table->timestamps();
            $table->softDeletes();
        });
        \ManoCode\MiniWechat\Models\WechatSetting::query()->insert([
            // 小程序示例配置
            [
                'type'=>'mini-wechat',
                'key'=>'appid',
                'value'=>'',
                'label'=>'应用ID',
                'created_at'=>date('Y-m-d H:i:s')
            ],
            [
                'type'=>'mini-wechat',
                'key'=>'secret',
                'value'=>'',
                'label'=>'应用秘钥',
                'created_at'=>date('Y-m-d H:i:s')
            ],
            [
                'type'=>'mini-wechat',
                'key'=>'token',
                'value'=>'token-mini-app',
                'label'=>'Token',
                'created_at'=>date('Y-m-d H:i:s')
            ],
            [
                'type'=>'mini-wechat',
                'key'=>'aes_key',
                'value'=>'',
                'label'=>'aes_key',
                'created_at'=>date('Y-m-d H:i:s')
            ],
            // 微信支付示例配置
            [
                'type'=>'wechat-payment',
                'key'=>'mch_id',
                'value'=>'1360649000',
                'label'=>'商户ID',
                'created_at'=>date('Y-m-d H:i:s')
            ],
            [
                'type'=>'wechat-payment',
                'key'=>'certificate',
                'value'=>'',
                'label'=>'证书内容',
                'created_at'=>date('Y-m-d H:i:s')
            ],
            [
                'type'=>'wechat-payment',
                'key'=>'private_key',
                'value'=>'',
                'label'=>'证书秘钥',
                'created_at'=>date('Y-m-d H:i:s')
            ],
            [
                'type'=>'wechat-payment',
                'key'=>'secret_key',
                'value'=>'',
                'label'=>'v3 API 秘钥',
                'created_at'=>date('Y-m-d H:i:s')
            ],
            [
                'type'=>'wechat-payment',
                'key'=>'v2_secret_key',
                'value'=>'',
                'label'=>'v2 API 秘钥',
                'created_at'=>date('Y-m-d H:i:s')
            ],
            // 其他配置
            [
                'type'=>'other',
                'key'=>'force_bind_mobile',
                'value'=>'no',
                'label'=>'是否强制绑定手机号',
                'created_at'=>date('Y-m-d H:i:s')
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wechat_setting');
    }
};
