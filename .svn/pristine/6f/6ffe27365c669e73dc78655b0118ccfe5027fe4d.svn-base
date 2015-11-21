<?php
class DbConnect{
	private  $DB_HOST = 'chefonline.co.uk';
	private  $DB_USER = 'srs_mobile_api';
	private  $DB_PASS = 'yDei59@8';
	private  $DB_NAME = 'smart__mobile__api';
	// private  $DB_USER = 'root';
	// private  $DB_PASS = '';
	// private  $DB_NAME = 'smartr5_database';
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