<?php namespace controllers\web;

use system\BaseController;

class Json extends BaseController
{
    public function index()
    {
    	$data = $this->config;
    	$this->response->json($data);
    }
}