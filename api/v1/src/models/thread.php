<?php


class Thread
{
	public $title;
	private $description;
	public $owner_id;
	public $timestamp;
	public $game;

	public function __construct($_title, $_description, $_game, $_owner_id)
	{
		$this->title = $_title;
		$this->description = $_description;
		$this->owner_id = $_owner_id;
		$this->game = $_game;
	}

	public function save()
	{
		$db = getDB();

		$this->timestamp = time();

		$sql = $db -> prepare("INSERT INTO thread (title, description, owner_id, timestamp, game) VALUES (:title, :description, :owner_id, :timestamp, :game)");


		$sql -> bindParam(':title', $this->title);
		$sql -> bindParam(':description', $this->description);
		$sql -> bindParam(':owner_id', $this->owner_id);
		$sql -> bindParam(':timestamp', $this->timestamp);
		$sql -> bindParam(':game', $this->game);

		$sql -> execute();
	}

	public static function getWithID($id)
	{
		$db = getDB();

		$sql = $db -> prepare("SELECT thread.* , user.name, user.avatar_url FROM thread LEFT JOIN user ON thread.owner_id=user.id WHERE thread.id='$id'");
		$sql -> execute();
		$posts = $sql -> fetchAll(PDO::FETCH_ASSOC);

		return $posts[0];
	}

	public static function getList($page, $keyword, $game)
	{
		$db = getDB();

		if($page > -1)
		{
			if(strlen($keyword) == 0)
			{
				$sql = $db -> prepare("SELECT thread.* , user.name, user.avatar_url FROM thread LEFT JOIN user ON thread.owner_id=user.id WHERE thread.game='$game' ORDER BY thread.timestamp DESC LIMIT 1 OFFSET ".$page * 1);
			}
			else
			{
				$sql = $db -> prepare("SELECT thread.* , user.name, user.avatar_url FROM thread LEFT JOIN user ON thread.owner_id=user.id WHERE (thread.title LIKE '%$keyword%' OR user.name LIKE '%$keyword%') AND thread.game='$game' ORDER BY thread.timestamp DESC LIMIT 1 OFFSET ".$page * 1);
			}
		}
		else
		{
			if(strlen($keyword) == 0)
			{
				$sql = $db -> prepare("SELECT thread.* , user.name, user.avatar_url FROM thread LEFT JOIN user ON thread.owner_id=user.id WHERE thread.game='$game' ORDER BY thread.timestamp DESC");
			}
			else
			{
				$sql = $db -> prepare("SELECT thread.* , user.name, user.avatar_url FROM thread LEFT JOIN user ON thread.owner_id=user.id WHERE (thread.title LIKE '%$keyword%' OR user.name LIKE '%$keyword%') AND thread.game='$game' ORDER BY thread.timestamp DESC");
			}
		}


		$sql -> execute();

		$threads = $sql -> fetchAll(PDO::FETCH_ASSOC);

		return $threads;
	}

}
