<?php

class Verify
{
	public $code;
	public $email;
	public function __construct($_email)
	{
		$this -> code = generateRandomString();
		$this -> email = $_email;

		$this -> save();
	}

	public function save()
	{
		$db = getDB();
		$sql = $db -> prepare("DELETE FROM request WHERE email=:email");
		$sql -> bindParam(':email', $this->email);
		$sql -> execute();

		$sql = $db -> prepare("INSERT INTO request (email, code) VALUES (:email, :code)");
		$sql -> bindParam(':code', $this->code);
		$sql -> bindParam(':email', $this->email);
		$sql -> execute();
	}

	public static function verify($code)
	{
		$db = getDB();
		$sql = $db -> prepare("SELECT * FROM request WHERE code='$code'");
		$sql -> execute();

		$verifys = $sql -> fetchAll(PDO::FETCH_ASSOC);

		if(count($verifys) > 0)
		{
			User::verifyEmail($verifys[0]['email']);
			$sql = $db -> prepare("DELETE FROM request WHERE code='$code'");
			$sql -> execute();

			return 1;
		}
		return 0;
	}

}