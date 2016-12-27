<?php
	$authenticateForLogin = function()
	{
	    return function ()
	    {
	    	global $app, $config;
			if(!isset($_SESSION['login']))
				$app->redirect($config['publicUrl'] . "/login");
	    };
	};

	$app->get('', 'web\Home:index');
	$app->get('json', 'web\Json:index');