<?php
class Setting extends REST
{
		private $mysqli ='';
	private $obj='';
	function __construct()
	{
		$this->obj = new DbConnect();
		$this->mysqli = $this->obj->db_connect();
		
	}	

	public function ResturentSchedule()
	{
			$resturent_id=mysqli_real_escape_string($this->mysqli,$_POST['rest_id']);
			$weekday=mysqli_real_escape_string($this->mysqli,$_POST['weekday']);
			$part = "";
			if(!empty($weekday) && $weekday > 0)
			{
				$part = " AND weekday = '".$weekday."' ";
			}

			$query="SELECT *FROM restaurant_schedule WHERE restaurant_id='".$resturent_id."' $part AND status='1' ORDER BY weekday";
			$this->mysqli->set_charset("utf8");
			$result = $this->mysqli->query($query);
			$days=array(1=>"Monday",2=>"Tuesday",3=>"Wednesday",4=>"Thursday",5=>"Friday",6=>"Saturday",7=>"Sunday");
			$arr["schedule"]=array();
			if($result->num_rows > 0)
			{			
				while($row = $result->fetch_assoc()) 
				{
					$Listdays=array();
					$Listdays['weekday_id']=$row['weekday'];
					$Listdays['weekday_name']= $days[$row['weekday']];
					$Listdays['opening_time']= $row['opening_time'];
					$Listdays['closing_time']= $row['closing_time'];
					
					array_push($arr["schedule"],$Listdays);
				}		
			}
			echo json_encode($arr,JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
	}

	/********************************************************************************
	|
	|		UPDATE LATITUDE AND LONGITUDE INFO FOR INDIVIDUAL RESTAURANT FROM API END
	|		Keywords are: 
	|
	*********************************************************************************/
	public function calculateLatLong(){

		$query = "SELECT id as rest_id, restaurant_name, address1, address2, town, city, postcode FROM restaurant";
		$result = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
		while($row = $result->fetch_assoc()){
			$name = $row['restaurant_name'];
			$address1 = str_replace('<br/>','',$row['address1']);
			$address2 = str_replace('<br/>','',$row['address2']);
			$town = $row['town'];
			$city = $row['city'];
			$postcode = $row['postcode'];

			$address = $name ." ". $address1." ".$address2." ".$town." ".$city." ".$postcode;     
			$address = str_replace(" ", "+", $address);
			$geocode = file_get_contents("http://maps.google.com/maps/api/geocode/json?address=$address&sensor=false&region=UK");        

            //$geocode=file_get_contents('http://maps.google.com/maps/api/geocode/json?address='.$address.'&sensor=false&region=UK');

            $output= json_decode($geocode);

             $lat = $output->results[0]->geometry->location->lat;
             $long = $output->results[0]->geometry->location->lng;
             $rest_id = $row['rest_id'];
             // if(!empty($lat) && !empty($long))
             // {             	
             	$update = "UPDATE restaurant SET latitude = '$lat', longitude ='$long' WHERE id = '$rest_id'";
             	$this->mysqli->query($update) or die($this->mysqli->error.__LINE__);

             	echo $address.'<br>Lat: '.$lat.'<br>Long: '.$long."\n";
             	
             // }
		}
	}


	/********************************************************************
	|
	|		UPDATE RESTAURANT LOGO FROM INDIVISUAL RESTAURANT BACKOFFICE
	|		Keywords are: rest_id, logo
	|
	*********************************************************************/
	public function updateRestaurantLogo()
	{
		$rest_id = $_POST['rest_id'];
		$logo = $_POST['logo'];

		$query = "UPDATE restaurant set logo = '".$logo."' WHERE id = '".$rest_id."'";

		$result = $this->mysqli->query($query);

		if($result)
			echo json_encode("true");
		else
			echo json_encode("false");

	}

	/**************************************************************************
	|
	|		ADD RESTAURANT SLIDER IMAGE FORM INDIVISUAL RESTAURANT BACKOFFICE
	|		Keywords are: rest_id, slider
	|
	***************************************************************************/
	public function updateRestaurantSliderImage()
	{
		$rest_id = $_POST['rest_id'];
		$slider_image_set = json_decode($_POST['slider']);
		$html = "";
		foreach ($slider_image_set as $key => $value) {
			$html .= $value.',';			
 		}

		$query = "UPDATE restaurant set slider_image = '".$html."' WHERE id = '".$rest_id."'";

		$result = $this->mysqli->query($query);

		if($result)
			echo json_encode("true");
		else
			echo json_encode("false");

	}

	/********************************************************************************
	|
	|		UPDATE "RESTAURANT RESERVATION INFO WHETHER THEY HAVE RESERVATION OR NOT" 
	|		FROM INDIVISUAL RESTAURANT BACKOFFICE
	|		Keywords are: rest_id, accept_reservation
	|
	*********************************************************************************/
	public function updateRestaurantReservationInfo()
	{
		$rest_id = $_POST['rest_id'];
		$accept_reservation = $_POST['accept_reservation'];

		$query = "UPDATE restaurant set accept_reservation = '".$accept_reservation."' WHERE id = '".$rest_id."'";

		$result = $this->mysqli->query($query);

		if($result)
			echo json_encode("true");
		else
			echo json_encode("false");
	}
	
	/******************************************************************************************
	|
	|		UPDATE "RESTAURANT DELIVERY AREA POSTCODE LIST WHETHER THEY HAVE RESERVATION OR NOT" 
	|		FROM INDIVISUAL RESTAURANT BACKOFFICE FOR BOTH LOCAL AND CENTRAL DB UPDATE
	|		Keywords are: rest_id, radius
	|
	*******************************************************************************************/
	public function updasteDeliveryPostcodeList()
	{
		$rest_id = $_POST['rest_id'];
		$radius = $_POST['radius'];
		$query = "SELECT id as rest_id, latitude, longitude, postcode FROM restaurant";
		$result = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
    	if($result->num_rows > 0)
    	{
    		$row = $result->fetch_assoc();
    		$lat = $row['latitude'];
    		$long = $row['longitude'];
    		$postcode = $row['postcode'];

    		$q = "SELECT `postcode`, SQRT(POW(69.1 * (`lat`-'".$lat."'),2)+ POW(69.1 * ('".$long."' - `lng`) * COS(`lat` / 57.3), 2)) AS distance FROM `uk_postcode` HAVING distance < '".$radius."' ORDER BY distance";
    		$res = $this->mysqli->query($q) or die($this->mysqli->error.__LINE__);
    		$postcode_list = "";
    		while ($data = $res->fetch_assoc()) {
    			$postcode_list .= $data['postcode'].",";
    		} 

    		$update_db = self::savePostcodeList($rest_id, $radius, $postcode_list);   		

    		if($update_db == 1)
    		{
    			echo json_encode($postcode_list);
    		}
    		else
    		{
    			echo json_encode("no data found");
    		}

    	}else
    	{
    		return null;
    	}
	}

	private function savePostcodeList($rest_id, $radius, $postcode_list)
	{
		$check = "select restaurant_id from restaurant_delivery_area WHERE restaurant_id = '".$rest_id."'";
		$rows = $this->mysqli->query($check) or die($this->mysqli->error.__LINE__);
		if($rows->num_rows > 0)
		{
			$query2 = "UPDATE restaurant_delivery_area SET delivery_radius = '".$radius."' AND postcode_list = '".$postcode_list."' WHERE restaurant_id = '".$rest_id."'";
			$status = $this->mysqli->query($query2) or die($this->mysqli->error.__LINE__);
			if($status)
				return 1;
			else
				return 0;
		}
		else
		{
			$query3 = "INSERT INTO restaurant_delivery_area (restaurant_id, delivery_radius, free_delivery_radius, postcode_list) values('".$rest_id."', '".$radius."', '".$radius."', '".$postcode_list."')";
			$status = $this->mysqli->query($query3) or die($this->mysqli->error.__LINE__);
			if($status)
				return 1;
			else
				return 0;
		}

	}




}
?>