<?php namespace controllers\errors;

use system\BaseController;

class NotFound extends BaseController
{
    protected function Index()
    {
    	$this->httpLib->_http_response_code(404);
        $data = $this->config;
        echo $this->view->render("errors/404.ams",$data);
    }
}