<?php
class DbConnect{
	// private  $DB_HOST = 'chefonline.co.uk';
	// private  $DB_USER = 'srs_mobile_api';
	// private  $DB_PASS = 'yDei59@8';
	// private  $DB_NAME = 'smart__mobile__api';
	private  $DB_HOST = 'smartrestaurantsolutions.com';
	private  $DB_USER = 'smartr5live_test';
	private  $DB_PASS = '$Ry8q4y3';
	// private  $DB_NAME = 'smartr5_database';  //this is main connection db. just using another one for testing purpose
	//private  $DB_NAME = 'smartr5_database';
	private  $DB_NAME = 'smartr5_live_test';
	public $mysqli =''; 
	function db_connect()
	{
		$this->mysqli = new mysqli($this->DB_HOST, $this->DB_USER, $this->DB_PASS, $this->DB_NAME);
		return $this->mysqli;
	}

	public function ConnectionClose()
	{
		mysqli_close($this->mysqli);
	}
}


?>