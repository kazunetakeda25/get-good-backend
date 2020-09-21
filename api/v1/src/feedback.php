<?php

use Slim\Http\Request;
use Slim\Http\Response;

$app->post('/feedback', function (Request $request, Response $response, array $args) {

	$token = $request->getHeader('token')[0];
	$user_id = Session::getUserID($token);

	if(!$user_id)
	{		
		return $response->withJSON(array(
			'result' => false
		));
	}
	
	$content = $request->getParsedBody()['content'];

	$group = new Feedback($content, $user_id);
	$group -> save();
		
	return $response->withJSON(array(
		'result' => true
	));
});
