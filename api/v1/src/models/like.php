<?php


class Like
{
	public $comment;
	public $user_id;
	public $like;

	public function __construct($_comment, $_user_id, $_like)
	{
		$this->comment = $_comment;
		$this->user_id = $_user_id;
		$this->like = $_like;
	}

	public function save()
	{
		$db = getDB();

		$sql = $db -> prepare("DELETE FROM comment_like WHERE comment=:comment AND user_id=:user_id");

		$sql -> bindParam(':comment', $this->comment);
		$sql -> bindParam(':user_id', $this->user_id);
		$sql -> execute();

		$sql = $db -> prepare("INSERT INTO comment_like (comment, user_id, data) VALUES (:comment, :user_id, :data)");


		$sql -> bindParam(':comment', $this->comment);
		$sql -> bindParam(':user_id', $this->user_id);
		$sql -> bindParam(':data', $this->like);

		$sql -> execute();
	}

	public static function getLikes($comment)
	{
		$db = getDB();

		$sql = $db -> prepare("SELECT * FROM comment_like WHERE comment='$comment'");
		$sql -> execute();

		$likes = $sql -> fetchAll(PDO::FETCH_ASSOC);
		return $likes;
	}
}
