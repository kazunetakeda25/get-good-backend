<?php


class Admin_Post
{
	public $title;
	public $post;

	public function __construct($_title, $_post)
	{
		$this->title = $_title;
		$this->post = $_post;
	}	

	public function save()
	{
		$db = getDB();

		$sql = $db -> prepare("INSERT INTO admin_post (title, post) VALUES (:title, :post)");
		$sql -> bindParam(':title', $this->title);
		$sql -> bindParam(':post', $this->post);
		$sql -> execute();
	}

	public static function getList($game)
	{
		$db = getDB();

		$sql = $db -> prepare("SELECT * FROM admin_post WHERE game='$game'");
		$sql -> execute();
		$posts = $sql -> fetchAll(PDO::FETCH_ASSOC);

		return $posts;
	}

	public static function getWithID($id)
	{
		$db = getDB();

		$sql = $db -> prepare("SELECT * FROM admin_post WHERE id='$id'");
		$sql -> execute();
		$posts = $sql -> fetchAll(PDO::FETCH_ASSOC);

		return $posts[0];
	}
}
