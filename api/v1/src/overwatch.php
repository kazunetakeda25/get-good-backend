<?php
use Slim\Http\Request;
use Slim\Http\Response;

$app->post('/overwatch/player_list', function (Request $request, Response $response, array $args) {


	$Page = $request->getParsedBody()['Page'];
	$Sort = $request->getParsedBody()['Sort'];
	$PlayerRatingMax = $request->getParsedBody()['PlayerRatingMax'];
	$PlayerRatingMin = $request->getParsedBody()['PlayerRatingMin'];
	$GameRatingMax = $request->getParsedBody()['GameRatingMax'];
	$GameRatingMin = $request->getParsedBody()['GameRatingMin'];
	$Server = $request->getParsedBody()['Server'];
	$Platform = $request->getParsedBody()['Platform'];
	$Online = $request->getParsedBody()['Online'];
	$Category = $request->getParsedBody()['Category'];
	$Keyword = $request->getParsedBody()['Keyword'];
	$Game = $request->getParsedBody()['Game'];

	$users = User::getPlayerList($Page, $Sort, $PlayerRatingMin, $PlayerRatingMax, $GameRatingMin, $GameRatingMax, $Server, $Platform, $Online, $Category, $Keyword, $Game);
	
	return $response->withJSON(array(
		'result' => true,
		'data' => array(
			'users' => $users
		)
	));
});


$app->post('/overwatch/send_notification', function (Request $request, Response $response, array $args) {

	$token = $request->getHeader('token')[0];
	$user_id = Session::getUserID($token);

	if(!$user_id)
	{		
		return $response->withJSON(array(
			'result' => false
		));
	}

	$user = User::getProfile($user_id);

	$message = $request->getParsedBody()['message'];
	$userID = $request->getParsedBody()['user_id'];


	Session::sendNotification($message, $userID);

	if(strpos($message, "sent you a message") !== false)
	{	
		return;
	}

	$activity = new Activity($userID, $message);
	$activity -> save();
	return $response->withJSON(array(
		'result' => true
	));
});

$app->post('/overwatch/send_silent_notification', function (Request $request, Response $response, array $args) {

	$token = $request->getHeader('token')[0];
	$user_id = Session::getUserID($token);

	if(!$user_id)
	{		
		return $response->withJSON(array(
			'result' => false
		));
	}

	$user = User::getProfile($user_id);
	$userID = $request->getParsedBody()['user_id'];
	$dialogID = $request->getParsedBody()['dialog_id'];


	Session::sendSilentNotification($user, $userID, $dialogID);
});

$app->get('/overwatch/user_profile', function (Request $request, Response $response, array $args) {

	$userid = ($request->getQueryParams()['userid']);
	$user = User::getProfile($userid);

	return $response->withJSON(array(
		'result' => true,
		'data' => array(
			'profile' => $user
		)
	));
});

$app->post('/overwatch/online', function (Request $request, Response $response, array $args) {

	$token = $request->getHeader('token')[0];
	$user_id = Session::getUserID($token);

	if(!$user_id)
	{		
		return $response->withJSON(array(
			'result' => false
		));
	}

	User::online($user_id);
});

$app->get('/overwatch/activities', function (Request $request, Response $response, array $args) {

	$token = $request->getHeader('token')[0];
	$user_id = Session::getUserID($token);

	if(!$user_id)
	{		
		return $response->withJSON(array(
			'result' => false
		));
	}

	$activities = Activity::getList($user_id);

	return $response->withJSON(array(
		'result' => true,
		'data' => array(
			'activities' => $activities
		)
	));
});


