<?php


class User
{
	public $email;
	public $avatar_url;
	private $password;
	public $name;

	public function __construct($_email, $_name, $_avatar_url, $_password)
	{
		$this->email = $_email;
		$this->avatar_url = $_avatar_url;
		$this->name = $_name;
		$this->password = $_password;
	}	

	public function register()
	{
		$db = getDB();

		$sql = $db -> prepare("SELECT * FROM user WHERE email = :email");
		$sql -> bindParam(':email', $this->email);
		$sql -> execute();
		$date = date('d M y', time());

		$users = $sql -> fetchAll(PDO::FETCH_ASSOC);

		if(count($users) == 0)
		{
			$sql = $db -> prepare("INSERT INTO user (email, name, avatar_url, password, join_date) VALUES (:email, :name, :avatar, PASSWORD(:password), '$date')");

			$sql -> bindParam(':email', $this->email);
			$sql -> bindParam(':name', $this->name);
			$sql -> bindParam(':avatar', str_replace("images/", "images%2F", $this->avatar_url));
			$sql -> bindParam(':password', $this->password);

			$sql -> execute();

			return true;
		}
		else
		{
			return  false;
		}
	}

	public static function login($email, $password)
	{
		$db = getDB();

		$sql = $db -> prepare("SELECT * FROM user WHERE email='$email' AND password=PASSWORD('$password')");

		$sql -> execute();		
		$users = $sql -> fetchAll(PDO::FETCH_ASSOC);

		if(count($users) > 0)
		{	
			$users[0]['password'] = null;
			return $users[0];
		}
		else
		{
			return null;
		}
	}

	public static function getProfile($user_id)
	{
		$db = getDB();

		$sql = $db -> prepare("SELECT * FROM user WHERE id='$user_id'");
		$sql -> execute();

		$users = $sql -> fetchAll(PDO::FETCH_ASSOC);
		$users[0]['password'] = null;

		return $users[0];
	}

	public static function getWithEmail($user_email)
	{
		$db = getDB();

		$sql = $db -> prepare("SELECT * FROM user WHERE email='$user_email'");
		$sql -> execute();

		$users = $sql -> fetchAll(PDO::FETCH_ASSOC);

		if(count($users) > 0)
		{
			$users[0]['password'] = null;
			return $users[0];
		}
		else
		{
			return null;
		}
	}

	public static function getWithName($name)
	{
		$db = getDB();

		$sql = $db -> prepare("SELECT * FROM user WHERE name='$name'");
		$sql -> execute();

		$users = $sql -> fetchAll(PDO::FETCH_ASSOC);

		if(count($users) > 0)
		{
			$users[0]['password'] = null;
			return $users[0];
		}
		else
		{
			return null;
		}
	}

	public static function checkName($name, $id)
	{
		$db = getDB();

		$sql = $db -> prepare("SELECT * FROM user WHERE name='$name' AND id!='$id'");
		$sql -> execute();

		$users = $sql -> fetchAll(PDO::FETCH_ASSOC);

		if(count($users) > 0)
		{
			$users[0]['password'] = null;
			return $users[0];
		}
		else
		{
			return null;
		}
	}

	public static function getWithGameId($gameid)
	{
		$db = getDB();

		$sql = $db -> prepare("SELECT * FROM user WHERE blizzard_id='$gameid'");
		$sql -> execute();

		$users = $sql -> fetchAll(PDO::FETCH_ASSOC);


		if(count($users) > 0)
		{
			$users[0]['password'] = null;
			return $users[0];
		}
		else
		{
			return null;
		}
	}

	public static function checkGameId($gameid, $id)
	{
		$db = getDB();

		$sql = $db -> prepare("SELECT * FROM user WHERE blizzard_id='$gameid' AND id!='$id'");
		$sql -> execute();

		$users = $sql -> fetchAll(PDO::FETCH_ASSOC);

		if(count($users) > 0)
		{
			$users[0]['password'] = null;
			return $users[0];
		}
		else
		{
			return null;
		}
	}

	public static function checkLOLId($gameid, $id)
	{
		$db = getDB();

		$sql = $db -> prepare("SELECT * FROM user WHERE lol_id='$gameid' AND id!='$id'");
		$sql -> execute();

		$users = $sql -> fetchAll(PDO::FETCH_ASSOC);

		if(count($users) > 0)
		{
			$users[0]['password'] = null;
			return $users[0];
		}
		else
		{
			return null;
		}
	}

	public static function verifyEmail($email)
	{
		$db = getDB();
		$sql = $db -> prepare("UPDATE user SET verified=true WHERE email='$email'");
		$sql -> execute();
	}

	public static function resetPassword($email, $password)
	{
		$db = getDB();
		$sql = $db -> prepare("UPDATE user SET password=PASSWORD('$password') WHERE email='$email'");
		$sql -> execute();
	}

	public static function getPlayerList($Page, $Sort, $PlayerRatingMin, $PlayerRatingMax, $GameRatingMin, $GameRatingMax, $Server, $Platform, $Online, $Category, $Keyword, $Game)
	{

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



		$db = getDB();
		$query = "SELECT * FROM user";


		$where = "";

		$time = time();
		$prefix = "";
		if($Game == 0)
		{
			$prefix = "";
		}
		else if($Game == 1)
		{
			$prefix = "lol_";

			$GameRatingMin = array_search($GameRatingMin, $ranks);
			$GameRatingMax = array_search($GameRatingMax, $ranks);

			if($GameRatingMax == 0)
				$GameRatingMax = -1;
			
			if($GameRatingMin == 0)
				$GameRatingMin = -1;
		}

		if($PlayerRatingMin != -1)
		{
			$where .= " AND ".$prefix."player_rating >= $PlayerRatingMin";
		}
		if($PlayerRatingMax != -1)
		{
			$where .= " AND ".$prefix."player_rating <= $PlayerRatingMax";
		}

		if($GameRatingMin != -1)
		{
			if($Game == 0)
				$where .= " AND overwatch_rank >= $GameRatingMin";
			else if($Game == 1)					
				$where .= " AND lol_rank >= $GameRatingMin";
		}
		if($GameRatingMax != -1)
		{
			if($Game == 0)
				$where .= " AND overwatch_rank <= $GameRatingMax";
			else if($Game == 1)					
				$where .= " AND lol_rank <= $GameRatingMax";
		}
		
		if(strlen($Server) != 0 && $Server != "all")
		{
			$where .= " AND ".$prefix."server LIKE '%$Server%'";
		}
		
		if(strlen($Platform) != 0 && $Platform != "all")
		{
			$where .= " AND server LIKE '%$Platform%'";
		}

		if($Online == 2)
		{
			$where .= " AND ".$prefix."ready > $time - 180";
		}
		else if($Online == 1)
		{
			$where .= " AND online > $time - 7";
		}
		else if($Online == 0)
		{
			$where .= " AND online <= $time - 7";
		}

		if(strlen($Keyword) != 0)
		{
			$where .= " AND name LIKE '%$Keyword%'";
		}

		if($Game == 0)
		{

			if(strlen($Category) != 0)
			{
				$heros = explode(" ", $Category);
				for($i = 0; $i < count($heros); $i++)
				{
					$hero = $heros[$i];
					if(strlen($hero) == 0)
					{
						continue;
					}

					$where .= " AND (overwatch_heroes LIKE '%".$hero."%')";
				}
			}

		}
		else if($Game == 1)
		{

			if(strlen($Category) != 0)
			{
				$heros = explode(" ", $Category);
				for($i = 0; $i < count($heros); $i++)
				{
					$hero = $heros[$i];
					if(strlen($hero) == 0)
					{
						continue;
					}

					$where .= " AND (lol_heroes LIKE '%".$hero."%')";
				}
			}

		}

		$order="";
		if($Sort == "popular")
		{
			$order .= " ORDER BY ".$prefix."player_review_count DESC";
		}
		else if($Sort == "player_rating_low")
		{			
			$order .= " ORDER BY ".$prefix."player_rating ASC";
		}
		else if($Sort == "player_rating_high")
		{			
			$order .= " ORDER BY ".$prefix."player_rating DESC";
		}
		else if($Sort == "game_rating_low")
		{
			if($Game == 0)
				$order .= " ORDER BY overwatch_rank ASC";	
			else if($Game == 1)					
				$order .= " ORDER BY lol_rank ASC";	
		}
		else if($Sort == "game_rating_high")
		{
			if($Game == 0)
				$order .= " ORDER BY overwatch_rank DESC";	
			else if($Game == 1)					
				$order .= " ORDER BY lol_rank DESC";	
		}
		else if($Sort == "relevance")
		{			
			if($Game == 0)
			{
				$order .= " ORDER BY overwatch_hero_count ASC";	
			}
			else
			{				
				$order .= " ORDER BY ".$prefix."hero_count ASC";	
			}
		}

		if(strlen($order) != 0)
		{
			$order .= ", id DESC";
		}
		else 
		{
			$order .= " ORDER BY id DESC";
		}

		if(strlen($where) != 0)
		{
			$where = substr($where, 4);
			$query .= " WHERE ".$where.$order;
		}
		else
		{
			$query .= " ".$order;
		}


		$query .= " LIMIT 10 OFFSET ".$Page * 10;

		$sql = $db -> prepare($query);

		$sql -> execute();

		$users = $sql -> fetchAll(PDO::FETCH_ASSOC);

		for($i = 0; $i < count($users); $i++)
		{
			$users[$i]['lol_rank'] = $ranks[(int)$users[$i]['lol_rank']];
		}

		return $users;
	}

	public static function updateProfile($userid, $name, $avatar_url, $description, $blizzard_id, $overwatch_rank, $server, $overwatch_heroes)
	{
		$db = getDB();
		$hero_count = count(explode(" ", $overwatch_heroes));

		$avatar_url = str_replace("images/", "images%2F", $avatar_url);
		$sql = $db -> prepare("UPDATE user SET 
			name='$name',
			avatar_url='$avatar_url',
			description='$description',
			blizzard_id='$blizzard_id',
			overwatch_rank='$overwatch_rank',
			overwatch_hero_count='$hero_count',
			server='$server',
			overwatch_heroes='$overwatch_heroes'
			WHERE id='$userid'
			");

		$sql -> execute();		
	}


	public static function updateLoLProfile($userid, $name, $avatar_url, $description, $id, $rank, $server, $heroes, $category)
	{
		$db = getDB();
		$hero_count = count(explode(" ", $heroes));		

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


		$rank = array_search($rank, $ranks);
		$avatar_url = str_replace("images/", "images%2F", $avatar_url);
		$sql = $db -> prepare("UPDATE user SET 
			name='$name',
			avatar_url='$avatar_url',
			lol_description='$description',
			lol_id='$id',
			lol_rank='$rank',
			lol_heroes='$heroes',
			lol_hero_count='$hero_count',
			lol_category = '$category',
			lol_server='$server'
			WHERE id='$userid'
			");

		$sql -> execute();
		// $sql -> debugDumpParams();
	}

	public static function ready($userid, $game)
	{
		$db = getDB();
		$ready = time();
		if($game == 0)
		{
			$ready_string = "ready";
		}
		else if($game == 1)
		{
			$ready_string = "lol_ready";
		}

		$sql = $db -> prepare("UPDATE user SET 
			" . $ready_string . "='$ready' WHERE id='$userid'
			");

		$sql -> execute();	

		// $sql -> debugDumpParams();

		return $ready;	
	}

	public static function updateRating($id)
	{
		$db = getDB();
		$games = ['', 'lol_'];

		for($j = 0; $j < count($games); $j++)
		{
			$prefix = $games[$j];


			$sql = $db -> prepare("SELECT * FROM trainee_rating WHERE to_id='$id' AND game='$j'");
			$sql -> execute();

			$rates = $sql -> fetchAll(PDO::FETCH_ASSOC);
			$sum = 0;

			for($i = 0; $i < count($rates); $i++)
			{
				$rate = $rates[$i];
				$sum += $rate['general'];
			}

			$average = $sum / count($rates);
			$count = count($rates);

			$sql = $db -> prepare("UPDATE user SET ".$prefix."trainee_rating = '$average', ".$prefix."trainee_review_count='$count' WHERE id='$id'");
			$sql -> execute();


			$sql = $db -> prepare("SELECT * FROM coach_rating WHERE to_id='$id' AND game='$j'");
			$sql -> execute();

			$rates = $sql -> fetchAll(PDO::FETCH_ASSOC);
			$sum = 0;

			for($i = 0; $i < count($rates); $i++)
			{
				$rate = $rates[$i];
				$sum += $rate['flexibility'];
				$sum += $rate['competency'];
				$sum += $rate['communication'];
				$sum += $rate['attitude'];
			}

			$average = $sum / count($rates) / 4;
			$count = count($rates);

			$sql = $db -> prepare("UPDATE user SET ".$prefix."coach_rating = '$average', ".$prefix."coach_review_count='$count' WHERE id='$id'");
			$sql -> execute();

			$db = getDB();
			$sql = $db -> prepare("SELECT * FROM player_rating WHERE to_id='$id' AND game='$j'");	

			$sql -> execute();

			$playerRates = $sql -> fetchAll(PDO::FETCH_ASSOC);

			$fReview = 2.5;

			for($i = 0; $i < count($playerRates); $i++)
			{
				$playerRate = $playerRates[$i];

				$fRating = 0.0;

				if($playerRate['leader'] == 1)
					$fRating += 0.08;

				if($playerRate['cooperative'] == 1)
					$fRating += 0.04;

				if($playerRate['good_communication'] == 1)
					$fRating += 0.05;

				if($playerRate['sportsmanship'] == 1)
					$fRating += 0.06;

				if($playerRate['mvp'] == 1)
					$fRating += 0.1;

				if($playerRate['flex_player'] == 1)
					$fRating += 0.03;

				if($playerRate['good_hero_competency'] == 1)
					$fRating += 0.04;

				if($playerRate['good_ultimate_usage'] == 1)
					$fRating += 0.04;





				if($playerRate['abusive_chat'] == 1)
					$fRating -= 0.05;


				if($playerRate['griefing'] == 1)
					$fRating -= 0.08;


				if($playerRate['spam'] == 1)
					$fRating -= 0.03;


				if($playerRate['no_communication'] == 1)
					$fRating -= 0.05;


				if($playerRate['un_cooperative'] == 1)
					$fRating -= 0.04;


				if($playerRate['trickling_in'] == 1)
					$fRating -= 0.06;


				if($playerRate['poor_hero_competency'] == 1)
					$fRating -= 0.04;


				if($playerRate['bad_ultimate_usage'] == 1)
					$fRating -= 0.04;


				if($playerRate['overextending'] == 1)
					$fRating -= 0.05;

				$x = $fReview - 2.5;
				$fFactor = 1.0;

				if($x == 0)
				{
					$fFactor = 1.0;
				}
				else if($x > 0 && $fRating > 0)
				{
					$fFactor = (exp(2.5) - exp(abs($x))) / (exp(2.5) - 1);
				}
				else if($x > 0 && $fRating < 0)
				{
					$fFactor = 1.0;
				}
				else if($x < 0 && $fRating > 0)
				{
					$fFactor = 1.0;
				}
				else if($x < 0 && $fRating < 0)
				{
					$fFactor = (exp(2.5) - exp(abs($x))) / (exp(2.5) - 1);
				}

				$fReview += $fFactor * $fRating;
			}
			$fCount = count($playerRates);

			$sql = $db -> prepare("UPDATE user SET ".$prefix."player_rating='$fReview', ".$prefix."player_review_count='$fCount' WHERE id='$id'");
			$sql -> execute();

		}
		

		$sql = $db -> prepare("SELECT * FROM tbl_group WHERE owner_id='$id' OR users LIKE '%$id%'");
		$sql -> execute();

		$groups = $sql -> fetchAll(PDO::FETCH_ASSOC);

		for($i = 0; $i < count($groups); $i++)
		{
			Group::updateRating($groups[$i]['id']);
		}
	}

	public static function getUsers($ids)
	{
		$arIds = explode(":", $ids);

		$arUsers = array();
		$index = 0;
		for($i = 0; $i < count($arIds); $i++)
		{
			$id = $arIds[$i];

			if(strlen($id) == 0)
				continue;

			$profile = User::getProfile($id);
			$arUsers[$index++] = $profile;
		}

		return $arUsers;
	}

	public static function online($id)
	{	
		$time = time();

		$db = getDB();
		$sql = $db -> prepare("UPDATE user SET online='$time' WHERE id='$id'");
		$sql -> execute();

		$sql -> debugDumpParams();
	}


}
