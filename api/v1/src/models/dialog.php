<?php

class Dialog
{

	public $id;
	public $holder_id;
	public $rec_id;
	public $type;
	public $state;
	public $reference_id;
	public $timestamp;
	public $game;

	public function __construct($_holder_id, $_type, $_reference_id, $_rec_id, $_game)
	{
		$this -> holder_id = $_holder_id;
		$this -> type = $_type;
		$this -> reference_id = $_reference_id;
		$this -> rec_id = $_rec_id;
		$this -> timestamp = time();

		$this -> game = $_game;
	}

	public function save()
	{		
		$db = getDB();
		if($this->type == 1)
		{		
			$sql = $db -> prepare("SELECT * FROM dialog WHERE ((holder_id=:holder_id AND rec_id=:rec_id) OR (holder_id=:rec_id AND rec_id=:holder_id)) AND type=:type AND game=:game");

			$sql -> bindParam(':holder_id', $this->holder_id);
			$sql -> bindParam(':rec_id', $this->rec_id);
			$sql -> bindParam(':type', $this->type);
			$sql -> bindParam(':game', $this->game);

			$sql -> execute();

			$dialogs = $sql -> fetchAll(PDO::FETCH_ASSOC);

			if(count($dialogs) > 0)
			{
				return $dialogs[0];
			}
		}
		else if($this->type == 2)
		{
			$sql = $db -> prepare("SELECT * FROM dialog WHERE holder_id=:holder_id AND rec_id=:rec_id AND type=:type AND reference_id=:reference_id AND game=:game");

			$sql -> bindParam(':holder_id', $this->holder_id);
			$sql -> bindParam(':rec_id', $this->rec_id);
			$sql -> bindParam(':type', $this->type);
			$sql -> bindParam(':reference_id', $this->reference_id);
			$sql -> bindParam(':game', $this->game);

			$sql -> execute();

			$dialogs = $sql -> fetchAll(PDO::FETCH_ASSOC);

			if(count($dialogs) > 0)
			{
				return $dialogs[0];
			}
		}


		$sql = $db -> prepare("INSERT INTO dialog (holder_id, type, reference_id, rec_id, timestamp, game) VALUES (:holder_id, :type, :reference_id, :rec_id, :timestamp, :game)");

		$sql -> bindParam(':holder_id', $this->holder_id);
		$sql -> bindParam(':type', $this->type);
		// $sql -> bindParam(':state', $this->state);
		$sql -> bindParam(':reference_id', $this->reference_id);
		$sql -> bindParam(':rec_id', $this->rec_id);
		$sql -> bindParam(':timestamp', $this->timestamp);
		$sql -> bindParam(':game', $this->game);

		$sql -> execute();
		
		$sql = $db -> prepare("SELECT * FROM dialog WHERE holder_id=:holder_id AND type=:type AND reference_id=:reference_id AND rec_id=:rec_id AND game=:game");

		$sql -> bindParam(':holder_id', $this->holder_id);
		$sql -> bindParam(':reference_id', $this->reference_id);
		$sql -> bindParam(':rec_id', $this->rec_id);
		$sql -> bindParam(':type', $this->type);
		$sql -> bindParam(':game', $this->game);
		$sql -> execute();

		$dialogs = $sql -> fetchAll(PDO::FETCH_ASSOC);

		return $dialogs[0];
	}

	public static function sendMessage($dialog_id, $user_id, $message, $type)
	{		
		$time = time();
		$db = getDB();

		$sql = $db -> prepare("INSERT INTO message (dialog_id, user_id, message, type, timestamp) VALUES ('$dialog_id', '$user_id', :message, '$type', '$time')");

		$sql -> bindParam(':message', $message);
		$sql -> execute();

		$sql = $db -> prepare("SELECT * FROM message WHERE dialog_id='$dialog_id' ORDER BY id DESC LIMIT 1");
		$sql -> execute();

		$messages = $sql -> fetchAll(PDO::FETCH_ASSOC);
		
		if($type != 10)
		{
			$sql = $db -> prepare("UPDATE dialog SET timestamp='$time' WHERE id='$dialog_id'");
			$sql -> execute();
		}
		else
		{
			$sql = $db -> prepare("UPDATE tbl_group SET timestamp='$time' WHERE id='$dialog_id'");
			$sql -> execute();			
		}

		return $messages[0];
	}

	public static function getDialogWithID($dialog_id)
	{		
		$db = getDB();

		$sql = $db -> prepare("SELECT * FROM dialog WHERE id='$dialog_id'");
		$sql -> execute();

		$dialogs = $sql -> fetchAll(PDO::FETCH_ASSOC);

		return $dialogs[0];
	}

	public static function getDialogWithUserID($user_id, $game)
	{		
		$db = getDB();

		$sql = $db -> prepare("SELECT * FROM dialog WHERE (rec_id='$user_id' OR holder_id='$user_id') AND game='$game' ORDER BY timestamp DESC");
		$sql -> execute();

		$dialogs = $sql -> fetchAll(PDO::FETCH_ASSOC);

		return $dialogs;
	}

	public static function getMessages($dialog_id, $message_id)
	{		
		$db = getDB();

		$sql = $db -> prepare("SELECT * FROM message WHERE dialog_id='$dialog_id' AND id > $message_id");


		$sql -> execute();
		// $sql -> debugDumpParams();
		$dialogs = $sql -> fetchAll(PDO::FETCH_ASSOC);

		return $dialogs;
	}

	public static function updateDialog($dialog_id, $state, $reference_id, $inviter_id, $block_id)
	{
		$db = getDB();

		$sql = $db -> prepare("UPDATE dialog SET state='$state', reference_id='$reference_id', inviter_id='$inviter_id', block_id='$block_id' WHERE id='$dialog_id'");

		$sql -> execute();

		// $sql -> debugDumpParams();
	}

	public static function getLastTimestamp($user_id, $game)
	{
		$db = getDB();

		$sql = $db -> prepare("SELECT MAX(timestamp) AS timestamp FROM dialog WHERE game='$game' AND ( holder_id='$user_id' OR rec_id='$user_id')");
		$sql -> execute();

		$timestamp = $sql -> fetchAll(PDO::FETCH_ASSOC);

		return ($timestamp[0]['timestamp']);
	}

}