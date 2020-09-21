<?php

	function getDB()
	{
		
		$dbhost = "localhost";
		$dbuser = "root";


		if(environment == 'dev')
		{			
			$dbpass = "root";
			$dbname	= "getgood";
		}
		else if(environment == 'qa')
		{
			$dbpass = "";
			$dbname	= "getgood_dev";
		}	
		else if(environment == 'prod')
		{
			$dbpass = "";
			$dbname	= "getgood";
		}


		$dsn = "mysql:dbname=".$dbname.";host:127.0.0.1";
		$dbConnection = new PDO($dsn, $dbuser, $dbpass);
		return $dbConnection;
	}

	function generateRandomString($length = 20) {
	    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $charactersLength = strlen($characters);
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, $charactersLength - 1)];
	    }
	    return $randomString;
	}
?>