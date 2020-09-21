<?php

class Trainee_Rating
{
	public $from_id;
	public $to_id;
	public $general;
	public $comment;
	public $game;

	public function __construct($_from_id, $_to_id, $_general, $_comment, $_game)
	{
		$this -> from_id = $_from_id;
		$this -> to_id = $_to_id;
		$this -> general = $_general;
		$this -> comment = $_comment;

		$this -> game = $_game;
	}

	public function save()
	{
		$db = getDB();
		$sql = $db -> prepare("DELETE FROM trainee_rating WHERE from_id=:from_id AND to_id=:to_id AND game=:game");
		$sql -> bindParam(':from_id', $this->from_id);
		$sql -> bindParam(':to_id', $this->to_id);
		$sql -> bindParam(':game', $this->game);
		$sql -> execute();

		$sql = $db -> prepare("INSERT INTO trainee_rating (from_id, to_id, comment, general, game) VALUES (:from_id, :to_id, :comment, :general, :game)");
		$sql -> bindParam(':from_id', $this->from_id);
		$sql -> bindParam(':to_id', $this->to_id);
		$sql -> bindParam(':comment', $this->comment);
		$sql -> bindParam(':general', $this->general);
		$sql -> bindParam(':game', $this->game);
		$sql -> execute();
	}

	public static function getWithUserID($user_id, $game)
	{
		$db = getDB();

		$sql = $db -> prepare("SELECT trainee_rating.* , user.name, user.avatar_url FROM trainee_rating LEFT JOIN user ON user.id = trainee_rating.from_id WHERE trainee_rating.to_id='$user_id'  AND trainee_rating.game='$game'");
		$sql -> execute();

		$coachRatings = $sql -> fetchAll(PDO::FETCH_ASSOC);

		// $sql -> debugDumpParams();
		return $coachRatings;
	}


}