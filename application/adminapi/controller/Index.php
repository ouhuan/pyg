<?php
namespace app\adminapi\controller;

class Index extends BaseApi
{
    public function index()
    {
        $token = \tools\jwt\Token::getToken(100);
        dump($token);
        $user_id = \tools\jwt\Token::getUserId($token);
        dump($user_id);
        // $this->response(200, '呵呵', '');
        // $goods = \think\Db::table('pyg_goods')->find();
        // dump($goods);die;
        // return 'adminapi/index';
    }
}
