<?php


class Session
{
	public $user_id;
	public $token;
	private $push_token;
	public $platform;

	public function __construct($_user_id, $_push_token, $_platform)
	{
		$this -> user_id = $_user_id;
		$this -> push_token = $_push_token;
		$this -> platform = $_platform;


		$this -> token = generateRandomString();

	}

	public function save()
	{
		$db = getDB();

		$sql = $db -> prepare("DELETE FROM session WHERE user_id=:id");
		$sql -> bindParam(':id', $this->user_id);
		$sql -> execute();

		$sql = $db -> prepare("INSERT INTO session (user_id, token, push_token, platform) VALUES (:id, :token, :push_token, :platform)");

		$sql -> bindParam(':id', $this->user_id);
		$sql -> bindParam(':token', $this->token);
		$sql -> bindParam(':push_token', $this->push_token);
		$sql -> bindParam(':platform', $this->platform);

		$sql -> execute();
	}

	public static function getUserID($token)
	{
		$db = getDB();

		$sql = $db -> prepare("SELECT * FROM session WHERE token='$token'");
		$sql -> execute();

		$sessions = $sql -> fetchAll(PDO::FETCH_ASSOC);
		if(count($sessions) > 0)
		{
			return $sessions[0]['user_id'];
		}
		else
		{
			return false;
		}
	}

	public static function updatePushToken($token, $push_token, $platform)
	{
		$db = getDB();

		$sql = $db -> prepare("UPDATE session SET push_token='$push_token', platform='$platform' WHERE token='$token'");
		$sql -> execute();
	}

	public static function logout($user_id)
	{
		$db = getDB();

		$sql = $db -> prepare("DELETE FROM session WHERE user_id='$user_id'");
		$sql -> execute();
	}

	public static function sendNotification($message, $userId)
	{
		$db = getDB();

		$sql = $db -> prepare("SELECT * FROM session WHERE user_id='$userId'");
		$sql -> execute();

		$sessions = $sql -> fetchAll(PDO::FETCH_ASSOC);

		if(count($sessions) == 0)
			return false;

		if($sessions[0]['platform'] == 1)
		{
			$push_token = $sessions[0]['push_token'];

		    $registrationIds = $push_token;
		#prep the bundle
		     $msg = array
		          	(
						'body' 	=> $message,
						'title'	=> 'Get Good',
		             	'icon'	=> 'myicon',/*Default Icon*/
		              	'sound' => 'mySound'/*Default sound*/
		          	);
			$fields = array
					(
						'to'		=> $registrationIds,
						'notification'	=> $msg
					);


			$headers = array
					(
						'Authorization: key=' . "AIzaSyCAzL-ZfT5BqBgIeT4LKi-T4XzKkddgnlM",
						'Content-Type: application/json'
					);
		#Send Reponse To FireBase Server
				$ch = curl_init();
				curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
				curl_setopt( $ch,CURLOPT_POST, true );
				curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
				curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
				curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
				curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
				$result = curl_exec($ch );
				curl_close( $ch );
		#Echo Result Of FireBase Server
		// echo $result;
		}
		else if($sessions[0]['platform'] == 2)
		{
			$deviceToken = $sessions[0]['push_token'];
			echo $deviceToken;
			$ctx = stream_context_create();
			// ck.pem is your certificate file

			if(environment == 'dev')
			{
				stream_context_set_option($ctx, 'ssl', 'local_cert', __DIR__.'/pushcert.pem');
			}
			else
			{
				stream_context_set_option($ctx, 'ssl', 'local_cert', __DIR__.'/pushcert_prod.pem');
			}

			stream_context_set_option($ctx, 'ssl', 'passphrase', "qwert");
			// Open a connection to the APNS server
			$fp = null;

			if(environment == 'dev')
			{
				$fp = stream_socket_client(
				'ssl://gateway.sandbox.push.apple.com:2195', $err,
				$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
			}
			else
			{

				$fp = stream_socket_client(
				'ssl://gateway.push.apple.com:2195', $err,
				$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
			}

			if (!$fp)
				exit("Failed to connect: $err $errstr" . PHP_EOL);
			// Create the payload body
			$body['aps'] = array(
				'alert' => array(
				    'title' => "Get Good",
	                'body' => $message,
				 ),
				'sound' => 'default'
			);
			// Encode the payload as JSON
			$payload = json_encode($body);
			// Build the binary notification
			$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
			// Send it to the server
			$result = fwrite($fp, $msg, strlen($msg));

			// Close the connection to the server
			fclose($fp);
			// if (!$result)
			// 	return 'Message not delivered' . PHP_EOL;
			// else
			// 	return 'Message successfully delivered' . PHP_EOL;
		}
		else if($sessions[0]['platform'] == 3)
		{
			$curl = curl_init();

			curl_setopt_array($curl, array(
			  CURLOPT_URL => "https://fcm.googleapis.com/fcm/send",
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => "",
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 30,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => "POST",
			  CURLOPT_POSTFIELDS => '{
					"notification": {
						"title": "Get Good",
						"body": "'.$message.'"
					},
					"to": "'.$sessions[0]['push_token'].'"
					
				}',
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

			// if ($err) {
			//   echo "cURL Error #:" . $err;
			// } else {
			//   echo $response;
			// }
		}
	}


	public static function sendSilentNotification($user, $userId, $dialogID)
	{
		$db = getDB();

		$sql = $db -> prepare("SELECT * FROM session WHERE user_id='$userId'");
		$sql -> execute();

		$sessions = $sql -> fetchAll(PDO::FETCH_ASSOC);
		print_r($sessions);
		if(count($sessions) == 0)
			return false;

		if($sessions[0]['platform'] == 1)
		{
			$push_token = $sessions[0]['push_token'];

		    $registrationIds = $push_token;
		#prep the bundle
		     $msg = array
		          	(
						'name' 	=> $user['name'],
						'dialog' => $dialogID
		          	);
			$fields = array
					(
						'to'		=> $registrationIds,
						'data'	=> $msg
					);


			$headers = array
					(
						'Authorization: key=' . "AIzaSyCAzL-ZfT5BqBgIeT4LKi-T4XzKkddgnlM",
						'Content-Type: application/json'
					);
		#Send Reponse To FireBase Server
				$ch = curl_init();
				curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
				curl_setopt( $ch,CURLOPT_POST, true );
				curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
				curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
				curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
				curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
				$result = curl_exec($ch );
				curl_close( $ch );
		#Echo Result Of FireBase Server
		echo $result;
		}
		else if($sessions[0]['platform'] == 2)
		{
			$deviceToken = $sessions[0]['push_token'];
			echo $deviceToken;
			$ctx = stream_context_create();
			// ck.pem is your certificate file

			if(environment == 'dev')
			{
				stream_context_set_option($ctx, 'ssl', 'local_cert', __DIR__.'/pushcert.pem');
			}
			else
			{
				stream_context_set_option($ctx, 'ssl', 'local_cert', __DIR__.'/pushcert_prod.pem');
			}

			stream_context_set_option($ctx, 'ssl', 'passphrase', "qwert");
			// Open a connection to the APNS server
			$fp = null;

			if(environment == 'dev')
			{
				$fp = stream_socket_client(
				'ssl://gateway.sandbox.push.apple.com:2195', $err,
				$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
			}
			else
			{

				$fp = stream_socket_client(
				'ssl://gateway.push.apple.com:2195', $err,
				$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
			}

			if (!$fp)
				exit("Failed to connect: $err $errstr" . PHP_EOL);
			// Create the payload body

			$body = array(
		        "aps" => array(
		            "content-available" => 1,
		            "sound" => ""
		        ),
				'name' 	=> $user['name'],
				'dialog' => $dialogID
		    );

			// $body['aps'] = array(
			// 	'alert' => array(
			// 	    'title' => "Get Good",
	  //               'body' => $message,
			// 	 ),
			// 	'sound' => 'default'
			// );
			// Encode the payload as JSON
			$payload = json_encode($body);
			// Build the binary notification
			$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
			// Send it to the server
			$result = fwrite($fp, $msg, strlen($msg));

			// Close the connection to the server
			fclose($fp);
			if (!$result)
				return 'Message not delivered' . PHP_EOL;
			else
				return 'Message successfully delivered' . PHP_EOL;
		}

	}

}
