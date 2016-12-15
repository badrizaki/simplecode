<?php namespace controllers\web;

use system\BaseController;

class Home extends BaseController
{
    public function index()
    {
    	$data = $this->config;
        echo $this->view->render('web/welcome.html', $data);
    }
}