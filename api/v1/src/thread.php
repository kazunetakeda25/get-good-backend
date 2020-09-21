<?php

use Slim\Http\Request;
use Slim\Http\Response;

$app->get('/overwatch/thread/admin_post', function (Request $request, Response $response, array $args) {

	$token = $request->getHeader('token')[0];
	$user_id = Session::getUserID($token);
	$game = ($request->getQueryParams()['game']);

	if(!$user_id)
	{		
		return $response->withJSON(array(
			'result' => false
		));
	}

	$posts = Admin_Post::getList($game);

	return $response->withJSON(array(
		'result' => true,
		'data' => array(
			'posts' => $posts
		)
	));
});


$app->get('/overwatch/thread/admin_post/byid', function (Request $request, Response $response, array $args) {

	$token = $request->getHeader('token')[0];
	$user_id = Session::getUserID($token);
	$id = ($request->getQueryParams()['id']);

	if(!$user_id)
	{		
		return $response->withJSON(array(
			'result' => false
		));
	}

	$posts = Admin_Post::getWithID($id);

	return $response->withJSON(array(
		'result' => true,
		'data' => array(
			'post' => $posts
		)
	));
});

$app->get('/overwatch/thread/byid', function (Request $request, Response $response, array $args) {

	$token = $request->getHeader('token')[0];
	$user_id = Session::getUserID($token);
	$id = ($request->getQueryParams()['id']);

	if(!$user_id)
	{		
		return $response->withJSON(array(
			'result' => false
		));
	}

	$posts = Thread::getWithID($id);

	return $response->withJSON(array(
		'result' => true,
		'data' => array(
			'post' => $posts
		)
	));
});


$app->post('/overwatch/thread', function (Request $request, Response $response, array $args) {

	$token = $request->getHeader('token')[0];
	$user_id = Session::getUserID($token);

	if(!$user_id)
	{		
		return $response->withJSON(array(
			'result' => false
		));
	}

	
	$title = $request->getParsedBody()['title'];
	$description = $request->getParsedBody()['description'];
	$game = $request->getParsedBody()['game'];

	$thread = new Thread($title, $description, $game, $user_id);
	$thread -> save();

	return $response->withJSON(array(
		'result' => true
	));
});

$app->post('/overwatch/thread/comment', function (Request $request, Response $response, array $args) {

	$token = $request->getHeader('token')[0];
	$user_id = Session::getUserID($token);

	if(!$user_id)
	{		
		return $response->withJSON(array(
			'result' => false
		));
	}

	
	$comment = $request->getParsedBody()['comment'];
	$thread = $request->getParsedBody()['thread'];
	$reference = $request->getParsedBody()['reference'];

	$thread = new Comment($comment, $thread, $user_id, $reference);
	$thread -> save();

	return $response->withJSON(array(
		'result' => true
	));
});

$app->post('/overwatch/thread/comment/like', function (Request $request, Response $response, array $args) {

	$token = $request->getHeader('token')[0];
	$user_id = Session::getUserID($token);

	if(!$user_id)
	{		
		return $response->withJSON(array(
			'result' => false
		));
	}

	
	$id = $request->getParsedBody()['id'];
	$like = $request->getParsedBody()['like'];

	$like = new Like($id, $user_id, $like);
	$like -> save();

	$likes = Like::getLikes($id);

	return $response->withJSON(array(
		'result' => true,
		'data' => array(
			'likes' => $likes
		)
	));
});

$app->get('/overwatch/thread/comment/like', function (Request $request, Response $response, array $args) {

	$token = $request->getHeader('token')[0];
	$user_id = Session::getUserID($token);

	if(!$user_id)
	{		
		return $response->withJSON(array(
			'result' => false
		));
	}

	$id = $request->getQueryParams()['id'];
	$likes = Like::getLikes($id);

	return $response->withJSON(array(
		'result' => true,
		'data' => array(
			'likes' => $likes
		)
	));
});

$app->get('/overwatch/thread_list', function (Request $request, Response $response, array $args) {

	$token = $request->getHeader('token')[0];
	$user_id = Session::getUserID($token);

	if(!$user_id)
	{		
		return $response->withJSON(array(
			'result' => false
		));
	}

	$page = $request->getQueryParams()['page'];
	$keyword = $request->getQueryParams()['keyword'];
	$game = $request->getQueryParams()['game'];

	$threads = Thread::getList($page, $keyword, $game);

	return $response->withJSON(array(
		'result' => true,
		'data' => array(
			'threads' => $threads
		)
	));
});

$app->get('/overwatch/thread/comment_list', function (Request $request, Response $response, array $args) {

	$token = $request->getHeader('token')[0];
	$user_id = Session::getUserID($token);

	if(!$user_id)
	{		
		return $response->withJSON(array(
			'result' => false
		));
	}

	$thread = $request->getQueryParams()['thread'];
	$reference = $request->getQueryParams()['reference'];

	$comments = Comment::getList($thread, $reference);

	return $response->withJSON(array(
		'result' => true,
		'data' => array(
			'comments' => $comments
		)
	));
});

