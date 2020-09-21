<?php

use Slim\Http\Request;
use Slim\Http\Response;


$app->post('/overwatch/lesson', function (Request $request, Response $response, array $args) {

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
	$videos = $request->getParsedBody()['videos'];
	$thumb_url = $request->getParsedBody()['thumb_url'];
	$price = $request->getParsedBody()['price'];
	$game = $request->getParsedBody()['game'];

	if($game == 0)
		$game = 0;

	$lesson = new Lesson($title, $description, $hero, $videos, $thumb_url, $price, $user_id, $game);
	$lesson -> save();
		
	return $response->withJSON(array(
		'result' => true
	));
});

$app->post('/overwatch/lesson_list', function (Request $request, Response $response, array $args) {

	$ranks = array(
		0 => '',
		1 => 'BRONZE I',
		2 => 'BRONZE II',
		3 => 'BRONZE III',
		4 => 'BRONZE IV',
		5 => 'BRONZE V',
		6  => 'SILVER I',
		7  => 'SILVER II',
		8  => 'SILVER III',
		9  => 'SILVER IV',
		10 => 'SILVER V',
		11 => 'GOLD I',
		12 => 'GOLD II',
		13 => 'GOLD III',
		14 => 'GOLD IV',
		15 => 'GOLD V',
		16 => 'PLATINUM I',
		17 => 'PLATINUM II',
		18 => 'PLATINUM III',
		19 => 'PLATINUM IV',
		20 => 'PLATINUM V',
		21=> 'DIAMOND I',
		22=> 'DIAMOND II',
		23=> 'DIAMOND III',
		24=> 'DIAMOND IV',
		25=> 'DIAMOND V'
	);

	$Page = $request->getParsedBody()['Page'];
	$Sort = $request->getParsedBody()['Sort'];


	$CoachRatingMax = $request->getParsedBody()['CoachRatingMax'];
	$CoachRatingMin = $request->getParsedBody()['CoachRatingMin'];

	$GameRatingMax = $request->getParsedBody()['GameRatingMax'];
	$GameRatingMin = $request->getParsedBody()['GameRatingMin'];
	$PriceMin = $request->getParsedBody()['PriceMin'];
	$PriceMax = $request->getParsedBody()['PriceMax'];
	$Server = $request->getParsedBody()['Server'];
	$Platform = $request->getParsedBody()['Platform'];
	$Online = $request->getParsedBody()['Online'];
	$Category = $request->getParsedBody()['Category'];
	$Keyword = $request->getParsedBody()['Keyword'];
	$Game = $request->getParsedBody()['Game'];
	

	$lessons = Lesson::getList($Page, $Sort, $CoachRatingMin, $CoachRatingMax, $GameRatingMin, $GameRatingMax, $PriceMax, $PriceMin, $Server, $Platform, $Online, $Category, $Keyword, $Game);

	$lesson_list = array();
	for($i = 0; $i < count($lessons); $i++)
	{
		$lesson_list[$i]['id'] = $lessons[$i]['id'];
		$lesson_list[$i]['title'] = $lessons[$i]['title'];
		$lesson_list[$i]['description'] = $lessons[$i]['description'];
		$lesson_list[$i]['hero'] = $lessons[$i]['hero'];
		$lesson_list[$i]['hero_count'] = $lessons[$i]['hero_count'];
		$lesson_list[$i]['inactive'] = $lessons[$i]['inactive'];
		$lesson_list[$i]['price'] = $lessons[$i]['price'];
		$lesson_list[$i]['owner_id'] = $lessons[$i]['owner_id'];
		$lesson_list[$i]['server'] = $lessons[$i]['user_server'];
		$lesson_list[$i]['thumb_url'] = $lessons[$i]['thumb_url'];
		$lesson_list[$i]['videos'] = $lessons[$i]['videos'];
		$lesson_list[$i]['ready'] = $lessons[$i]['ready'];

		$lesson_list[$i]['owner']['id'] = $lessons[$i]['user_id'];
		$lesson_list[$i]['owner']['email'] = $lessons[$i]['email'];
		$lesson_list[$i]['owner']['name'] = $lessons[$i]['name'];
		$lesson_list[$i]['owner']['verified'] = $lessons[$i]['verified'];
		$lesson_list[$i]['owner']['avatar_url'] = $lessons[$i]['avatar_url'];
		$lesson_list[$i]['owner']['description'] = $lessons[$i]['group_description'];
		$lesson_list[$i]['owner']['blizzard_id'] = $lessons[$i]['blizzard_id'];
		$lesson_list[$i]['owner']['join_date'] = $lessons[$i]['join_date'];
		$lesson_list[$i]['owner']['overwatch_rank'] = $lessons[$i]['overwatch_rank'];
		$lesson_list[$i]['owner']['server'] = $lessons[$i]['user_server'];
		$lesson_list[$i]['owner']['overwatch_heroes'] = $lessons[$i]['overwatch_heroes'];
		$lesson_list[$i]['owner']['overwatch_hero_count'] = $lessons[$i]['overwatch_hero_count'];
		$lesson_list[$i]['owner']['coach_review_count'] = $lessons[$i]['coach_review_count'];
		$lesson_list[$i]['owner']['coach_rating'] = $lessons[$i]['coach_rating'];
		$lesson_list[$i]['owner']['trainee_review_count'] = $lessons[$i]['trainee_review_count'];
		$lesson_list[$i]['owner']['trainee_rating'] = $lessons[$i]['trainee_rating'];
		$lesson_list[$i]['owner']['player_review_count'] = $lessons[$i]['player_review_count'];
		$lesson_list[$i]['owner']['player_rating'] = $lessons[$i]['player_rating'];
		$lesson_list[$i]['owner']['lol_rank'] = $ranks[(int)$lessons[$i]['lol_rank']];
		$lesson_list[$i]['owner']['lol_server'] = $lessons[$i]['lol_server'];
		$lesson_list[$i]['owner']['lol_coach_rating'] = $lessons[$i]['lol_coach_rating'];
		$lesson_list[$i]['owner']['lol_coach_review_count'] = $lessons[$i]['lol_coach_review_count'];
	}
	
	
	return $response->withJSON(array(
		'result' => true,
		'data' => array(
			'lessons' => $lesson_list
		)
	));
});



$app->get('/overwatch/lessons', function (Request $request, Response $response, array $args) {

	$userid = ($request->getQueryParams()['userid']);
	$game = ($request->getQueryParams()['game']);


	$lessons = Lesson::getLessonsWithUserID($userid, $game);

	$lesson_list = array();
	for($i = 0; $i < count($lessons); $i++)
	{
		$lesson_list[$i]['id'] = $lessons[$i]['id'];
		$lesson_list[$i]['title'] = $lessons[$i]['title'];
		$lesson_list[$i]['description'] = $lessons[$i]['description'];
		$lesson_list[$i]['hero'] = $lessons[$i]['hero'];
		$lesson_list[$i]['hero_count'] = $lessons[$i]['hero_count'];
		$lesson_list[$i]['inactive'] = $lessons[$i]['inactive'];
		$lesson_list[$i]['price'] = $lessons[$i]['price'];
		$lesson_list[$i]['owner_id'] = $lessons[$i]['owner_id'];
		$lesson_list[$i]['server'] = $lessons[$i]['user_server'];
		$lesson_list[$i]['thumb_url'] = $lessons[$i]['thumb_url'];
		$lesson_list[$i]['videos'] = $lessons[$i]['videos'];

		$lesson_list[$i]['owner']['id'] = $lessons[$i]['user_id'];
		$lesson_list[$i]['owner']['email'] = $lessons[$i]['email'];
		$lesson_list[$i]['owner']['name'] = $lessons[$i]['name'];
		$lesson_list[$i]['owner']['verified'] = $lessons[$i]['verified'];
		$lesson_list[$i]['owner']['avatar_url'] = $lessons[$i]['avatar_url'];
		$lesson_list[$i]['owner']['description'] = $lessons[$i]['group_description'];
		$lesson_list[$i]['owner']['blizzard_id'] = $lessons[$i]['blizzard_id'];
		$lesson_list[$i]['owner']['join_date'] = $lessons[$i]['join_date'];
		$lesson_list[$i]['owner']['overwatch_rank'] = $lessons[$i]['overwatch_rank'];
		$lesson_list[$i]['owner']['server'] = $lessons[$i]['user_server'];
		$lesson_list[$i]['owner']['lol_server'] = $lessons[$i]['user_lol_server'];
		$lesson_list[$i]['owner']['overwatch_heroes'] = $lessons[$i]['overwatch_heroes'];
		$lesson_list[$i]['owner']['overwatch_hero_count'] = $lessons[$i]['overwatch_hero_count'];
		$lesson_list[$i]['owner']['coach_review_count'] = $lessons[$i]['coach_review_count'];
		$lesson_list[$i]['owner']['coach_rating'] = $lessons[$i]['coach_rating'];
		$lesson_list[$i]['owner']['trainee_review_count'] = $lessons[$i]['trainee_review_count'];
		$lesson_list[$i]['owner']['trainee_rating'] = $lessons[$i]['trainee_rating'];
		$lesson_list[$i]['owner']['player_review_count'] = $lessons[$i]['player_review_count'];
		$lesson_list[$i]['owner']['player_rating'] = $lessons[$i]['player_rating'];
		$lesson_list[$i]['owner']['lol_rank'] = $lessons[$i]['lol_rank'];
		$lesson_list[$i]['owner']['lol_server'] = $lessons[$i]['lol_server'];
	}
	
	
	return $response->withJSON(array(
		'result' => true,
		'data' => array(
			'lessons' => $lesson_list
		)
	));

});



$app->post('/overwatch/lesson/ready', function (Request $request, Response $response, array $args) {

	$token = $request->getHeader('token')[0];
	$user_id = Session::getUserID($token);

	if(!$user_id)
	{		
		return $response->withJSON(array(
			'result' => false
		));
	}
	
	$id = $request->getParsedBody()['id'];

	$timestamp = Lesson::ready($id);
		
	return $response->withJSON(array(
		'result' => true,
		'data' => array(
			'timestamp' => $timestamp
		)
	));
});


$app->put('/overwatch/lesson', function (Request $request, Response $response, array $args) {

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
	$price = $request->getParsedBody()['price'];
	$thumb_url = $request->getParsedBody()['thumb_url'];
	$ready = $request->getParsedBody()['ready'];
	$videos = $request->getParsedBody()['videos'];

	Lesson::update($id, $title, $description, $hero, $price, $server, $thumb_url, $videos, $ready);
		
	return $response->withJSON(array(
		'result' => true
	));
});

$app->get('/overwatch/my_lessons', function (Request $request, Response $response, array $args) {

	$token = $request->getHeader('token')[0];
	$user_id = Session::getUserID($token);

	if(!$user_id)
	{		
		return $response->withJSON(array(
			'result' => false
		));
	}

	$game = ($request->getQueryParams()['game']);
	$lessons = Lesson::getMyLessons($user_id, $game);
		
	return $response->withJSON(array(
		'result' => true,
		'data' => array(
			'lessons' => $lessons
		)
	));
});


$app->get('/overwatch/lesson', function (Request $request, Response $response, array $args) {

	$id = ($request->getQueryParams()['id']);

	$lesson = Lesson::getWithID($id);
	$lesson['owner'] = User::getProfile($lesson['owner_id']);
	return $response->withJSON(array(
		'result' => true,
		'data' => array(
			'lesson' => $lesson
		)
	));
});


$app->post('/overwatch/lesson/coach_rating', function (Request $request, Response $response, array $args) {

	$token = $request->getHeader('token')[0];
	$user_id = Session::getUserID($token);

	if(!$user_id)
	{		
		return $response->withJSON(array(
			'result' => false
		));
	}

	$id = $request->getParsedBody()['id'];
	$comment = $request->getParsedBody()['comment'];
	$competency = $request->getParsedBody()['competency'];
	$communication = $request->getParsedBody()['communication'];
	$flexibility = $request->getParsedBody()['flexibility'];
	$attitude = $request->getParsedBody()['attitude'];
	$game = $request->getParsedBody()['game'];

	if($game == 0)
		$game = 0;

	$trainee_rating = new Coach_Rating($user_id, $id, $competency, $communication, $flexibility, $attitude, $comment, $game);
	
	$trainee_rating -> save();

	User::updateRating($id);
	return $response->withJSON(array(
		'result' => true
	));
});



$app->post('/overwatch/lesson/trainee_rating', function (Request $request, Response $response, array $args) {

	$token = $request->getHeader('token')[0];
	$user_id = Session::getUserID($token);

	if(!$user_id)
	{		
		return $response->withJSON(array(
			'result' => false
		));
	}

	$id = $request->getParsedBody()['id'];
	$comment = $request->getParsedBody()['comment'];
	$general = $request->getParsedBody()['general'];
	$game = $request->getParsedBody()['game'];

	if($game == 0)
		$game = 0;

	$trainee_rating = new Trainee_Rating($user_id, $id, $general, $comment, $game);
	
	$trainee_rating -> save();

	User::updateRating($id);
	return $response->withJSON(array(
		'result' => true
	));
});



$app->delete('/overwatch/lesson', function (Request $request, Response $response, array $args) {

	$token = $request->getHeader('token')[0];
	$user_id = Session::getUserID($token);

	if(!$user_id)
	{		
		return $response->withJSON(array(
			'result' => false
		));
	}

	$user = User::getProfile($user_id);
	
	$lesson_id = $request->getParsedBody()['id'];

	Lesson::delete($lesson_id);
	return $response->withJSON(array(
		'result' => true
	));
});

