<?php

namespace app\adminapi\controller;

use think\Controller;

class BaseApi extends Controller
{
    protected $no_login = ['login/login', 'login/captcha'];

    protected function _initialize()
    {
        parent::_initialize();
        // 允许的源域名
        header("Access-Control-Allow-Origin: *");
        // 允许的请求头信息
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
        // 允许的请求类型
        header('Access-Control-Allow-Methods: GET, POST, PUT,DELETE,OPTIONS,PATCH');
        try {
            $path = strtolower($this->request->controller().'/'.$this->request->action());
            if (!in_array($path, $this->no_login)) {
                // 需要登录校验
                $user_id = \tools\jwt\Token::getUserId();
                if (empty($user_id)) {
                    $this->fail('token验证失败', 100);
                }
                $this->request->get('user_id', $user_id);
                $this->request->post('user_id', $user_id);
            }
        } catch (\Throwable $th) {
            $this->fail('token验证失败', 101);
        }
    }

    protected function response($code = 200, $msg = 'success', $data = '') {
        $res = [
            'code' => $code,
            'msg' => $msg,
            'data' => $data,
        ];
        json($res)->send(); die;
    }

    /**
     * 成功的响应
     */
    protected function ok($data = [], $code = 200, $msg = 'success') {
        $this->response($code, $msg, $data);
    }
    
    /**
     * 错误的响应
     */
    protected function fail($msg = '操作失败', $code = 400, $data = '' ) {
        $this->response($code, $msg, $data);
    }
}
