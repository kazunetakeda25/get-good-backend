<?php

class Forgot
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
		$sql = $db -> prepare("DELETE FROM request WHERE email=:email AND type=1");
		$sql -> bindParam(':email', $this->email);
		$sql -> execute();

		$sql = $db -> prepare("INSERT INTO request (email, code, type) VALUES (:email, :code, 1)");
		$sql -> bindParam(':code', $this->code);
		$sql -> bindParam(':email', $this->email);
		$sql -> execute();
	}

	public static function reset($code, $password)
	{
		$db = getDB();
		$sql = $db -> prepare("SELECT * FROM request WHERE code='$code' AND type=1");
		$sql -> execute();

		$verifys = $sql -> fetchAll(PDO::FETCH_ASSOC);

		if(count($verifys) > 0)
		{
			User::resetPassword($verifys[0]['email'], $password);
			$sql = $db -> prepare("DELETE FROM request WHERE code='$code'");
			$sql -> execute();

			return 1;
		}
		return 0;
	}

}