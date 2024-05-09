<?php

namespace ManoCode\MiniWechat\Http\Controllers;

use Slowlyo\OwlAdmin\Controllers\AdminController;

class MiniWechatController extends AdminController
{
    public function index()
    {
        $page = $this->basePage()->body('微信小程序模块');

        return $this->response()->success($page);
    }
}
