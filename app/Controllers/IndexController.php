<?php

namespace App\Controllers;

class IndexController {

    private function _() {
        if ($account = session('account')) {
            $title = 'Xin chào ' . $account['fullname'];
            redirect(url('/store'));

        }
        $text = "Đăng nhập";

        $mess = '';
        $option = 'owner';
        $phone = ''; $username = '';
        if (!empty(post())) {
            $phone = post('phone');

            $r = call_api('/user/login', ['phone' => post('phone'), 'password' => post('password')]);
            if ($r && $r['err'] != 1) {
                $_SESSION['account'] = $r['data'];
                if ($login_uri = session('login_uri')) {
                    unset($_SESSION['login_uri']);
                    redirect($login_uri);
                } else {
                    redirect(url());
                }
            } else {
                $mess = 'Đăng nhập không thành công!';
            }
        }
        render_page('login', ['title' => $text, 'mess' => $mess,'option' => $option,'phone' => $phone ,
            'username' => $username]);
    }

    public function indexAction($uuid) {

        if ($account = session('account')) {
            session_unset();
            session_destroy();
            session_write_close();
            redirect(url('/'.$uuid));
        }else{
            $res = call_api("/verify",['uuid' => $uuid]);
            if ($res['code'] == 0){
                redirect(config('url.base'));
            }else{
                render_page_layout('otp',compact('uuid'),'_non_layout');
            }
        }
    }

    public function loginAction(){

        $uuid = post('uuid');
        $otp1 = post('otp1');
        $otp2 = post('otp2');
        $otp3 = post('otp3');
        $otp4 = post('otp4');

        $pass = $otp1.$otp2.$otp3.$otp4;


        $res = call_api("/verify",['uuid' => $uuid,'pass' => $pass ]);

        if ($res['code'] == 1){
            $store_id = $res['store_id'];
            $_SESSION['account'] = ['uuid' => $uuid, 'store_id' => $store_id];
            $this->storePage($store_id);

        }else{
            render_page_layout('otp',compact('uuid'),'_non_layout');
        }


    }

    public function storePage($store_id){
        $res = call_api("/store/getByAdmin",['id' => $store_id]);
        $store = [];
        $menu = [];
        $city =[];
        $district = [];
        $ward =[];
        if($res['err'] == 1){
            redirect(config('url.base'));
        }
        $store = $res['data']['store'];
        $menu = $res['data']['menus'];
        $city = $res['data']['city'];
        $district = $res['data']['district'];
        $ward = $res['data']['ward'];
        render_page('store/store2', compact('store','menu','city', 'district','ward'));

    }


    public function logoutAction() {
        //call_api('user/logout', ['tkSession' => session('account.tkSession')]);
        session_unset();
        session_destroy();
        session_write_close();
        redirect(url());
    }

    public function authAction(){
        render_page_layout('wellcome', [],'_non_layout');
    }

}
