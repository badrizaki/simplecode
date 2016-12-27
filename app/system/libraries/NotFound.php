<?php namespace system\libraries;

use system\BaseController;

class NotFound extends BaseController
{
    protected function Index()
    {
    	$this->httpLib->_http_response_code(404);
        echo '<!DOCTYPE HTML>
        <html>
        <head>
        	<title>404 NOT FOUND</title>
        	<style>
	        	#fof{ display:block; width:100%; padding:150px 0; text-align:center;}
				#fof .hgroup { display:block; width:80%; margin:0 auto; padding:0;}
				#fof .hgroup h1, #fof .hgroup h2 { margin:0 0 0 40px; padding:0; float:left; text-transform:uppercase;}
				#fof .hgroup h1{margin-top:-90px; font-size:200px;}
				#fof .hgroup h2{font-size:60px;}
				#fof .hgroup h2 span{display:block; font-size:30px;}
				#fof p{margin:25px 0 0 0; padding:0; font-size:16px;}
				#fof p:first-child{margin-top:0;}
        	</style>
        </head>
        <body>
        	<div class="wrapper row2">
			  	<div id="container" class="clear">
				    <section id="fof" class="clear">
				      	<div class="hgroup clear">
					        <h1>404</h1>
					        <h2>Error ! <span>Page Not Found</span></h2>
						</div>
						<p>For Some Reason The Page You Requested Could Not Be Found On Our Server</p>
						<p><a href="javascript:history.go(-1)">&laquo; Go Back</a> / <a href="#">Go Home &raquo;</a></p>
				    </section>
			  	</div>
			</div>
        </body>
        </html>
			';
    }
}