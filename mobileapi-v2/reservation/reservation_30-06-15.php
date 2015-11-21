<?php
class Reservation extends REST
{
	private $mysqli ='';
	private $obj='';

	public function __construct()
	{
		$this->obj = new DbConnect();
		$this->mysqli = $this->obj->db_connect();
	}


public function PostReservation()
{
		$fullname =  mysqli_real_escape_string($this->mysqli,$_POST['fullName']);
		$resturent_id =  mysqli_real_escape_string($this->mysqli,$_POST['rest_id']);
		$email =  mysqli_real_escape_string($this->mysqli,$_POST['email']);
		$mobile =  mysqli_real_escape_string($this->mysqli,$_POST['mobile_no']);
		$reservation_date =  mysqli_real_escape_string($this->mysqli,$_POST['reservation_date']);
		$reservation_time =  mysqli_real_escape_string($this->mysqli,$_POST['reservation_time']);
		$guest =  mysqli_real_escape_string($this->mysqli,$_POST['guest']);
		$special_request =  mysqli_real_escape_string($this->mysqli,$_POST['special_request']);


		$status=1;
		$userid=self::getUserID($email);
		
		if($userid === 0)  {
			mysqli_real_escape_string($this->mysqli,$_POST['user_id']);
		}

		if ($userid!=0) 
		{
			$query ="INSERT INTO restaurant_reservation(restaurant_id,user_id,first_name,email,mobile,reservation_date,reservation_time,no_of_guest,special_request,status)values('".$resturent_id."','".$userid."','".$fullname."','".$email."','".$mobile."','".$reservation_date."','".$reservation_time."','".$guest."','".$special_request."','".$status."')";
			$r=$this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
			if ($r)
			 {
				$success = array('status' => "Success", "msg" => "Your reservation has been successfully completed!");
				echo json_encode($success);
			}
			else
			{
				$success = array('status' => "Failed", "msg" => "Sorry! reservation failed");
				echo json_encode($success);
			}
			
		}
		else  {
			$success = array('status' => "Failed", "msg" => "Sorry! reservation failed");
			echo json_encode($success);
		}
		
	   	$this->obj->ConnectionClose();
}
private function getUserID($email)
	{
		$query="SELECT id as userid from user where email='".$email."'";
		$result = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
		if($result->num_rows > 0) 
		{
			$row = $result->fetch_assoc();
			return  $row['userid'];
		}
		else
		{
			 return 0;
		}
	}
	
	//this function is for getting user id from restaurant tbl
	private function getRestaurantUserID($resturent_id)
	{
		$query="SELECT id as userid from restaurant where id='".$resturent_id."'";
		$result = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
		if($result->num_rows > 0) 
		{
			$row = $result->fetch_assoc();
			return  $row['userid'];
		}
		else
		{
			 return 0;
		}
	}

	public function getReservation()
	{

		$rest_id=$_REQUEST['rest_id'];
		$query="SELECT * FROM restaurant_reservation WHERE restaurant_id='$rest_id' ORDER BY id DESC"; 
		$result = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
		$arr['app'] = array();$arrr = array();
		if($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$arrr[]=$row;
				array_push($arr['app'], $arrr);
			}
		}
		$json_response = json_encode($arr);
		echo $json_response;
	}

	public function updateReservationStatus()
	{		
		$id=$_REQUEST['id'];
		$status=$_REQUEST['status'];
		$rest_id=$_REQUEST['rest_id'];
		$query="UPDATE restaurant_reservation SET status='$status' WHERE id='$id' && restaurant_id='$rest_id'";
		$result=$this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
		if ($result)
			 {
				$success = array('status' => "Success", "msg" => "Your reservation has been successfully completed!");
				echo json_encode($success);
			}
			else
			{
				$success = array('status' => "Failed", "msg" => "Sorry! reservation failed");
				echo json_encode($success);
			}
	}

}

?>