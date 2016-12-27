<?php namespace controllers\web;

use system\BaseController;

class Home extends BaseController
{
	public function __construct($params)
	{
		## IF WANT USE CONSTRUCT MUST SEND PARAMETER LIKE BELLOW
		parent::__construct($params);
		$this->UserModel = $this->load->model("UserModel");
	}

    public function index()
    {
    	$data = $this->config;
    	$result = $this->UserModel->userList();
        echo $this->view->render('web/welcome.html', $data);
    }
}