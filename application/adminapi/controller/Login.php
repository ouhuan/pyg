<?php

namespace app\adminapi\controller;

class Login extends BaseApi
{
    public function captcha()
    {
        $uniqid = uniqid(mt_rand(10000, 999999));
        $src = captcha_src($uniqid);
        $res = [
            'src' => $src,
            'uniqid' => $uniqid
        ];
        $this->ok($res);
    }

    public function login()
    {
        $params = input();

        $validate = $this->validate($params, [
            'username|用户名' => 'require',
            'password|密码' => 'require',
            'code|验证码' => 'require',
            'uniqid|验证码标识' => 'require'
        ]);
        if ($validate !== true) {
            $this->fail($validate, 401);
        }

        // 校验验证码
        session_id(cache('session_id_'.$params['uniqid']));

        if(!captcha_check($params['code'], $params['uniqid'])) {
            $this->fail('验证码错误', 402);
        }

        // 查询用户信息
        $password = encrypt_password($params['password']);
        $info = \app\common\model\admin::where('username', $params['username'])
            ->where('password', $password)
            ->find();
        if(empty($info)) {
            $this->fail('用户名或者密码错误', 403);
        }

        // 生成token
        $token = \tools\jwt\Token::getToken($info['id']);
        $data = [
            'token' => $token,
            'user_id' => $info['id'],
            'username' => $info['username'],
            'nickname' => $info['nickname'],
            'email' => $info['email']
        ];
        $this->ok($data);
    }

    public function logout()
    {
        # code...
        $delete_token = cache('delete_token') ?: [];
        $token = \tools\jwt\Token::getRequestToken();
        $delete_token[] = $token;
        cache('delete_token', $delete_token);
        $this->ok();
    }
}
