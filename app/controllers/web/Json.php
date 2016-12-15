<?php namespace controllers\web;

use system\BaseController;

class Json extends BaseController
{
    public function index()
    {
    	$data = $this->config;
    	echo json_encode($data);
    }
}