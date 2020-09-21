<?php

use Slim\Http\Request;
use Slim\Http\Response;

$app->post('/overwatch/dialog', function (Request $request, Response $response, array $args) {

	$token = $request->getHeader('token')[0];
	$user_id = Session::getUserID($token);

	if(!$user_id)
	{		
		return $response->withJSON(array(
			'result' => false
		));
	}

	$user = User::getProfile($user_id);

	$type = $request->getParsedBody()['type'];
	$reference_id = $request->getParsedBody()['reference_id'];
	$rec_id = $request->getParsedBody()['rec_id'];
	$game = $request->getParsedBody()['game'];

	$dialog = new Dialog($user['id'], $type, $reference_id, $rec_id, $game);
	$result = $dialog -> save();

	return $response->withJSON(array(
		'result' => true,
		'data' => array(
			'dialog' => $result
		)
	));
});


$app->put('/overwatch/dialog', function (Request $request, Response $response, array $args) {

	$token = $request->getHeader('token')[0];
	$user_id = Session::getUserID($token);

	if(!$user_id)
	{		
		return $response->withJSON(array(
			'result' => false
		));
	}

	$user = User::getProfile($user_id);

	$dialog_id = $request->getParsedBody()['dialog_id'];

	$inviter_id = $request->getParsedBody()['inviter_id'];
	$state = $request->getParsedBody()['state'];
	$reference_id = $request->getParsedBody()['reference_id'];
	$block_id = $request->getParsedBody()['block_id'];

	Dialog::updateDialog($dialog_id, $state, $reference_id, $inviter_id, $block_id);

	return $response->withJSON(array(
		'result' => true
	));
});

$app->post('/overwatch/message', function (Request $request, Response $response, array $args) {

	$token = $request->getHeader('token')[0];
	$user_id = Session::getUserID($token);

	if(!$user_id)
	{		
		return $response->withJSON(array(
			'result' => false
		));
	}

	$user = User::getProfile($user_id);
	$type = $request->getParsedBody()['type'];
	$message = $request->getParsedBody()['message'];
	$dialog_id = $request->getParsedBody()['dialog_id'];

	$message = Dialog::sendMessage($dialog_id, $user['id'], $message, $type);

	$message['avatar_url'] = $user['avatar_url'];
	$message['name'] = $user['name'];

	return $response->withJSON(array(
		'result' => true,
		'data' => array(
			'message' => $message
		)
	));
});

$app->get('/overwatch/dialog_list', function (Request $request, Response $response, array $args) {

	$token = $request->getHeader('token')[0];
	$user_id = Session::getUserID($token);

	if(!$user_id)
	{		
		return $response->withJSON(array(
			'result' => false
		));
	}

	$game = $request->getQueryParams()['game'];
	$user = User::getProfile($user_id);

	$dialogs = Dialog::getDialogWithUserID($user_id, $game);

	return $response->withJSON(array(
		'result' => true,
		'data' => array(
			'dialogs' => $dialogs
		)
	));
});

$app->get('/overwatch/dialog', function (Request $request, Response $response, array $args) {

	$token = $request->getHeader('token')[0];
	$user_id = Session::getUserID($token);

	if(!$user_id)
	{		
		return $response->withJSON(array(
			'result' => false
		));
	}

	$dialog_id = ($request->getQueryParams()['id']);

	$message_id = ($request->getQueryParams()['message_id']);
	
	$dialogs = Dialog::getDialogWithID($dialog_id);
	$messages = Dialog::getMessages($dialog_id, $message_id);
	return $response->withJSON(array(
		'result' => true,
		'data' => array(
			'dialog' => $dialogs,
			'messages' => $messages
		)
	));
});

$app->get('/overwatch/dialog/timestamp', function (Request $request, Response $response, array $args) {

	$token = $request->getHeader('token')[0];
	$user_id = Session::getUserID($token);

	if(!$user_id)
	{		
		return $response->withJSON(array(
			'result' => false
		));
	}
	
	$game = ($request->getQueryParams()['game']);

	$timestamp = Dialog::getLastTimestamp($user_id, $game);
	return $response->withJSON(array(
		'result' => true,
		'data' => array(
			'timestamp' => $timestamp
		)
	));
});