<?php

class Coach_Rating
{
	public $from_id;
	public $to_id;
	public $comment;
	public $competency;
	public $communication;
	public $flexibility;
	public $attitude;
	public $game;

	public function __construct($_from_id, $_to_id, $_competency, $_communication, $_flexibility, $_attitude, $_comment, $_game)
	{
		$this -> from_id = $_from_id;
		$this -> to_id = $_to_id;
		$this -> competency = $_competency;
		$this -> communication = $_communication;
		$this -> flexibility = $_flexibility;
		$this -> attitude = $_attitude;
		$this -> comment = $_comment;

		$this -> game = $_game;
	}

	public function save()
	{
		$db = getDB();
		$sql = $db -> prepare("DELETE FROM coach_rating WHERE from_id=:from_id AND to_id=:to_id AND game=:game");
		$sql -> bindParam(':from_id', $this->from_id);
		$sql -> bindParam(':to_id', $this->to_id);
		$sql -> bindParam(':game', $this->game);

		$sql -> execute();

		$sql = $db -> prepare("INSERT INTO coach_rating (from_id, to_id, comment, competency, communication, flexibility, attitude, game) VALUES (:from_id, :to_id, :comment, :competency, :communication, :flexibility, :attitude, :game)");
		$sql -> bindParam(':from_id', $this->from_id);
		$sql -> bindParam(':to_id', $this->to_id);
		$sql -> bindParam(':comment', $this->comment);
		$sql -> bindParam(':competency', $this->competency);
		$sql -> bindParam(':communication', $this->communication);
		$sql -> bindParam(':flexibility', $this->flexibility);
		$sql -> bindParam(':attitude', $this->attitude);
		$sql -> bindParam(':game', $this->game);
		
		$sql -> execute();
	}

	public static function getWithUserID($id, $game)
	{
		$db = getDB();
		$sql = $db -> prepare("SELECT coach_rating.*, user.name, user.avatar_url FROM coach_rating LEFT JOIN user ON coach_rating.from_id=user.id WHERE to_id='$id' AND game='$game'");
		$sql -> execute();
		$rates = $sql -> fetchAll(PDO::FETCH_ASSOC);

		return $rates;
	}

}