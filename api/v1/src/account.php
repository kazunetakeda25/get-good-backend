<?php
use Slim\Http\Request;
use Slim\Http\Response;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


$app->get('/account/profile', function (Request $request, Response $response, array $args) {

	$token = $request->getHeader('token')[0];

	$user_id = Session::getUserID($token);
	if($user_id == null)
	{
		return $response->withJSON(array(
			'result' => false
		));
	}
	else
	{
		$profile = User::getProfile($user_id);

		return $response->withJSON(array(
			'result' => true,
			'data' => array(
				'profile' => $profile
			)
		));
	}
});

$app->post('/account/profile', function (Request $request, Response $response, array $args) {

	$token = $request->getHeader('token')[0];


	$name = $request->getParsedBody()['name'];
	$avatar_url = $request->getParsedBody()['avatar_url'];
	$description = $request->getParsedBody()['description'];
	$blizzard_id = $request->getParsedBody()['blizzard_id'];
	$overwatch_rank = $request->getParsedBody()['overwatch_rank'];
	$server = $request->getParsedBody()['server'];
	$overwatch_heroes = $request->getParsedBody()['overwatch_heroes'];

	$user_id = Session::getUserID($token);

	if($user_id == null)
	{
		return $response->withJSON(array(
			'result' => false
		));
	}
	else
	{
	
		User::updateProfile($user_id, $name, $avatar_url, $description, $blizzard_id, $overwatch_rank, $server, $overwatch_heroes);

		return $response->withJSON(array(
			'result' => true
		));
	}

});

$app->post('/account/lol_profile', function (Request $request, Response $response, array $args) {

	$token = $request->getHeader('token')[0];


	$name = $request->getParsedBody()['name'];
	$avatar_url = $request->getParsedBody()['avatar_url'];
	$description = $request->getParsedBody()['description'];
	$id = $request->getParsedBody()['id'];
	$rank = $request->getParsedBody()['rank'];
	$server = $request->getParsedBody()['server'];
	$heroes = $request->getParsedBody()['heroes'];
	$category = $request->getParsedBody()['category'];

	$user_id = Session::getUserID($token);

	if($user_id == null)
	{
		return $response->withJSON(array(
			'result' => false
		));
	}
	else
	{
	
		User::updateLoLProfile($user_id, $name, $avatar_url, $description, $id, $rank, $server, $heroes, $category);

		return $response->withJSON(array(
			'result' => true
		));
	}

});


$app->post('/account/profile/ready', function (Request $request, Response $response, array $args) {

	$token = $request->getHeader('token')[0];

	$user_id = Session::getUserID($token);
	$game = $request->getParsedBody()['game'];

	if($user_id == null)
	{
		return $response->withJSON(array(
			'result' => false
		));
	}
	else
	{
	
		$timestamp = User::ready($user_id, $game);

		return $response->withJSON(array(
			'result' => true,
			'data' => array(
				'timestamp' => $timestamp
			)
		));
	}

});

$app->get('/account/check_email', function (Request $request, Response $response, array $args) {

	$email = $request->getQueryParams()['email'];
	$name = $request->getQueryParams()['name'];

	$emailUsers = User::getWithEmail($email);
	$nameUsers = User::getWithName($name);

	if($emailUsers != null)
	{
		return $response->withJSON(array(
			'result' => false,
			'data' => array(
				"code" => 1
			)
		));
	}
	else if($nameUsers != null)
	{
		return $response->withJSON(array(
			'result' => false,
			'data' => array(
				"code" => 2
			)
		));
	}
	else
	{
		return $response->withJSON(array(
			'result' => true
		));
	}
});



$app->get('/account/check_name', function (Request $request, Response $response, array $args) {
	$token = $request->getHeader('token')[0];
	$user_id = Session::getUserID($token);

	$name = $request->getQueryParams()['name'];

	$nameUsers = User::checkName($name, $user_id);

	if($nameUsers != null)
	{
		return $response->withJSON(array(
			'result' => false,
			'data' => array(
				"code" => 2
			)
		));
	}
	else
	{
		return $response->withJSON(array(
			'result' => true
		));
	}
});

$app->get('/account/check_game_id', function (Request $request, Response $response, array $args) {

	$token = $request->getHeader('token')[0];
	$user_id = Session::getUserID($token);

	$gameid = $request->getQueryParams()['gameid'];
	if(strlen($gameid) == 0)
	{		
		return $response->withJSON(array(
			'result' => true
		));
	}

	$nameUsers = User::checkGameId($gameid, $user_id);

	if($nameUsers != null)
	{
		return $response->withJSON(array(
			'result' => false,
			'data' => array(
				"code" => 2
			)
		));
	}
	else
	{
		return $response->withJSON(array(
			'result' => true
		));
	}
});
$app->get('/account/check_lol_id', function (Request $request, Response $response, array $args) {

	$token = $request->getHeader('token')[0];
	$user_id = Session::getUserID($token);

	$gameid = $request->getQueryParams()['gameid'];
	if(strlen($gameid) == 0)
	{		
		return $response->withJSON(array(
			'result' => true
		));
	}

	$nameUsers = User::checkLOLId($gameid, $user_id);

	if($nameUsers != null)
	{
		return $response->withJSON(array(
			'result' => false,
			'data' => array(
				"code" => 2
			)
		));
	}
	else
	{
		return $response->withJSON(array(
			'result' => true
		));
	}
});

$app->post('/account/verify', function (Request $request, Response $response, array $args) {

	$email = $request->getParsedBody()['email'];

	$verify = new Verify($email);

	$verify_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."?code=".$verify->code;

	$mail = new PHPMailer(true);           
	try {
		$mail->IsSMTP(); // telling the class to use SMTP
		// $mail->Host       = "mail.yourdomain.com"; // SMTP server
		$mail->SMTPDebug  = 4;                     // enables SMTP debug information (for testing)
		                                           // 1 = errors and messages
		                                           // 2 = messages only
		$mail->SMTPAuth   = true;                  // enable SMTP authentication
		$mail->SMTPSecure = "tls";                 
		$mail->Host       = "smtp.gmail.com";      // SMTP server
		$mail->Port       = 587;                   // SMTP port
		$mail->Username   = "dugx@getgoodapp.com";  // username
		$mail->Password   = "10oct1985";            // password
	    //Recipients
	    $mail->setFrom('support@getgoodapp.com', 'GetGood Support Team');
	    $mail->addAddress($email);               // Name is optional
	    $mail->addReplyTo('no-reply@getgoodapp.com');


	    //Content
	    $mail->isHTML(true);                                  // Set email format to HTML
	    $mail->Subject = 'Verify your email';
	    $mail->Body    = 'Please verify your email on below link </br></br>'.$verify_url;

	    $mail->send();
	} catch (Exception $e) {
	}

	return $response->withJSON(array(
		'result' => true
	));
});

$app->get('/account/verify', function (Request $request, Response $response, array $args) {

	$code = $request->getQueryParams()['code'];
    return $this->renderer->render($response, 'verify.php', $args);	
});

$app->post('/account/verify_email', function (Request $request, Response $response, array $args) {

	$code = $request->getParsedBody()['code'];


	$result = Verify::verify($code);

	if($result == 0)
	{
		echo "Verification code expired. Please try again.";
	}
	else if($result == 1)
	{
		echo "Verification success!";
	}
});


$app->post('/account/push_token', function (Request $request, Response $response, array $args) {

	$token = $request->getHeader('token')[0];
	$push_token = $request->getParsedBody()['push_token'];
	$platform = $request->getParsedBody()['platform'];

	$user_id = Session::getUserID($token);
	if($user_id == null)
	{
		return $response->withJSON(array(
			'result' => false
		));
	}
	else
	{
		Session::updatePushToken($token, $push_token, $platform);		
		
		return $response->withJSON(array(
			'result' => true
		));
	}

});



$app->post('/account/forgot_password', function (Request $request, Response $response, array $args) {

	$email = $request->getParsedBody()['email'];

	$user = User::getWithEmail($email);
	if($user == null)
	{
		return $response->withJSON(array(
			'result' => false
		));
	}
	$forgot = new Forgot($email);

	$reset_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."?code=".$forgot->code;	
	$mail = new PHPMailer(true);           
	try {
		$mail->IsSMTP(); // telling the class to use SMTP
		// $mail->Host       = "mail.yourdomain.com"; // SMTP server
		// $mail->SMTPDebug  = 4;                     // enables SMTP debug information (for testing)
		                                           // 1 = errors and messages
		                                           // 2 = messages only
		$mail->SMTPAuth   = true;                  // enable SMTP authentication
		$mail->SMTPSecure = "tls";                 
		$mail->Host       = "smtp.gmail.com";      // SMTP server
		$mail->Port       = 587;                   // SMTP port
		$mail->Username   = "dugx@getgoodapp.com";  // username
		$mail->Password   = "10oct1985";            // password
	    //Recipients
	    $mail->setFrom('support@getgoodapp.com', 'GetGood Support Team');
	    $mail->addAddress($email);               // Name is optional
	    $mail->addReplyTo('no-reply@getgoodapp.com');


	    //Content
	    $mail->isHTML(true);                                  // Set email format to HTML
	    $mail->Subject = 'Verify your email';
	    $mail->Body    = 'Please reset your password on below link </br></br>'.$reset_url;

	    $mail->send();
	} catch (Exception $e) {
	}

	return $response->withJSON(array(
		'result' => true
	));
});

$app->get('/account/forgot_password', function (Request $request, Response $response, array $args) {

	$code = $request->getQueryParams()['code'];
    return $this->renderer->render($response, 'forgot_password.php', $args);
});

$app->post('/account/reset_password', function (Request $request, Response $response, array $args) {

	$code = $request->getParsedBody()['code'];
	$password = $request->getParsedBody()['password'];


	$result = Forgot::reset($code, $password);

	if($result == 0)
	{
		echo "Reset code expired. Please try again.";
	}
	else if($result == 1)
	{
		echo "Reset success!";
	}
});


$app->get('/overwatch/profile/coach_rating', function (Request $request, Response $response, array $args) {

	$token = $request->getHeader('token')[0];
	$user_id = Session::getUserID($token);

	if(!$user_id)
	{		
		return $response->withJSON(array(
			'result' => false
		));
	}
	$id = $request->getQueryParams()['id'];
	$game = $request->getQueryParams()['game'];
	$ratings = Coach_Rating::getWithUserID($id, $game);
		
	return $response->withJSON(array(
		'result' => true,
		'data' => array(
			'ratings' => $ratings
		)
	));
	
});

$app->get('/overwatch/profile/trainee_rating', function (Request $request, Response $response, array $args) {

	$token = $request->getHeader('token')[0];
	$user_id = Session::getUserID($token);

	if(!$user_id)
	{		
		return $response->withJSON(array(
			'result' => false
		));
	}
	$id = $request->getQueryParams()['id'];
	$game = $request->getQueryParams()['game'];
	$ratings = Trainee_Rating::getWithUserID($id, $game);
		
	return $response->withJSON(array(
		'result' => true,
		'data' => array(
			'ratings' => $ratings
		)
	));
});


$app->get('/overwatch/profile/player_rating', function (Request $request, Response $response, array $args) {

	$token = $request->getHeader('token')[0];
	$user_id = Session::getUserID($token);

	if(!$user_id)
	{		
		return $response->withJSON(array(
			'result' => false
		));
	}
	$id = $request->getQueryParams()['id'];
	$game = $request->getQueryParams()['game'];
	$ratings = Player_Rating::getWithUserID($id, $game);
		
	return $response->withJSON(array(
		'result' => true,
		'data' => array(
			'ratings' => $ratings
		)
	));
});
