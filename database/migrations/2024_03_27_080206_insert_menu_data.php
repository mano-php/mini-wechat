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
        $parent_id = \Slowlyo\OwlAdmin\Models\AdminMenu::query()->insertGetId([
            'parent_id'=>0,
            'order'=>0,
            'title'=>'用户模块',
            'icon'=>'ph:user-gear',
            'url'=>'/users',
            'url_type'=>1,
            'visible'=>1,
            'is_home'=>0,
            'keep_alive'=>0,
            'component'=>'amis',
            'is_full'=>0,
            'created_at'=>date('Y-m-d H:i:s')
        ]);
        \Slowlyo\OwlAdmin\Models\AdminMenu::query()->insert([
            [
                'parent_id'=>$parent_id,
                'order'=>8,
                'title'=>'用户管理',
                'icon'=>'ph:user-gear',
                'url'=>'/member',
                'url_type'=>1,
                'visible'=>1,
                'is_home'=>0,
                'is_full'=>0,
                'created_at'=>date('Y-m-d H:i:s')
            ],
            [
                'parent_id'=>$parent_id,
                'order'=>8,
                'title'=>'微信授权',
                'icon'=>'ph:user-gear',
                'url'=>'/wechat_bind',
                'url_type'=>1,
                'visible'=>1,
                'is_home'=>0,
                'is_full'=>0,
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

    }
};
