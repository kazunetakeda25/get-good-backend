<?php


class Feedback
{
	public $content;
	public $user_id;
	public $timestamp;

	public function __construct($_content, $_user_id)
	{
		$this->content = $_content;
		$this->user_id = $_user_id;
		$this->timestamp = time();
	}

	public function save()
	{
		$db = getDB();

		$sql = $db -> prepare("INSERT INTO feedback (content, user_id, timestamp) VALUES (:content, :user_id, :timestamp)");


		$sql -> bindParam(':content', $this->content);
		$sql -> bindParam(':user_id', $this->user_id);
		$sql -> bindParam(':timestamp', $this->timestamp);

		$sql -> execute();
	}
}
