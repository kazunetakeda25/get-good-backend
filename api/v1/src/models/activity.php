<?php


class Activity
{
	public $user_id;
	public $message;

	public function __construct($_user_id, $_message)
	{
		$this->user_id = $_user_id;
		$this->message = $_message;
	}	

	public function save()
	{
		$db = getDB();

		$sql = $db -> prepare("INSERT INTO activity (user_id, message) VALUES (:user_id, :message)");
		$sql -> bindParam(':user_id', $this->user_id);
		$sql -> bindParam(':message', $this->message);
		$sql -> execute();
	}

	public static function getList($id)
	{
		$db = getDB();

		$sql = $db -> prepare("SELECT * FROM activity WHERE user_id='$id' ORDER BY id DESC");
		$sql -> execute();
		$posts = $sql -> fetchAll(PDO::FETCH_ASSOC);

		return $posts;
	}
}
