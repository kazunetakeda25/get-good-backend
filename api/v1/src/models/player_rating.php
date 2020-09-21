<?php

class Player_Rating
{
	public $from_id;
	public $to_id;
	public $leader;
	public $cooperative;
	public $good_communication;
	public $sportsmanship;
	public $mvp;
	public $flex_player;
	public $good_hero_competency;
	public $griefing;
	public $spam;
	public $no_communication;
	public $un_cooperative;
	public $trickling_in;
	public $poor_hero_competency;
	public $bad_ultimate_usage;
	public $overextending;
	public $comment;

	public $game;

	public function __construct($_from_id
										,$_to_id
										,$_leader
										,$_cooperative
										,$_good_communication
										,$_sportsmanship
										,$_mvp
										,$_flex_player
										,$_good_hero_competency
										,$_good_ultimate_usage
										,$_abusive_chat
										,$_griefing
										,$_spam
										,$_no_communication
										,$_un_cooperative
										,$_trickling_in
										,$_poor_hero_competency
										,$_bad_ultimate_usage
										,$_overextending
										,$_comment
										,$_game)
	{
		$this -> from_id = $_from_id;
		$this -> to_id = $_to_id;
		$this -> leader = $_leader;
		$this -> cooperative = $_cooperative;
		$this -> good_communication = $_good_communication;
		$this -> sportsmanship = $_sportsmanship;
		$this -> mvp = $_mvp;
		$this -> flex_player = $_flex_player;
		$this -> good_hero_competency = $_good_hero_competency;
		$this -> good_ultimate_usage = $_good_ultimate_usage;
		$this -> abusive_chat = $_abusive_chat;
		$this -> griefing = $_griefing;
		$this -> spam = $_spam;
		$this -> no_communication = $_no_communication;
		$this -> un_cooperative = $_un_cooperative;
		$this -> trickling_in = $_trickling_in;
		$this -> poor_hero_competency = $_poor_hero_competency;
		$this -> bad_ultimate_usage = $_bad_ultimate_usage;
		$this -> overextending = $_overextending;
		$this -> comment = $_comment;
		$this -> game = $_game;
	}

	public function save()
	{
		$db = getDB();
		$sql = $db -> prepare("DELETE FROM player_rating WHERE from_id=:from_id AND to_id=:to_id AND game=:game");
		$sql -> bindParam(':from_id', $this->from_id);
		$sql -> bindParam(':to_id', $this->to_id);
		$sql -> bindParam(':game', $this->game);
		$sql -> execute();

		$sql = $db -> prepare("INSERT INTO player_rating (from_id, to_id, leader, cooperative, good_communication, sportsmanship, mvp, flex_player, good_hero_competency, good_ultimate_usage, abusive_chat, griefing, spam, no_communication, un_cooperative, trickling_in, poor_hero_competency, bad_ultimate_usage, overextending, comment, game) VALUES (:from_id, :to_id, :leader, :cooperative, :good_communication, :sportsmanship, :mvp, :flex_player, :good_hero_competency,:good_ultimate_usage ,:abusive_chat, :griefing, :spam, :no_communication, :un_cooperative, :trickling_in, :poor_hero_competency, :bad_ultimate_usage, :overextending, :comment, :game)");

		$sql -> bindParam(':from_id', $this->from_id);
		$sql -> bindParam(':to_id', $this->to_id);
		$sql -> bindParam(':leader', $this->leader);
		$sql -> bindParam(':cooperative', $this->cooperative);
		$sql -> bindParam(':good_communication', $this->good_communication);
		$sql -> bindParam(':sportsmanship', $this->sportsmanship);
		$sql -> bindParam(':mvp', $this->mvp);
		$sql -> bindParam(':flex_player', $this->flex_player);
		$sql -> bindParam(':good_hero_competency', $this->good_hero_competency);
		$sql -> bindParam(':good_ultimate_usage', $this->good_ultimate_usage);
		$sql -> bindParam(':abusive_chat', $this->abusive_chat);
		$sql -> bindParam(':griefing', $this->griefing);
		$sql -> bindParam(':spam', $this->spam);
		$sql -> bindParam(':no_communication', $this->no_communication);
		$sql -> bindParam(':un_cooperative', $this->un_cooperative);
		$sql -> bindParam(':trickling_in', $this->trickling_in);
		$sql -> bindParam(':poor_hero_competency', $this->poor_hero_competency);
		$sql -> bindParam(':bad_ultimate_usage', $this->bad_ultimate_usage);
		$sql -> bindParam(':overextending', $this->overextending);
		$sql -> bindParam(':comment', $this->comment);
		$sql -> bindParam(':game', $this->game);
		
		$sql -> execute();

		User::updateRating($this -> to_id);
	}

	public static function getWithUserID($id, $game)
	{
		$db = getDB();

		$sql = $db -> prepare("SELECT player_rating.*, user.name FROM player_rating LEFT JOIN user ON user.id = player_rating.from_id WHERE player_rating.to_id='$id' AND player_rating.game='$game'");

		$sql -> execute();
		$rates = $sql -> fetchAll(PDO::FETCH_ASSOC);

		return $rates;
	}
}