<?php
/**
* 
*/
class ManageUser extends REST
{
	private $mysqli ='';
	private $obj='';
	
	function __construct()
	{
		$this->obj = new DbConnect();
		$this->mysqli = $this->obj->db_connect();
	}	
	public function Login()
	{
		header('Access-Control-Allow-Origin: *');
		header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
		header('Content-Type: application/json; charset=utf-8');
		$username=  mysqli_real_escape_string($this->mysqli,$_REQUEST['username']);
		$pass= sha1(mysqli_real_escape_string($this->mysqli,$_REQUEST['password']));
		$status=1;
		$query="SELECT a.id as userid,a.user_group_id,a.email,b.first_name,b.sur_name,b.last_name,b.postcode,b.user_id,b.mobile_no,b.address1, b.address2, b.date_of_anniversery, b.date_of_birth, b.telephone_no, b.town FROM user a, user_profile b WHERE a.email='".$username."'AND a.password='".$pass."' AND a.id=b.user_id AND a.status='".$status."'";
		$result = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
		if($result->num_rows > 0) 
		{
			$row = $result->fetch_assoc();
			$success = array('status' => "Success","UserDetails"=>array("userid"=>$row['userid'],"user_group_id"=>$row['user_group_id'],"email"=>$row['email'],"first_name"=>$row['first_name'],"sur_name"=>$row['sur_name'],"last_name"=>$row['last_name'],"postcode"=>$row['postcode'],"mobile_no"=>$row['mobile_no'],"address1"=>$row['address1'],"address2"=>$row['address2'],"date_of_birth"=>$row['date_of_birth'],"date_of_anniversery"=>$row['date_of_anniversery'],"telephone_no"=>$row['telephone_no'],"town"=>$row['town']));
			echo json_encode($success);
		}
		else
		{
			$failed = array('status' => "Failed","UserDetails"=>array('Message'=>'User does not exist'));
			echo json_encode($failed);
		}
		$this->obj->ConnectionClose();
	}
	private function checkUser($email)
	{
		$query="SELECT *from user where email='".$email."'";
		$result = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
		if($result->num_rows > 0) 
		{
			 return 1;
		}
		else
		{
			 return 0;
		}
	}
	public function RegisterUser()
	{

		$fname =  mysqli_real_escape_string($this->mysqli,$_REQUEST['fname']);
		$lname =  mysqli_real_escape_string($this->mysqli,$_REQUEST['lname']);
		$email =  mysqli_real_escape_string($this->mysqli,$_REQUEST['email']);
		$mobile =  mysqli_real_escape_string($this->mysqli,$_REQUEST['mobile_no']);
		$telno =  mysqli_real_escape_string($this->mysqli,$_REQUEST['telephone_no']);
		$dob =  strtotime(mysqli_real_escape_string($this->mysqli,$_REQUEST['dob_date']));
		$dateOfAniversary = strtotime(mysqli_real_escape_string($this->mysqli,$_REQUEST['doa']));
		$postcode =  mysqli_real_escape_string($this->mysqli,$_REQUEST['postcode']);
		$address1 =  mysqli_real_escape_string($this->mysqli,$_REQUEST['address1']);
		$address2 =  mysqli_real_escape_string($this->mysqli,$_REQUEST['address2']);
		$city =  mysqli_real_escape_string($this->mysqli,$_REQUEST['city']);
		$country =  mysqli_real_escape_string($this->mysqli,$_REQUEST['country']);
		$password = sha1(mysqli_real_escape_string($this->mysqli,$_REQUEST['password']));
		$status=1;
		$created_at=date('y-m-d');
		$user_group_id=0;
		if (self::checkUser($email)!=0) 
		{
			$success = array('status' => "Failed", "msg" => "You are already registered in our system, So you can login using existing email and password");
			echo json_encode($success);
		}
		else
		{
			$query ="INSERT INTO user(user_group_id,email,password,status,created_at)values('".$user_group_id."','".$email."','".$password."','".$status."','".$created_at."')";
			$r=$this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
		if ($r)
		 {
		 	$user_id=mysqli_insert_id($this->mysqli); 
		 	$query2 ="INSERT INTO user_profile(user_id,first_name,last_name,address1,address2,postcode,date_of_birth,date_of_anniversery,mobile_no,telephone_no,town,region)values('".$user_id."','".$fname."','".$lname."','".$address1."','".$address2."','".$postcode."','".$dob."','".$dateOfAniversary."','".$mobile."','".$telno."','".$city."','".$country."')";
			$rst=$this->mysqli->query($query2) or die($this->mysqli->error.__LINE__);
			if ($rst) {
			$data = array();
			$data['status'] = "Success";
			$data['msg'] = "You have been successfully registered.";
			$data['UserDetails'] = self::ViewProfile($user_id);

			echo json_encode($data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
			}
			
	   	}
	   }
	   $this->obj->ConnectionClose();
	}


	public function ForgetPassword()
	{
		$email =  mysqli_real_escape_string($this->mysqli,$_REQUEST['email']);
		
		$query="select *from user where email='".$email."'";
		$result = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
		$arr['app'] = array();
		if($result->num_rows > 0)
		 {
		 	$row = $result->fetch_assoc();
		 	$checkStatus = self::SendEmail($email,$row['id']);
			if($checkStatus == 1)
			{
				$arr['app'] = array('status' => "Success", "msg" => "Password successfully updated. Please check your mailbox!");
				echo json_encode($arr);
			}
			else
			{
				$arr['app'] = array('status' => "Failed", "msg" => "Updating password failed. Try agian.");
				echo json_encode($arr);
			}
		 }
		 else
		 {
			$arr['app'] = array('status' => "Failed", "msg" => "Your email address does not exist.");			
			echo json_encode($arr);
		 }
		// $this->obj->ConnectionClose();
	}

	private function SendEmail($to,$rid)
	{

		$newpassword=self::randomPassword();
		
		$headers = 'From: ChefOnline info@chefonline.co.uk' . "\r\n" ;
		$headers .='Reply-To: '. $to . "\r\n" ;
		// $headers .= "X-Priority: 3\r\n";
		$subject = "Forget password reminder";
		$headers .='X-Mailer: PHP/' . phpversion();
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";

		$message="Dear Concern, your previous password has been reset and new password is: $newpassword";
		$message.="<br>Best Regards<br> Smart Resturent Solution Team.";
		mail($to, $subject, $message, $headers);
		$updateStatus = self::UpdatePassword($rid,$newpassword);
		if($updateStatus == 1)
		{
			$this->obj->ConnectionClose();
			return 1;
		}else{
			return 0;
		}		
	}
	private function UpdatePassword($rid,$pass)
	{
		$query="UPDATE user SET password='".sha1($pass)."' WHERE id='".$rid."'";
		$r=$this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
		if ($r) {
			return 1;
		}else
		{
			return 0;
		}
	}
	private function randomPassword() 
	{
	    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
	    $pass = array(); //remember to declare $pass as an array
	    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
	    for ($i = 0; $i < 8; $i++) {
	        $n = rand(0, $alphaLength);
	        $pass[] = $alphabet[$n];
	    }
	    return implode($pass); //turn the array into a string
	}
	public function ResetPassword()
	{
		$email =  mysqli_real_escape_string($this->mysqli,$_REQUEST['email']);
		$previouspassword =  mysqli_real_escape_string($this->mysqli,$_REQUEST['previouspassword']);
		$pass =  mysqli_real_escape_string($this->mysqli,$_REQUEST['newpassword']);
		
		$query="select id as userid, email, password FROM user where email='".$email."'AND password='".sha1($previouspassword)."'";
		$result = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
		
		if($result->num_rows > 0)
		 {
		 	$row = $result->fetch_assoc();
		 	$getStatus = self::UpdatePassword($row['userid'],$pass);
		 	if($getStatus == 1)
		 	{
		 		$success = array('status' => "Success", "msg" => "Your password reset successfully.");
				echo json_encode($success);
		 	}
		 	else
		 	{
		 		$success = array('status' => "Failed", "msg" => "Failed to reset password.");
				echo json_encode($success);
		 	}
		 }
		 else{
		 	$success = array('status' => "Failed", "msg" => "Username or previous password does not match!");
			echo json_encode($success);
		 }
	}
	private function ViewProfile($userid)
	{
		$query="select  u.id as userid,u.email, p.first_name,p.last_name,p.address1,p.address2,p.mobile_no,p.telephone_no,p.date_of_birth,p.date_of_anniversery,p.postcode,p.town,p.region FROM user u, user_profile p where u.id='".$userid."' AND p.user_id='".$userid."'";
		$result = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
		$arr = array();
		if($result->num_rows > 0)
		 {
			while($row = $result->fetch_assoc()) 
			{
				$arr['userid']=$row['userid'];
				$arr['email'] = $row['email'];
				$arr['first_name'] = $row['first_name']; 
				$arr['last_name'] = $row['last_name']; 
				$arr['address1'] = $row['address1']; 
				$arr['address2'] = $row['address2']; 
				$arr['mobile_no'] = $row['mobile_no']; 
				$arr['telephone_no'] = $row['telephone_no']; 
				$arr['date_of_birth'] = $row['date_of_birth']; 
				$arr['date_of_anniversery'] = $row['date_of_anniversery']; 
				$arr['postcode'] = $row['postcode']; 
				$arr['town'] = $row['town']; 
				$arr['region'] = $row['region']; 
			}
		}		
		return $arr;
	}
}

?>