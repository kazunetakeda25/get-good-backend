<?php


class Comment
{
	public $comment;
	public $thread;
	public $owner_id;
	public $reference;

	public function __construct($_comment, $_thread, $_owner_id, $_reference)
	{
		$this->comment = $_comment;
		$this->thread = $_thread;
		$this->owner_id = $_owner_id;
		$this->reference = $_reference;
	}

	public function save()
	{
		$db = getDB();

		$timestamp = time();

		$sql = $db -> prepare("INSERT INTO comment (comment, thread, owner_id, reference, timestamp) VALUES (:comment, :thread, :owner_id, :reference, '$timestamp')");


		$sql -> bindParam(':comment', $this->comment);
		$sql -> bindParam(':thread', $this->thread);
		$sql -> bindParam(':owner_id', $this->owner_id);
		$sql -> bindParam(':reference', $this->reference);

		$sql -> execute();
	}

	public static function getList($thread, $reference)
	{
		$db = getDB();
		if(strlen($thread) == 0)
		{
			$sql = $db -> prepare("SELECT comment.*, user.name, user.avatar_url FROM comment LEFT JOIN user ON comment.owner_id=user.id WHERE comment.reference='$reference' ORDER BY timestamp DESC");
		}
		else
		{
			$sql = $db -> prepare("SELECT comment.*, user.name, user.avatar_url FROM comment LEFT JOIN user ON comment.owner_id=user.id WHERE comment.thread='$thread' ORDER BY timestamp DESC");
		}
		$sql -> execute();

		$comments = $sql -> fetchAll(PDO::FETCH_ASSOC);

		return $comments;
	}
}
