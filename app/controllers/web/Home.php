<?php namespace controllers\web;

use system\BaseController;

class Home extends BaseController
{
	public function __construct($params)
	{
		## IF WANT USE CONSTRUCT MUST SEND PARAMETER LIKE BELLOW
		parent::__construct($params);
		$this->UserModel = $this->load->model("UserModel");
		
		$this->visitor = $this->lib->visitor->setVisitorInfo($this->config["ipVisitor"]);
		$this->logDirError  = $this->config["logAdmin"].'/error/';
		$this->logDirAccess = $this->config["logAdmin"].'/access/';
	}

    public function index()
    {
    	$data = $this->config;
    	$result = $this->UserModel->userList();
        $this->view('web/welcome.html', $data);
    }
}