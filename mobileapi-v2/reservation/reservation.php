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
		$fullname =  mysqli_real_escape_string($this->mysqli,$_REQUEST['fullName']);
		$resturent_id =  mysqli_real_escape_string($this->mysqli,$_REQUEST['rest_id']);
		$email =  mysqli_real_escape_string($this->mysqli,$_REQUEST['email']);
		$mobile =  mysqli_real_escape_string($this->mysqli,$_REQUEST['mobile_no']);
		$reservation_date =  mysqli_real_escape_string($this->mysqli,$_REQUEST['reservation_date']);
		$reservation_time =  mysqli_real_escape_string($this->mysqli,$_REQUEST['reservation_time']);
		$guest =  mysqli_real_escape_string($this->mysqli,$_REQUEST['guest']);
		$special_request =  mysqli_real_escape_string($this->mysqli,$_REQUEST['special_request']);


		$status=1;
		$userid=self::getUserID($email);
		
		if($userid === 0)  {
			//mysqli_real_escape_string($this->mysqli,$_REQUEST['user_id']);	
			$getRestUId = $this->mysqli->query("select user_id from restaurant where id=".$resturent_id);
			$rows = $getRestUId->fetch_assoc();
			$userid = $rows['user_id'];			
		}

		if ($userid!=0) 
		{
			$query ="INSERT INTO restaurant_reservation(restaurant_id,user_id,first_name,email,mobile,reservation_date,reservation_time,no_of_guest,special_request,status)values('".$resturent_id."','".$userid."','".$fullname."','".$email."','".$mobile."','".$reservation_date."','".$reservation_time."','".$guest."','".$special_request."','".$status."')";
			$r=$this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
			$reservation_id=mysqli_insert_id($this->mysqli); 
			if ($r)
			 {
	 	 		/*Send email to client*/			 	 		
	 			$send_email = self::ReservationConfirmationEmail($reservation_id);
	 			/*Email send end*/
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
		$query="SELECT user_id as userid from restaurant where id='".$resturent_id."'";
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

	private function ReservationConfirmationEmail($reservation_id)
	{	
 		/*$sub = "Thanks you for reservation with ChefOnline";
		$message = "Thank you for your reservation with new restaurant, a member of our team will contact you to confirm your reservation.";*/		
		$query = "SELECT re.*, r.restaurant_name FROM restaurant_reservation re INNER JOIN restaurant r ON re.restaurant_id = r.id where re.id = '".$reservation_id."'";
		$rows = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
		$datas = $rows->fetch_assoc();

		$ssquery = "SELECT config_value FROM restaurant_configuration where restaurant_id = '".$datas['restaurant_id']."' AND config_meta = 'reservation_email' and status = 1";
		$rrrr = $this->mysqli->query($ssquery) or die($this->mysqli->error.__LINE__);
		$num_data = $rrrr->fetch_assoc();

		//$to = array($datas['email'], $num_data['config_value']);/**/
		$to	= $datas['email'];/*added by Nazrul*/
		$msg = "";	
		$msg .= '<p style="font-size: 14px; font-family: arial; color: #000">Thank you for your reservation with <strong>'.$datas["restaurant_name"].'</strong>, a member of our team will contact you to confirm your reservation.</p>';

		$msg .= '
					<table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock">
					    <tbody class="mcnTextBlockOuter">
					        <tr>
					            <td valign="top" class="mcnTextBlockInner">
					                
					                <table align="left" border="0" cellpadding="0" cellspacing="0" width="600" class="mcnTextContentContainer">
					                    <tbody><tr>
					                        
					                        <td valign="top" class="mcnTextContent" style="padding-top:9px; padding-right: 18px; padding-bottom: 9px; padding-left: 18px;">
					                        
					                            <table border="0" cellpadding="0" style="width:100%; padding:10px;" width="100%">
						<tbody>
							
							<tr style="background:#CCCCCC">
								<td colspan="2" style="font-size: 16px; padding: 5px;" height="30px">RESERVATION DETAILS</td>
							</tr>
							<tr>
								<td style="padding-left:10px;"><strong>Name:</strong></td>
								<td style="padding-left:10px;">'.$datas["first_name"]." ".$datas["last_name"].'</td>
							</tr>
							<tr>
								<td style="padding-left:10px;"><strong>Email:</strong></td>
								<td style="padding-left:10px;">'.$datas["email"].'</td>
							</tr>
							<tr>
								<td style="padding-left:10px;"><strong>Phone Number:</strong></td>
								<td style="padding-left:10px;">'.$datas["mobile"].' </td>
							</tr>
							<tr>
								<td style="padding-left:10px;"><strong>Reservation Date:</strong></td>
								<td style="padding-left:10px;">'.$datas["reservation_date"].'</td>
							</tr>
							<tr>
								<td style="padding-left:10px;"><strong>Reservation Time:</strong></td>
								<td style="padding-left:10px;">'.$datas["reservation_time"].' </td>
							</tr>
							<tr>
								<td style="padding-left:10px;"><strong>Number of guest:</strong></td>
								<td style="padding-left:10px;">'.$datas["no_of_guest"].' </td>
							</tr>
							<tr>
								<td style="padding-left:10px;"><strong>Special Instruction:</strong></td>
								<td style="padding-left:10px;">'.$datas["special_request"].'</td>
							</tr>
						</tbody>
					</table>

					</td></tr></tbody></table>
					</td></tr></tbody></table>					
				';

		$message= $msg;
		$message.="<br>Best Regards<br> <b>Smart Restaurent Solution Team.</b>";
		
		$headers = 'From: ChefOnline info@chefonline.co.uk' . "\r\n" ;
		//$headers .='Reply-To: '. $to . "\r\n" ;
		$headers .='Reply-To: info@chefonline.co.uk'. "\r\n" ;
		//$headers .='Bcc: monitor@smartrestaurantsolutions.com' . "\r\n";
		// $headers .= "X-Priority: 3\r\n";
		$subject = "Reservation Confirmation";
		$headers .='X-Mailer: PHP/' . phpversion();
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";

		
		mail($to, $subject, $message, $headers);
		return 1;
	}

}

?>