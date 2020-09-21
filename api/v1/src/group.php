<?php

use Slim\Http\Request;
use Slim\Http\Response;


$app->post('/overwatch/group', function (Request $request, Response $response, array $args) {

	$token = $request->getHeader('token')[0];
	$user_id = Session::getUserID($token);

	if(!$user_id)
	{		
		return $response->withJSON(array(
			'result' => false
		));
	}

	$user = User::getProfile($user_id);
	
	$title = $request->getParsedBody()['title'];
	$description = $request->getParsedBody()['description'];
	$hero = $request->getParsedBody()['hero'];
	$game = $request->getParsedBody()['game'];
	
	$group = new Group($user, $title, $description, $hero, $game);
	$group -> save();
		
	return $response->withJSON(array(
		'result' => true
	));
});


$app->post('/overwatch/group/ready', function (Request $request, Response $response, array $args) {

	$token = $request->getHeader('token')[0];
	$user_id = Session::getUserID($token);

	if(!$user_id)
	{		
		return $response->withJSON(array(
			'result' => false
		));
	}

	$user = User::getProfile($user_id);
	
	$group_id = $request->getParsedBody()['id'];

	$timestamp = Group::ready($group_id);		

	return $response->withJSON(array(
		'result' => true,
		'data' => array(
			'timestamp' => $timestamp
		)
	));
});


$app->put('/overwatch/group', function (Request $request, Response $response, array $args) {

	$token = $request->getHeader('token')[0];
	$user_id = Session::getUserID($token);

	if(!$user_id)
	{		
		return $response->withJSON(array(
			'result' => false
		));
	}
	
	$id = $request->getParsedBody()['id'];
	$title = $request->getParsedBody()['title'];
	$description = $request->getParsedBody()['description'];
	$hero = $request->getParsedBody()['hero'];

	Group::update($id, $title, $description, $hero);

	return $response->withJSON(array(
		'result' => true
	));
});


$app->post('/overwatch/group_list', function (Request $request, Response $response, array $args) {

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
	
	if($Game == 0)
		$Game = 0;

	$groups = Group::getList($Page, $Sort, $PlayerRatingMin, $PlayerRatingMax, $GameRatingMin, $GameRatingMax, $Server, $Platform, $Online, $Category, $Keyword, $Game);


	$group_list = array();
	for($i = 0; $i < count($groups); $i++)
	{
		$group_list[$i]['id'] = $groups[$i]['id'];
		$group_list[$i]['average_game_rating'] = $groups[$i]['average_game_rating'];
		$group_list[$i]['average_player_rating'] = $groups[$i]['average_player_rating'];
		$group_list[$i]['description'] = $groups[$i]['description'];
		$group_list[$i]['hero'] = $groups[$i]['hero'];
		$group_list[$i]['hero_count'] = $groups[$i]['hero_count'];
		$group_list[$i]['inactive'] = $groups[$i]['inactive'];
		$group_list[$i]['owner_id'] = $groups[$i]['owner_id'];
		$group_list[$i]['title'] = $groups[$i]['title'];
		$group_list[$i]['users'] = $groups[$i]['users'];
		$group_list[$i]['pending_users'] = $groups[$i]['pending_users'];
		$group_list[$i]['timestamp'] = $groups[$i]['timestamp'];
		$group_list[$i]['ready'] = $groups[$i]['ready'];

		$group_list[$i]['owner']['id'] = $groups[$i]['user_id'];
		$group_list[$i]['owner']['email'] = $groups[$i]['email'];
		$group_list[$i]['owner']['name'] = $groups[$i]['name'];
		$group_list[$i]['owner']['verified'] = $groups[$i]['verified'];
		$group_list[$i]['owner']['avatar_url'] = $groups[$i]['avatar_url'];
		$group_list[$i]['owner']['description'] = $groups[$i]['group_description'];
		$group_list[$i]['owner']['blizzard_id'] = $groups[$i]['blizzard_id'];
		$group_list[$i]['owner']['join_date'] = $groups[$i]['join_date'];
		$group_list[$i]['owner']['overwatch_rank'] = $groups[$i]['overwatch_rank'];
		$group_list[$i]['owner']['server'] = $groups[$i]['server'];
		$group_list[$i]['owner']['lol_server'] = $groups[$i]['lol_server'];
		$group_list[$i]['owner']['overwatch_heroes'] = $groups[$i]['overwatch_heroes'];
		$group_list[$i]['owner']['overwatch_hero_count'] = $groups[$i]['overwatch_hero_count'];
		$group_list[$i]['owner']['coach_review_count'] = $groups[$i]['coach_review_count'];
		$group_list[$i]['owner']['coach_rating'] = $groups[$i]['coach_rating'];
		$group_list[$i]['owner']['trainee_review_count'] = $groups[$i]['trainee_review_count'];
		$group_list[$i]['owner']['trainee_rating'] = $groups[$i]['trainee_rating'];
		$group_list[$i]['owner']['player_review_count'] = $groups[$i]['player_review_count'];
		$group_list[$i]['owner']['player_rating'] = $groups[$i]['player_rating'];
		$group_list[$i]['owner']['lol_player_rating'] = $groups[$i]['lol_player_rating'];
		$group_list[$i]['owner']['lol_heroes'] = $groups[$i]['lol_heroes'];
		$group_list[$i]['owner']['lol_id'] = $groups[$i]['lol_id'];
		$group_list[$i]['owner']['lol_rank'] = $groups[$i]['lol_rank'];
	}
	
	
	return $response->withJSON(array(
		'result' => true,
		'data' => array(
			'groups' => $group_list
		)
	));
});



$app->get('/overwatch/participating_groups', function (Request $request, Response $response, array $args) {

	$token = $request->getHeader('token')[0];
	$user_id = Session::getUserID($token);

	if(!$user_id)
	{		
		return $response->withJSON(array(
			'result' => false
		));
	}

	$game = $request->getQueryParams()['game'];
	$groups = Group::getParticipating($user_id, $game);

	$group_list = array();
	for($i = 0; $i < count($groups); $i++)
	{
		$group_list[$i]['id'] = $groups[$i]['id'];
		$group_list[$i]['average_game_rating'] = $groups[$i]['average_game_rating'];
		$group_list[$i]['average_player_rating'] = $groups[$i]['average_player_rating'];
		$group_list[$i]['description'] = $groups[$i]['description'];
		$group_list[$i]['hero'] = $groups[$i]['hero'];
		$group_list[$i]['hero_count'] = $groups[$i]['hero_count'];
		$group_list[$i]['inactive'] = $groups[$i]['inactive'];
		$group_list[$i]['owner_id'] = $groups[$i]['owner_id'];
		$group_list[$i]['title'] = $groups[$i]['title'];
		$group_list[$i]['users'] = $groups[$i]['users'];
		$group_list[$i]['pending_users'] = $groups[$i]['pending_users'];
		$group_list[$i]['timestamp'] = $groups[$i]['timestamp'];

		$group_list[$i]['owner']['id'] = $groups[$i]['user_id'];
		$group_list[$i]['owner']['email'] = $groups[$i]['email'];
		$group_list[$i]['owner']['name'] = $groups[$i]['name'];
		$group_list[$i]['owner']['verified'] = $groups[$i]['verified'];
		$group_list[$i]['owner']['avatar_url'] = $groups[$i]['avatar_url'];
		$group_list[$i]['owner']['description'] = $groups[$i]['group_description'];
		$group_list[$i]['owner']['blizzard_id'] = $groups[$i]['blizzard_id'];
		$group_list[$i]['owner']['join_date'] = $groups[$i]['join_date'];
		$group_list[$i]['owner']['overwatch_rank'] = $groups[$i]['overwatch_rank'];
		$group_list[$i]['owner']['server'] = $groups[$i]['server'];
		$group_list[$i]['owner']['lol_server'] = $groups[$i]['lol_server'];
		$group_list[$i]['owner']['overwatch_heroes'] = $groups[$i]['overwatch_heroes'];
		$group_list[$i]['owner']['overwatch_hero_count'] = $groups[$i]['overwatch_hero_count'];
		$group_list[$i]['owner']['coach_review_count'] = $groups[$i]['coach_review_count'];
		$group_list[$i]['owner']['coach_rating'] = $groups[$i]['coach_rating'];
		$group_list[$i]['owner']['trainee_review_count'] = $groups[$i]['trainee_review_count'];
		$group_list[$i]['owner']['trainee_rating'] = $groups[$i]['trainee_rating'];
		$group_list[$i]['owner']['player_review_count'] = $groups[$i]['player_review_count'];
		$group_list[$i]['owner']['player_rating'] = $groups[$i]['player_rating'];
		$group_list[$i]['owner']['lol_player_rating'] = $groups[$i]['lol_player_rating'];
		$group_list[$i]['owner']['lol_heroes'] = $groups[$i]['lol_heroes'];
		$group_list[$i]['owner']['lol_id'] = $groups[$i]['lol_id'];
		$group_list[$i]['owner']['lol_rank'] = $groups[$i]['lol_rank'];
	}
	
	
	return $response->withJSON(array(
		'result' => true,
		'data' => array(
			'groups' => $group_list
		)
	));

});

$app->get('/overwatch/groups', function (Request $request, Response $response, array $args) {


	$userid = ($request->getQueryParams()['userid']);
	$game = ($request->getQueryParams()['game']);


	$groups = Group::getGroupsWithUserID($userid, $game);

	$group_list = array();
	for($i = 0; $i < count($groups); $i++)
	{
		$group_list[$i]['id'] = $groups[$i]['id'];
		$group_list[$i]['average_game_rating'] = $groups[$i]['average_game_rating'];
		$group_list[$i]['average_player_rating'] = $groups[$i]['average_player_rating'];
		$group_list[$i]['description'] = $groups[$i]['description'];
		$group_list[$i]['hero'] = $groups[$i]['hero'];
		$group_list[$i]['hero_count'] = $groups[$i]['hero_count'];
		$group_list[$i]['inactive'] = $groups[$i]['inactive'];
		$group_list[$i]['owner_id'] = $groups[$i]['owner_id'];
		$group_list[$i]['title'] = $groups[$i]['title'];
		$group_list[$i]['users'] = $groups[$i]['users'];
		$group_list[$i]['pending_users'] = $groups[$i]['pending_users'];
		$group_list[$i]['timestamp'] = $groups[$i]['timestamp'];

		$group_list[$i]['owner']['id'] = $groups[$i]['user_id'];
		$group_list[$i]['owner']['email'] = $groups[$i]['email'];
		$group_list[$i]['owner']['name'] = $groups[$i]['name'];
		$group_list[$i]['owner']['verified'] = $groups[$i]['verified'];
		$group_list[$i]['owner']['avatar_url'] = $groups[$i]['avatar_url'];
		$group_list[$i]['owner']['description'] = $groups[$i]['group_description'];
		$group_list[$i]['owner']['blizzard_id'] = $groups[$i]['blizzard_id'];
		$group_list[$i]['owner']['join_date'] = $groups[$i]['join_date'];
		$group_list[$i]['owner']['overwatch_rank'] = $groups[$i]['overwatch_rank'];
		$group_list[$i]['owner']['server'] = $groups[$i]['server'];
		$group_list[$i]['owner']['lol_server'] = $groups[$i]['lol_server'];
		$group_list[$i]['owner']['overwatch_heroes'] = $groups[$i]['overwatch_heroes'];
		$group_list[$i]['owner']['overwatch_hero_count'] = $groups[$i]['overwatch_hero_count'];
		$group_list[$i]['owner']['coach_review_count'] = $groups[$i]['coach_review_count'];
		$group_list[$i]['owner']['coach_rating'] = $groups[$i]['coach_rating'];
		$group_list[$i]['owner']['trainee_review_count'] = $groups[$i]['trainee_review_count'];
		$group_list[$i]['owner']['trainee_rating'] = $groups[$i]['trainee_rating'];
		$group_list[$i]['owner']['player_review_count'] = $groups[$i]['player_review_count'];
		$group_list[$i]['owner']['player_rating'] = $groups[$i]['player_rating'];
		$group_list[$i]['owner']['lol_player_rating'] = $groups[$i]['lol_player_rating'];
		$group_list[$i]['owner']['lol_heroes'] = $groups[$i]['lol_heroes'];
		$group_list[$i]['owner']['lol_id'] = $groups[$i]['lol_id'];
		$group_list[$i]['owner']['lol_rank'] = $groups[$i]['lol_rank'];
	}
	
	
	return $response->withJSON(array(
		'result' => true,
		'data' => array(
			'groups' => $group_list
		)
	));

});



$app->get('/overwatch/my_groups', function (Request $request, Response $response, array $args) {

	$token = $request->getHeader('token')[0];
	$user_id = Session::getUserID($token);
	$game = $request->getQueryParams()['game'];

	// if($game == 0)
	// 	$game = 0;

	if(!$user_id)
	{		
		return $response->withJSON(array(
			'result' => false
		));
	}

	$groups = Group::getMyGroups($user_id, $game);
		
	return $response->withJSON(array(
		'result' => true,
		'data' => array(
			'groups' => $groups
		)
	));
});

$app->get('/overwatch/group', function (Request $request, Response $response, array $args) {


	$id = ($request->getQueryParams()['id']);
	$groups = Group::getWithID($id);

	$group_list = array();
	for($i = 0; $i < count($groups); $i++)
	{
		$group_list[$i]['id'] = $groups[$i]['id'];
		$group_list[$i]['average_game_rating'] = $groups[$i]['average_game_rating'];
		$group_list[$i]['average_player_rating'] = $groups[$i]['average_player_rating'];
		$group_list[$i]['description'] = $groups[$i]['description'];
		$group_list[$i]['hero'] = $groups[$i]['hero'];
		$group_list[$i]['hero_count'] = $groups[$i]['hero_count'];
		$group_list[$i]['inactive'] = $groups[$i]['inactive'];
		$group_list[$i]['owner_id'] = $groups[$i]['owner_id'];
		$group_list[$i]['title'] = $groups[$i]['title'];
		$group_list[$i]['users'] = $groups[$i]['users'];
		$group_list[$i]['pending_users'] = $groups[$i]['pending_users'];
		$group_list[$i]['timestamp'] = $groups[$i]['timestamp'];

		$group_list[$i]['owner']['id'] = $groups[$i]['user_id'];
		$group_list[$i]['owner']['email'] = $groups[$i]['email'];
		$group_list[$i]['owner']['name'] = $groups[$i]['name'];
		$group_list[$i]['owner']['verified'] = $groups[$i]['verified'];
		$group_list[$i]['owner']['avatar_url'] = $groups[$i]['avatar_url'];
		$group_list[$i]['owner']['description'] = $groups[$i]['group_description'];
		$group_list[$i]['owner']['blizzard_id'] = $groups[$i]['blizzard_id'];
		$group_list[$i]['owner']['join_date'] = $groups[$i]['join_date'];
		$group_list[$i]['owner']['overwatch_rank'] = $groups[$i]['overwatch_rank'];
		$group_list[$i]['owner']['server'] = $groups[$i]['server'];
		$group_list[$i]['owner']['overwatch_heroes'] = $groups[$i]['overwatch_heroes'];
		$group_list[$i]['owner']['overwatch_hero_count'] = $groups[$i]['overwatch_hero_count'];
		$group_list[$i]['owner']['coach_review_count'] = $groups[$i]['coach_review_count'];
		$group_list[$i]['owner']['coach_rating'] = $groups[$i]['coach_rating'];
		$group_list[$i]['owner']['trainee_review_count'] = $groups[$i]['trainee_review_count'];
		$group_list[$i]['owner']['trainee_rating'] = $groups[$i]['trainee_rating'];
		$group_list[$i]['owner']['player_review_count'] = $groups[$i]['player_review_count'];
		$group_list[$i]['owner']['player_rating'] = $groups[$i]['player_rating'];
		$group_list[$i]['owner']['lol_player_rating'] = $groups[$i]['lol_player_rating'];
		$group_list[$i]['owner']['lol_heroes'] = $groups[$i]['lol_heroes'];
		$group_list[$i]['owner']['lol_id'] = $groups[$i]['lol_id'];
		$group_list[$i]['owner']['lol_rank'] = $groups[$i]['lol_rank'];
	}

	return $response->withJSON(array(
		'result' => true,
		'data' => array(
			'group' => $group_list[0]
		)
	));
});


$app->post('/overwatch/group/join', function (Request $request, Response $response, array $args) {

	$token = $request->getHeader('token')[0];
	$user_id = Session::getUserID($token);

	if(!$user_id)
	{		
		return $response->withJSON(array(
			'result' => false
		));
	}

	$user = User::getProfile($user_id);
	
	$group_id = $request->getParsedBody()['id'];

	Group::join($group_id, $user['id']);	

	return $response->withJSON(array(
		'result' => true
	));
});


$app->post('/overwatch/group/join_user', function (Request $request, Response $response, array $args) {

	$token = $request->getHeader('token')[0];
	$user_id = Session::getUserID($token);

	if(!$user_id)
	{		
		return $response->withJSON(array(
			'result' => false
		));
	}

	$user = User::getProfile($user_id);
	
	$group_id = $request->getParsedBody()['id'];
	$userID = $request->getParsedBody()['user_id'];

	Group::join($group_id, $userID);	

	return $response->withJSON(array(
		'result' => true
	));
});


$app->post('/overwatch/group/apply', function (Request $request, Response $response, array $args) {

	$token = $request->getHeader('token')[0];
	$user_id = Session::getUserID($token);

	if(!$user_id)
	{		
		return $response->withJSON(array(
			'result' => false
		));
	}

	$user = User::getProfile($user_id);
	
	$group_id = $request->getParsedBody()['id'];

	Group::apply($group_id, $user['id']);	

	return $response->withJSON(array(
		'result' => true
	));
});

$app->post('/overwatch/group/leave', function (Request $request, Response $response, array $args) {

	$token = $request->getHeader('token')[0];
	$user_id = Session::getUserID($token);

	if(!$user_id)
	{		
		return $response->withJSON(array(
			'result' => false
		));
	}

	$user = User::getProfile($user_id);
	
	$group_id = $request->getParsedBody()['id'];

	Group::leave($group_id, $user['id']);	

	return $response->withJSON(array(
		'result' => true
	));
});

$app->get('/overwatch/users', function (Request $request, Response $response, array $args) {

	$token = $request->getHeader('token')[0];
	$user_id = Session::getUserID($token);

	if(!$user_id)
	{		
		return $response->withJSON(array(
			'result' => false
		));
	}

	$user = User::getProfile($user_id);
	

	$ids = ($request->getQueryParams()['ids']);

	if(strlen($ids) != 0)
	{
		$users = User::getUsers($ids);		
	}
	else
	{
		$users = [];
	}

	return $response->withJSON(array(
		'result' => true,
		'data' => array(
			'users' => $users
		)
	));
});


$app->get('/overwatch/group/messages', function (Request $request, Response $response, array $args) {

	$token = $request->getHeader('token')[0];
	$user_id = Session::getUserID($token);

	if(!$user_id)
	{		
		return $response->withJSON(array(
			'result' => false
		));
	}

	$user = User::getProfile($user_id);
	

	$id = ($request->getQueryParams()['id']);

	$messages = Group::getMessages($id);

	return $response->withJSON(array(
		'result' => true,
		'data' => array(
			'messages' => $messages
		)
	));
});


$app->delete('/overwatch/group', function (Request $request, Response $response, array $args) {

	$token = $request->getHeader('token')[0];
	$user_id = Session::getUserID($token);

	if(!$user_id)
	{		
		return $response->withJSON(array(
			'result' => false
		));
	}

	$user = User::getProfile($user_id);
	
	$group_id = $request->getParsedBody()['id'];

	Group::delete($group_id);
	return $response->withJSON(array(
		'result' => true
	));
});


$app->post('/overwatch/group/kick', function (Request $request, Response $response, array $args) {

	$token = $request->getHeader('token')[0];
	$user_id = Session::getUserID($token);

	if(!$user_id)
	{		
		return $response->withJSON(array(
			'result' => false
		));
	}

	$user = User::getProfile($user_id);
	
	$group_id = $request->getParsedBody()['id'];
	$user_id = $request->getParsedBody()['user_id'];

	Group::leave($group_id, $user_id);	

	return $response->withJSON(array(
		'result' => true
	));
});

$app->post('/overwatch/group/player_rating', function (Request $request, Response $response, array $args) {

	$token = $request->getHeader('token')[0];
	$user_id = Session::getUserID($token);

	if(!$user_id)
	{		
		return $response->withJSON(array(
			'result' => false
		));
	}

	$user = User::getProfile($user_id);
	
	$to_id = $request->getParsedBody()['to_id'];
	$leader = $request->getParsedBody()['leader'];
	$cooperative = $request->getParsedBody()['cooperative'];
	$good_communication = $request->getParsedBody()['good_communication'];
	$sportsmanship = $request->getParsedBody()['sportsmanship'];
	$mvp = $request->getParsedBody()['mvp'];
	$flex_player = $request->getParsedBody()['flex_player'];
	$good_hero_competency = $request->getParsedBody()['good_hero_competency'];
	$good_ultimate_usage = $request->getParsedBody()['good_ultimate_usage'];
	$abusive_chat = $request->getParsedBody()['abusive_chat'];
	$griefing = $request->getParsedBody()['griefing'];
	$spam = $request->getParsedBody()['spam'];
	$no_communication = $request->getParsedBody()['no_communication'];
	$un_cooperative = $request->getParsedBody()['un_cooperative'];
	$trickling_in = $request->getParsedBody()['trickling_in'];
	$poor_hero_competency = $request->getParsedBody()['poor_hero_competency'];
	$bad_ultimate_usage = $request->getParsedBody()['bad_ultimate_usage'];
	$overextending = $request->getParsedBody()['overextending'];
	$comment = $request->getParsedBody()['comment'];
	$game = $request->getParsedBody()['game'];

	if($game == 0)
	{
		$game = 0;
	}
	
	$playerRating = new Player_Rating($user_id
										,$to_id
										,$leader
										,$cooperative
										,$good_communication
										,$sportsmanship
										,$mvp
										,$flex_player
										,$good_hero_competency
										,$good_ultimate_usage
										,$abusive_chat
										,$griefing
										,$spam
										,$no_communication
										,$un_cooperative
										,$trickling_in
										,$poor_hero_competency
										,$bad_ultimate_usage
										,$overextending
										,$comment
										,$game);

	$playerRating -> save();
	
	return $response->withJSON(array(
		'result' => true
	));
});



$app->get('/overwatch/group/timestamp', function (Request $request, Response $response, array $args) {

	$token = $request->getHeader('token')[0];
	$user_id = Session::getUserID($token);

	if(!$user_id)
	{		
		return $response->withJSON(array(
			'result' => false
		));
	}
	$game = $request->getQueryParams()['game'];
	$timestamp = Group::getLastTimestamp($user_id, $game);
	return $response->withJSON(array(
		'result' => true,
		'data' => array(
			'timestamp' => $timestamp
		)
	));
});


$app->get('/overwatch/test', function (Request $request, Response $response, array $args) {
	$curl = curl_init();

	curl_setopt_array($curl, array(
	  CURLOPT_URL => "https://fcm.googleapis.com/fcm/send",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 30,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => "POST",
	  CURLOPT_POSTFIELDS => "{\n\t\"notification\": {\n\t\t\"title\": \"Hello World\",\n\t\t\"body\": \"Enjoy your coffee\"\n\t},\n\t\"to\": \"f3MhYv9MzhA:APA91bHwozwZ8E8oqlG5Qzzt-qdl7kL93gVBwqjOiRQhaLlnEde2tIpuzT66sFaEx0AZtB0AAhpkVRClyKLz1prP9tLVrvg8un8vLMoJ6hOWaK0ecgadM1T_YRbQ7o1GQKwrmWrb0MFf\"\n\t\n}",
	  CURLOPT_HTTPHEADER => array(
	    "Authorization: key=AAAAC8ylztk:APA91bHaRooXprKqPWHrcZYPRrGyZArV1LzYx2TzZ8If38ch9wuindDjDr2N-tbIpgXIjje6u1661xa4Xo33npkOhD6rsIvVSr6OeS5cc0jd0UliyPTkiTCLOG237Sse5_KreGs3vz1W",
	    "Cache-Control: no-cache",
	    "Content-Type: application/json",
	    "Postman-Token: 7a88617c-0952-4eba-a251-4a1a0d15e40e"
	  ),
	));

	$response = curl_exec($curl);
	$err = curl_error($curl);

	curl_close($curl);

	if ($err) {
	  echo "cURL Error #:" . $err;
	} else {
	  echo $response;
	}
});

