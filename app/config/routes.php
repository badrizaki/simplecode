<?php
	$app->get('', 'web\Home:index');
	$app->get('json', 'web\Json:index');