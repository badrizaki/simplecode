<?php namespace controllers\errors;

use system\BaseController;

class NotFound extends BaseController
{
    protected function Index()
    {
    	$this->http->_http_response_code(404);
        /*$data = $this->config;
        echo $this->view->render("errors/404.ams",$data);*/
        echo '<!DOCTYPE HTML>
        <html>
        <head>
        	<title>404 NOT FOUND</title>
        	<style>
	        	#fof{ display:block; width:100%; padding:10% 0; text-align:center;}
				#fof .hgroup { display:block; width:100%; margin:0 auto; padding:0;}
				#fof .hgroup h1, #fof .hgroup h2 { margin:0 0 0 0; padding:0; text-transform:uppercase;}
				#fof .hgroup h1{font-size:140px;}
				#fof .hgroup h2{font-size:60px;}
				#fof .hgroup h2 span{display:block; font-size:20px;}
				#fof p{margin:0 0 0 0; padding:0; font-size:16px;}
				#fof p:first-child{margin-top:0;}
        	</style>
        </head>
        <body>
        	<div class="wrapper row2">
			  	<div id="container" class="clear">
				    <section id="fof" class="clear">
				      	<div class="hgroup clear">
					        <div>
					        	<h1>404</h1>
					        </div>
					        <div>
					        <h2>Error ! <span>Page Not Found</span></h2>
					        </div>
						</div>
						<div>For Some Reason The Page You Requested Could Not Be Found On Our Server</div>
						<p><a href="javascript:history.go(-1)">&laquo; Go Back</a> / <a href="'.$this->config['publicUrl'].'">Go Home &raquo;</a></p>
				    </section>
			  	</div>
			</div>
        </body>
        </html>
			';
    }
}