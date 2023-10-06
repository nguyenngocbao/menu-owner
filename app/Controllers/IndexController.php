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

    public function indexAction() {
        $this->_();
    }

    public function loginAction() {
        $this->_();
    }

    public function forgotpassAction() {
        render_page('forgot_password', ['title' => 'Quên mật khẩu','mess' => ""]);
    }


    public function forgotpasswordAction() {
        if (!empty(post())) {
            $r = call_api('user/forgetpassword', [  'phone' => post('phone')]);
            if ($r) {
                if ($r['retCode'] == 1){
                    render_page('confirm_password', ['title' => 'Thay đổi mật khẩu','mess' => "", 'tkOTP' => $r['data']['tkOTP']]);
                }else{
                    render_page('forgot_password', ['title' => 'Quên mật khẩu','mess' => "Số điện thoại không tồn tại"]);
                }

            }
        }else{
            render_page('forgot_password', ['title' => 'Quên mật khẩu','mess' => "Không được để trống số điện thoại"]);
        }
    }

    public function confirmPasswordAction() {
        if (!empty(post())) {

            $r = call_api('user/confirmpassword', [  'tkOTP' => post('tkOTP'), 'otp' => post('otp'),'password' => sha1(post('pass'))]);
            if ($r) {
                if ($r['retCode'] == 1){
                    $_SESSION['account'] = $r['data'];
                    if ($login_uri = session('login_uri')) {
                        unset($_SESSION['login_uri']);
                        redirect($login_uri);
                    } else {
                        redirect(url());
                    }
                }else{
                    render_page('confirm_password', ['title' => 'confirm_password','mess' => "Thông tin không đúng", 'tkOTP' => post('tkOTP')]);
                }
            }
        }else{
            render_page('confirm_password', ['title' => 'Thay đổi mật khẩu','mess' => "Không được để trống các trường", 'tkOTP' => post('tkOTP')]);
        }
    }

    public function logoutAction() {
        call_api('user/logout', ['tkSession' => session('account.tkSession')]);
        session_unset();
        session_destroy();
        session_write_close();
        redirect(url());
    }

    public function authAction(){
        render_page_layout('wellcome', [],'_non_layout');
    }

}
