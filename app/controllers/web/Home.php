<?php namespace controllers\web;

use system\BaseController;

class Home extends BaseController
{
	public function __construct($params)
	{
		## IF WANT USE CONSTRUCT MUST SEND PARAMETER LIKE BELLOW
		parent::__construct($params);
		// echo "__construct";
	}

    public function index()
    {
    	$data = $this->config;
        echo $this->view->render('web/welcome.html', $data);
    }
}