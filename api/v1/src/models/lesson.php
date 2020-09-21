<?php


class Lesson
{
	public $id;
	public $title;
	public $description;
	public $hero;
	public $hero_count;
	public $inactive;
	public $price;
	public $owner_id;
	public $server;
	public $thumb_url;
	public $time;
	public $videos;
	public $game;

	public function __construct($_title, $_description, $_hero, $_videos, $_thumb_url, $_price, $user_id, $_game)
	{
		$this->title = $_title;
		$this->description = $_description;
		$this->hero = $_hero;
		$this->videos = $_videos;
		$this->thumb_url = str_replace("images/", "images%2F", $_thumb_url);
		$this->price = $_price;
		$this->owner_id = $user_id;
		$this->hero_count = count(explode(" ", $_hero));
		$this->game = $_game;
	}

	public function save()
	{
		$db = getDB();

		$sql = $db -> prepare("INSERT INTO lesson (owner_id, title, description, hero, hero_count, videos, thumb_url, price, game) VALUES (:owner_id, :title, :description, :hero, :hero_count, :videos, :thumb_url, :price, :game)");

		$sql -> bindParam(':owner_id', $this->owner_id);
		$sql -> bindParam(':title', $this->title);
		$sql -> bindParam(':description', $this->description);
		$sql -> bindParam(':hero', $this->hero);
		$sql -> bindParam(':hero_count', $this->hero_count);
		$sql -> bindParam(':videos', $this->videos);
		$sql -> bindParam(':thumb_url', $this->thumb_url);
		$sql -> bindParam(':price', $this->price);
		$sql -> bindParam(':game', $this->game);

		$sql -> execute();
		// $sql -> debugDumpParams();
	}

	public static function getList($Page, $Sort, $CoachRatingMin, $CoachRatingMax, $GameRatingMin, $GameRatingMax, $PriceMax, $PriceMin, $Server, $Platform, $Online, $Category, $Keyword, $Game)
	{		
		$db = getDB();
		$query = "SELECT lesson.* ,user.id as user_id, user.email,user.name,user.verified,user.avatar_url,user.description as group_description,user.blizzard_id,user.join_date,user.overwatch_rank,user.server as user_server,user.overwatch_heroes,user.overwatch_hero_count,user.coach_review_count,user.coach_rating,user.trainee_review_count,user.trainee_rating,user.player_review_count,user.player_rating, user.lol_rank, user.lol_server, user.lol_coach_rating, user.lol_coach_review_count FROM lesson INNER JOIN user on lesson.owner_id=user.id";

		$where = " AND lesson.inactive='0'";
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

		if($CoachRatingMin != -1)
		{
			$where .= " AND user.coach_rating >= $CoachRatingMin";
		}
		if($CoachRatingMax != -1)
		{
			$where .= " AND user.coach_rating <= $CoachRatingMax";
		}
		if($Game == 0)
		{
			if($GameRatingMin != -1)
			{
				$where .= " AND user.overwatch_rank >= $GameRatingMin";
			}
			if($GameRatingMax != -1)
			{
				$where .= " AND user.overwatch_rank <= $GameRatingMax";
			}
		}
		else if($Game == 1)
		{			
			$GameRatingMin = array_search($GameRatingMin, $ranks);
			$GameRatingMax = array_search($GameRatingMax, $ranks);

			if($GameRatingMax == 0)
				$GameRatingMax = -1;
			
			if($GameRatingMin == 0)
				$GameRatingMin = -1;

			if($GameRatingMin != -1)
			{
				$where .= " AND user.lol_rank >= $GameRatingMin";
			}
			if($GameRatingMax != -1)
			{
				$where .= " AND user.lol_rank <= $GameRatingMax";
			}
		}

		if($PriceMin != -1)
		{
			$where .= " AND lesson.price >= $PriceMin";
		}
		if($PriceMax != -1 && $PriceMax != 50)
		{
			$where .= " AND lesson.price <= $PriceMax";
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
			$where .= " AND lesson.ready > $time - 180";
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
			$where .= " AND ((lesson.title LIKE '%$Keyword%') OR (user.name LIKE '%$Keyword%'))";
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

				$where .= " AND lesson.hero LIKE '%".$hero."%'";
			}
		}

		$where .= " And lesson.game = ".$Game;
		$order="";
		if($Sort == "popular")
		{
			$order .= " ORDER BY user.player_review_count DESC";
		}
		else if($Sort == "coach_rating_low")
		{			
			$order .= " ORDER BY user.coach_rating ASC";
		}
		else if($Sort == "coach_rating_high")
		{			
			$order .= " ORDER BY user.coach_rating DESC";
		}
		else if($Sort == "game_rating_low")
		{
			if($Game == 0)
				$order .= " ORDER BY user.overwatch_rank ASC";			
			else if($Game == 1)				
				$order .= " ORDER BY user.lol_rank ASC";	
		}
		else if($Sort == "game_rating_high")
		{
			if($Game == 0)
				$order .= " ORDER BY user.overwatch_rank DESC";			
			else if($Game == 1)				
				$order .= " ORDER BY user.lol_rank DESC";
		}
		else if($Sort == "price_low")
		{
			$order .= " ORDER BY lesson.price ASC";			
		}
		else if($Sort == "price_high")
		{
			$order .= " ORDER BY lesson.price DESC";	
		}
		else if($Sort == "relevance")
		{			
			$order .= " ORDER BY lesson.hero_count ASC";	
		}

		if(strlen($order) != 0)
		{
			$order .= ", lesson.id DESC";
		}
		else 
		{
			$order .= " ORDER BY id DESC";
		}
		

		$where = substr($where, 4);
		$query .= " WHERE ".$where.$order;

		$query .= " LIMIT 10 OFFSET ".$Page * 10;

		$sql = $db -> prepare($query);
		$sql -> execute();
		$lessons = $sql -> fetchAll(PDO::FETCH_ASSOC);
		// $sql -> debugDumpParams();
		return $lessons;
	}

	public static function getMyLessons($id, $game)
	{
		$db = getDB();

		$sql = $db -> prepare("SELECT * FROM lesson WHERE owner_id='$id' AND inactive = '0' AND game='$game'");
		$sql -> execute();

		$groups = $sql -> fetchAll(PDO::FETCH_ASSOC);

		return $groups;
	}

	public static function update($id, $title, $description, $hero, $price, $server, $thumb_url, $videos, $ready)
	{
		$db = getDB();
		$thumb_url = str_replace("images/", "images%2F", $thumb_url);

		$hero_count = count(explode(" ", $_hero));
		$sql = $db -> prepare("UPDATE lesson SET title='$title', description='$description', hero='$hero', hero_count='$hero_count', price='$price', thumb_url='$thumb_url', videos='$videos', ready='$ready' WHERE id='$id' ");
		$sql -> execute();
	}

	public static function getLessonsWithUserID($id, $game)
	{
		$db = getDB();

		$sql = $db -> prepare("SELECT lesson.* ,user.id as user_id, user.email,user.name,user.verified,user.avatar_url,user.description as group_description,user.blizzard_id,user.join_date,user.overwatch_rank,user.server as user_server,user.lol_server as user_lol_server,user.overwatch_heroes,user.overwatch_hero_count,user.coach_review_count,user.coach_rating,user.trainee_review_count,user.trainee_rating,user.player_review_count,user.player_rating,user.lol_rank,user.lol_server FROM lesson INNER JOIN user on lesson.owner_id=user.id WHERE lesson.owner_id='$id' AND lesson.inactive='0' AND lesson.game='$game'");

		$sql -> execute();
		$groups = $sql -> fetchAll(PDO::FETCH_ASSOC);

		return $groups;
	}

	public static function getWithID($id)
	{
		$db = getDB();

		$sql = $db -> prepare("SELECT * FROM lesson WHERE id='$id'");
		$sql -> execute();

		$lessons = $sql -> fetchAll(PDO::FETCH_ASSOC);
		return $lessons[0];
	}

	public static function ready($id)
	{
		$db = getDB();

		$timestamp = time();
		$sql = $db -> prepare("UPDATE lesson SET ready='$timestamp' WHERE id='$id' ");
		$sql -> execute();

		return $timestamp;
	}

	public static function delete($id)
	{
		$db = getDB();

		$sql = $db -> prepare("UPDATE lesson SET inactive=1 WHERE id='$id'");
		$sql -> execute();
	}
}