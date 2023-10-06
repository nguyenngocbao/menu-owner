<?php

namespace App\Controllers;

abstract class CrudController
{
    public abstract function url();
    public function updateAction(){
        $data = post();
        $r = call_api($this->url().'update',$data);
        echo_json($r);
    }

    public function deleteAction(){
        $data = post();
        $r = call_api($this->url().'delete',$data);
        echo_json($r);
    }

    public function getAction(){
        $data = post();
        echo_json(call_api($this->url().'get',$data));
    }


}