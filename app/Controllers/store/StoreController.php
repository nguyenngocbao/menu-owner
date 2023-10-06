<?php

namespace App\Controllers\store;

class StoreController
{

    private function _() {
        $store_id = 1;
        $res = call_api("/store/getByAdmin",['id' => $store_id]);
        $store = [];
        $menu = [];
        $city =[];
        $district = [];
        $ward =[];
        if($res['err'] == 1){
            redirect('https://taplink.network');
        }
        $store = $res['data']['store'];
        $menu = $res['data']['menus'];
        $city = $res['data']['city'];
        $district = $res['data']['district'];
        $ward = $res['data']['ward'];
        render_page('menu/menu2', compact('store','menu','city', 'district','ward'));

    }
    public function indexAction() {
        $this->_();
    }


}