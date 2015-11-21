<?php
/**
* N@zrul Islam
*/
class UserProfile extends REST
{
	private $mysqli ='';
	private $obj='';

	function __construct()
	{
		$this->obj = new DbConnect();
		$this->mysqli = $this->obj->db_connect();
	}	

	public function profile()
	{
		$userid=mysqli_real_escape_string($this->mysqli,$_POST['userid']);
		$query="select  u.id as userid,u.email, p.first_name,p.last_name,p.address1,p.address2,p.mobile_no,p.telephone_no,p.date_of_birth,p.date_of_anniversery,p.postcode,p.town,p.region as country FROM user u, user_profile p where u.id='".$userid."' AND p.user_id='".$userid."'";
		$result = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
		$arr = array();
		if($result->num_rows > 0)
		 {
			while($row = $result->fetch_assoc()) 
			{
				$arr[]=$row;
			}
			$json_response = json_encode($arr, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
			echo $json_response;
		}
		else
		{
			$callback = array('status' => "Failed", 'msg' => "Profile informaiton not updated.");				
			echo json_encode($callback);
		}
		
	}

	private function getProfile_details($userid)
	{
		$userid=mysqli_real_escape_string($this->mysqli,$userid);
		$query="select  u.id as userid,u.email, p.first_name,p.last_name,p.address1,p.address2,p.mobile_no,p.telephone_no,p.date_of_birth,p.date_of_anniversery,p.postcode,p.town,p.region FROM user u, user_profile p where u.id='".$userid."' AND p.user_id='".$userid."'";
		$result = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
		$arr = array();
		if($result->num_rows > 0)
		 {
			while($row = $result->fetch_assoc()) 
			{
				$arr[]=$row;
			}
		}		
		$json_response = json_encode($arr);
		return $json_response;
	}

	public function updateProfileInfo()
	{		
		$userid =  mysqli_real_escape_string($this->mysqli,$_REQUEST['userid']);
		if(!empty($userid))
		{
			$arr = array();
			$fname =  mysqli_real_escape_string($this->mysqli,$_REQUEST['fname']);
			$lname =  mysqli_real_escape_string($this->mysqli,$_REQUEST['lname']);
			$email =  mysqli_real_escape_string($this->mysqli,$_REQUEST['email']);
			$mobile =  mysqli_real_escape_string($this->mysqli,$_REQUEST['mobile_no']);
			$telno =  mysqli_real_escape_string($this->mysqli,$_REQUEST['telephone_no']);		
			$postcode =  mysqli_real_escape_string($this->mysqli,$_REQUEST['postcode']);
			$address1 =  mysqli_real_escape_string($this->mysqli,$_REQUEST['address1']);
			$address2 =  mysqli_real_escape_string($this->mysqli,$_REQUEST['address2']);
			$city =  mysqli_real_escape_string($this->mysqli,$_REQUEST['city']);
			$country =  mysqli_real_escape_string($this->mysqli,$_REQUEST['country']);
			
				$dob =  mysqli_real_escape_string($this->mysqli,$_REQUEST['dob_date']);
				$dateOfAniversary =  mysqli_real_escape_string($this->mysqli,$_REQUEST['doa']);

			$query = "UPDATE user_profile a INNER JOIN user b ON a.user_id = b.id SET a.first_name = '$fname ', a.last_name = '$lname', b.email = '$email', a.address1 = '$address1', a.address2 = '$address2', a.mobile_no = '$mobile', a.telephone_no = '$telno', a.postcode = '$postcode', a.town = '$city', a.region = '$country', a.date_of_birth = '$dob', a.date_of_anniversery = '$dateOfAniversary' WHERE a.user_id = '".$userid."'";
			$result = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
			if($result)
			{
				$success = self::ViewProfile($userid);
				echo json_encode($success);
			}
			else
			{
				$callback = array('status' => "Failed", 'msg' => "Profile informaiton not updated.");				
				echo json_encode($callback);
			}
		}
		else
		{			
		 	$callback = array('status' => "Failed", "msg" => "Profile information not found!");		 	
			echo json_encode($callback);
		}


	}

	private function ViewProfile($userid)
	{
		$query="select  u.id as userid,u.email, p.first_name,p.last_name,p.address1,p.address2,p.mobile_no,p.telephone_no,p.date_of_birth,p.date_of_anniversery,p.postcode,p.town,p.region as country FROM user u, user_profile p where u.id='".$userid."' AND p.user_id='".$userid."'";
		$result = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
		if($result->num_rows > 0)
		 {
			while($row = $result->fetch_assoc()) 
			{
				$arr['UserDetails'] =$row;
				$arr['status'] = 'Success';
				$arr['msg'] = 'Profile information successfully updated.';
			}
		}
		
		return $arr;
	}
}
?>