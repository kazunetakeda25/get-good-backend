<?php
use Slim\Http\Request;
use Slim\Http\Response;

$app->post('/auth/register', function (Request $request, Response $response, array $args) {

	$email = $request->getParsedBody()['email'];
	$name = $request->getParsedBody()['name'];
	$avatar = $request->getParsedBody()['avatar'];
	$password = $request->getParsedBody()['password'];


	$user = new User($email, $name, $avatar, $password);

	if($user -> register())
	{
		return $response->withJSON(array(
			'result' => true
		));
	}
	else
	{
		return $response->withJSON(array(
			'result' => false
		));
	}
});

$app->post('/auth/login', function (Request $request, Response $response, array $args) {

	$email = $request->getParsedBody()['email'];
	$password = $request->getParsedBody()['password'];


	$user = User::login($email, $password);

	if($user != null)
	{		
		$platform = $request->getParsedBody()['platform'];
		$push_token = $request->getParsedBody()['push_token'];

		$session = new Session($user['id'], $push_token, $platform);
		$session -> save();

		
		return $response->withJSON(array(
			'result' => true,
			'data' => array(
				'token' => $session -> token,
				'user' => $user
			)
		));
	}
	else
	{
		return $response->withJSON(array(
			'result' => false
		));
	}
});

$app->get('/auth/logout', function (Request $request, Response $response, array $args) {

	$token = $request->getHeader('token')[0];
	$user_id = Session::getUserID($token);

	if($user_id != null)
	{
		Session::logout($user_id);
	}
});
