<?php


class Group
{
	public $id;
	public $average_game_rating;
	public $average_player_rating;
	public $description;
	public $hero;
	public $hero_count;
	public $inactive;
	public $owner_id;
	public $title;
	public $users;
	public $pending_users;
	public $game;

	public function __construct($user, $_title, $_description, $_hero, $_game)
	{
		$this->owner_id = $user['id'];
		$this->title = $_title;
		$this->description = $_description;
		$this->hero = $_hero;
		$this->game = $_game;

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

		if($_game == 0)
		{
			$this->average_game_rating = $user['overwatch_rank'] ;
			$this->average_player_rating = $user['player_rating'] ;
		}
		else if($_game == 1)
		{
			$this->average_game_rating = array_search($user['lol_rank'], $ranks);
			$this->average_player_rating = $user['lol_player_rating'] ;
		}

		$this->hero_count = count(explode(" ", $_hero));
	}

	public function save()
	{
		$db = getDB();

		$time = time();

		$sql = $db -> prepare("INSERT INTO tbl_group (owner_id, title, description, hero, hero_count, average_game_rating, average_player_rating, game, timestamp) VALUES (:owner_id, :title, :description, :hero, :hero_count, :average_game_rating, :average_player_rating, :game, '$time')");

		$sql -> bindParam(':owner_id', $this->owner_id);
		$sql -> bindParam(':title', $this->title);
		$sql -> bindParam(':description', $this->description);
		$sql -> bindParam(':hero', $this->hero);
		$sql -> bindParam(':hero_count', $this->hero_count);
		$sql -> bindParam(':game', $this->game);
		$sql -> bindParam(':average_game_rating', $this->average_game_rating);
		$sql -> bindParam(':average_player_rating', $this->average_player_rating);

		$sql -> execute();
	}

	public static function getList($Page, $Sort, $PlayerRatingMin, $PlayerRatingMax, $GameRatingMin, $GameRatingMax, $Server, $Platform, $Online, $Category, $Keyword, $Game)
	{		
		$db = getDB();

		$query = "SELECT tbl_group.* ,user.id as user_id, user.email,user.name,user.verified,user.avatar_url,user.description as group_description,user.blizzard_id,user.join_date,user.overwatch_rank,user.server, user.lol_server,user.overwatch_heroes,user.overwatch_hero_count,user.coach_review_count,user.coach_rating,user.trainee_review_count,user.trainee_rating,user.player_review_count,user.player_rating, user.lol_heroes, user.lol_player_rating, user.lol_id, user.lol_rank FROM tbl_group INNER JOIN user on tbl_group.owner_id=user.id";


		$where = "AND tbl_group.inactive='0'";
		$time = time();

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

		if($Game == 1)
		{
			$GameRatingMin = array_search($GameRatingMin, $ranks);
			$GameRatingMax = array_search($GameRatingMax, $ranks);

			if($GameRatingMax == 0)
				$GameRatingMax = -1;
			
			if($GameRatingMin == 0)
				$GameRatingMin = -1;
		}
		
		if($PlayerRatingMin != -1)
		{
			$where .= " AND tbl_group.average_player_rating >= $PlayerRatingMin";
		}
		if($PlayerRatingMax != -1)
		{
			$where .= " AND tbl_group.average_player_rating <= $PlayerRatingMax";
		}
		if($GameRatingMin != -1)
		{
			$where .= " AND tbl_group.average_game_rating >= $GameRatingMin";
		}
		if($GameRatingMax != -1)
		{
			$where .= " AND tbl_group.average_game_rating <= $GameRatingMax";
		}
		
		if(strlen($Server) != 0 && $Server != "all")
		{
			$where .= " AND user.server LIKE '%$Server%'";
		}
		if(strlen($Platform) != 0 && $Platform != "all")
		{
			$where .= " AND user.server LIKE '%$Platform%'";
		}
		
		if($Online == 2)
		{
			$where .= " AND tbl_group.ready > $time - 180";
		}
		else if($Online == 1)
		{
			$where .= " AND user.online > $time - 7";
		}
		else if($Online == 0)
		{
			$where .= " AND user.online <= $time - 7";
		}

		if(strlen($Keyword) != 0)
		{
			$where .= " AND ((tbl_group.title LIKE '%$Keyword%') OR (user.name LIKE '%$Keyword%'))";
		}
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

				$where .= " AND tbl_group.hero LIKE '%".$hero."%'";
			}
		}

		$where .= " AND game = ".$Game;

		$order="";
		if($Sort == "popular")
		{
			$order .= " ORDER BY user.player_review_count DESC";
		}
		else if($Sort == "player_rating_low")
		{			
			$order .= " ORDER BY tbl_group.average_player_rating ASC";
		}
		else if($Sort == "player_rating_high")
		{			
			$order .= " ORDER BY tbl_group.average_player_rating DESC";
		}
		else if($Sort == "game_rating_low")
		{
			$order .= " ORDER BY tbl_group.average_game_rating ASC";			
		}
		else if($Sort == "game_rating_high")
		{
			$order .= " ORDER BY tbl_group.average_game_rating DESC";	
		}
		else if($Sort == "relevance")
		{			
			$order .= " ORDER BY tbl_group.hero_count ASC";	
		}

		if(strlen($order) != 0)
		{
			$order .= ", tbl_group.id DESC";
		}
		else 
		{
			$order .= " ORDER BY tbl_group.id DESC";
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
		$groups = $sql -> fetchAll(PDO::FETCH_ASSOC);
		// $sql -> debugDumpParams();
		return $groups;
	}

	public static function update($id, $title, $description, $hero)
	{
		$db = getDB();

		$time = time();
		$sql = $db -> prepare("UPDATE tbl_group SET title='$title', description='$description', hero='$hero', timestamp='$time' WHERE id='$id' ");
		$sql -> execute();
	}

	public static function getMyGroups($id, $game)
	{
		$db = getDB();

		$sql = $db -> prepare("SELECT * FROM tbl_group WHERE owner_id='$id' AND inactive='0' AND game='$game'");
		$sql -> execute();

		$groups = $sql -> fetchAll(PDO::FETCH_ASSOC);

		// $sql -> debugDumpParams();

		return $groups;
	}

	public static function getGroupsWithUserID($id, $game)
	{		
		$db = getDB();

		$sql = $db -> prepare("SELECT tbl_group.* ,user.id as user_id, user.email,user.name,user.verified,user.avatar_url,user.description as group_description,user.blizzard_id, user.join_date,user.overwatch_rank,user.server,user.lol_server,user.overwatch_heroes,user.overwatch_hero_count,user.coach_review_count,user.coach_rating,user.trainee_review_count,user.trainee_rating,user.player_review_count,user.player_rating, user.lol_heroes, user.lol_player_rating, user.lol_id, user.lol_rank FROM tbl_group INNER JOIN user on tbl_group.owner_id=user.id WHERE tbl_group.owner_id='$id' AND tbl_group.inactive='0' AND tbl_group.game='$game'");

		$sql -> execute();
		$groups = $sql -> fetchAll(PDO::FETCH_ASSOC);

		return $groups;
	}

	public static function getParticipating($id, $game)
	{		
		$db = getDB();

		$sql = $db -> prepare("SELECT tbl_group.* ,user.id as user_id, user.email,user.name,user.verified,user.avatar_url,user.description as group_description,user.blizzard_id, user.join_date,user.overwatch_rank,user.server, user.lol_server, user.overwatch_heroes,user.overwatch_hero_count,user.coach_review_count,user.coach_rating,user.trainee_review_count,user.trainee_rating,user.player_review_count,user.player_rating, user.lol_heroes, user.lol_player_rating, user.lol_id, user.lol_rank FROM tbl_group INNER JOIN user on tbl_group.owner_id=user.id WHERE (tbl_group.owner_id='$id' OR tbl_group.users LIKE '%:$id:%' OR tbl_group.pending_users LIKE '%:$id:%') AND (tbl_group.inactive = '0' AND tbl_group.game = '$game') ORDER BY timestamp DESC");

		$sql -> execute();
		$groups = $sql -> fetchAll(PDO::FETCH_ASSOC);

		// $sql -> debugDumpParams();
		return $groups;
	}

	public static function ready($id)
	{
		$db = getDB();

		$timestamp = time();
		$sql = $db -> prepare("UPDATE tbl_group SET ready='$timestamp' WHERE id='$id' ");
		$sql -> execute();

		return $timestamp;
	}

	public static function join($id, $user_id)
	{
		$db = getDB();

		$sql = $db -> prepare("SELECT * FROM tbl_group WHERE id='$id'");
		$sql -> execute();

		$groups = $sql -> fetchAll(PDO::FETCH_ASSOC);
		$group = $groups[0];

		$users = $group['users'];

		$pending_users = $group['pending_users'];

		$userIDs = explode(":", $pending_users);


		$updatedIds = ":";
		for($i = 0; $i < count($userIDs); $i++)
		{
			if(strlen($userIDs[$i]) == 0)
				continue;

			if($userIDs[$i] != $user_id)
			{
				$updatedIds .= $userIDs[$i].":";
			}
		}

		if(strlen($updatedIds) <= 1)
			$updatedIds = "";
		// if(strlen($updatedIds) > 0)
		// $updatedIds = substr($updatedIds, 1);

		$time = time();

		$sql = $db -> prepare("UPDATE tbl_group SET pending_users='$updatedIds' WHERE id='$id'");
		$sql -> execute();


		if(strpos($users, $user_id) !== false)
		{
			return;
		}

		if(strlen($users) > 0)
		{
			$users = $users.$user_id.":";
		}
		else
		{
			$users = ":".$user_id.":";
		}

		$sql = $db -> prepare("UPDATE tbl_group SET users='$users', timestamp='$time' WHERE id='$id'");
		$sql -> execute();

		Group::updateRating($id);
	}

	public static function apply($id, $user_id)
	{
		$db = getDB();

		$sql = $db -> prepare("SELECT * FROM tbl_group WHERE id='$id'");
		$sql -> execute();

		$groups = $sql -> fetchAll(PDO::FETCH_ASSOC);
		$group = $groups[0];

		$users = $group['pending_users'];

		if(strpos($users, $user_id) !== false)
		{
			return;
		}

		if(strlen($users) > 0)
		{
			$users = $users.$user_id.":";
		}
		else
		{
			$users = ":".$user_id.":";
		}

		$time = time();

		$sql = $db -> prepare("UPDATE tbl_group SET pending_users='$users', timestamp='$time' WHERE id='$id'");
		$sql -> execute();
	}

	public static function leave($id, $user_id)
	{
		$db = getDB();

		$sql = $db -> prepare("SELECT * FROM tbl_group WHERE id='$id'");
		$sql -> execute();

		$groups = $sql -> fetchAll(PDO::FETCH_ASSOC);
		$group = $groups[0];

		$users = $group['users'];
		if(strpos($users, $user_id) !== false)
		{
			$userIDs = explode(":", $users);


			$updatedIds = ":";
			for($i = 0; $i < count($userIDs); $i++)
			{
				if(strlen($userIDs[$i]) == 0)
					continue;

				if($userIDs[$i] != $user_id)
				{
					$updatedIds .= $userIDs[$i].":";
				}
			}

			if(strlen($updatedIds) <= 1)
				$updatedIds = "";

			$time = time();
			$sql = $db -> prepare("UPDATE tbl_group SET users='$updatedIds', timestamp='$time' WHERE id='$id'");
			$sql -> execute();

			Group::updateRating($id);
		}

		$pending_users = $group['pending_users'];
		if(strpos($pending_users, $user_id) !== false)
		{
			$userIDs = explode(":", $pending_users);


			$updatedIds = ":";
			for($i = 0; $i < count($userIDs); $i++)
			{
				if(strlen($userIDs[$i]) == 0)
					continue;

				if($userIDs[$i] != $user_id)
				{
					$updatedIds .= $userIDs[$i].":";
				}
			}

			if(strlen($updatedIds) <= 1)
			$updatedIds = "";

			$time = time();
			$sql = $db -> prepare("UPDATE tbl_group SET pending_users='$updatedIds', timestamp='$time' WHERE id='$id'");
			$sql -> execute();

			Group::updateRating($id);
		}

	}

	public static function getWithID($id)
	{		
		$db = getDB();

		$sql = $db -> prepare("SELECT tbl_group.* ,user.id as user_id, user.email,user.name,user.verified,user.avatar_url,user.description as group_description,user.blizzard_id,user.join_date,user.overwatch_rank,user.server,user.overwatch_heroes,user.overwatch_hero_count,user.coach_review_count,user.coach_rating,user.trainee_review_count,user.trainee_rating,user.player_review_count,user.player_rating, user.lol_heroes, user.lol_player_rating, user.lol_id, user.lol_rank FROM tbl_group INNER JOIN user on tbl_group.owner_id=user.id WHERE tbl_group.id='$id'");
		$sql -> execute();

		$groups = $sql -> fetchAll(PDO::FETCH_ASSOC);

		return $groups;
	}

	public static function updateRating($id)
	{
		$db = getDB();

		$sql = $db -> prepare("SELECT * FROM tbl_group WHERE id='$id'");
		$sql -> execute();
		$groups = $sql -> fetchAll(PDO::FETCH_ASSOC);

		$group = $groups[0];

		$userIds = $group['users'];
		if(strlen($userIds) > 0)
		{
			$userIds = $userIds.":".$group['owner_id'].":";
		}
		else
		{
			$userIds = $group['owner_id'];
		}


		$arIds = explode(":", $userIds);

		$arUsers = array();
		$gameRating = 0;
		$playeRating = 0;

		$count = 0;
		$gameRatingCount = 0;
		
		$playerRatingKey = "";
		$rankKay = "";

		if($group['game'] == 0)
		{
			$playerRatingKey = "player_rating";
			$rankKay = "overwatch_rank";
		}
		else if($group['game'] == 1)
		{
			$playerRatingKey = "lol_player_rating";
			$rankKay = "lol_rank";
		}

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

		for($i = 0; $i < count($arIds); $i++)
		{
			$userId = $arIds[$i];

			if(strlen($userId) == 0)
				continue;

			$count ++;
			$profile = User::getProfile($userId);
			$playeRating += $profile[$playerRatingKey];

			if($group['game'] == 0)
				$gameRating += $profile[$rankKay];
			else if($group['game'] == 1)
			{				
				$gameRating += array_search($profile[$rankKay], $ranks);
			}

			$gameRatingCount ++;
		}

		if(count($arIds) != 0)
		{
			$playeRating /= $count;
		}

		if($group['game'] == 0)
		{
			if($gameRatingCount != 0)
				$gameRating /= $gameRatingCount;
		}
		else if($group['game'] == 1)
		{
			if($gameRatingCount != 0)
				$gameRating /= $gameRatingCount;

			$gameRating = round($gameRating);
		}

				
		$sql = $db -> prepare("UPDATE tbl_group SET average_game_rating='$gameRating', average_player_rating='$playeRating' WHERE id='$id'");
		$sql -> execute();
	}

	public static function getMessages($id)
	{
		$db = getDB();

		$sql = $db -> prepare("SELECT message.*, user.avatar_url AS avatar_url, user.name AS name FROM message LEFT JOIN user ON user.id=message.user_id WHERE message.dialog_id='$id' AND message.type='10' ORDER BY message.id ASC");

		$sql -> execute();

		$messages = $sql -> fetchAll(PDO::FETCH_ASSOC);

		// $sql -> debugDumpParams();
		return $messages;
	}

	public static function delete($group_id)
	{
		$db = getDB();

		$sql = $db -> prepare("UPDATE tbl_group SET inactive=1 WHERE id='$group_id'");
		$sql -> execute();
	}


	public static function getLastTimestamp($id, $game)
	{
		$db = getDB();

		$sql = $db -> prepare("SELECT MAX(timestamp) AS timestamp FROM tbl_group WHERE (owner_id='$id' OR users LIKE '%:$id:%' OR pending_users LIKE '%:$id:%') AND game='$game'");
		$sql -> execute();

		$timestamp = $sql -> fetchAll(PDO::FETCH_ASSOC);

		// $sql -> debugDumpParams();
		return ($timestamp[0]['timestamp']);
	}
}