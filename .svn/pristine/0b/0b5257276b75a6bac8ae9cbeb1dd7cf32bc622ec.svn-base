<?php
/**
* 
*/
require 'Rest.inc.php';
class Config extends REST
{
	private  $DB_HOST = '127.0.0.1';
	private  $DB_USER = 'srs_mobile';
	private  $DB_PASS = 'yDei59@8';
	private  $DB_NAME = 'smartr5__mobile';
	private $mysqli ='';
	function __construct()
	{
		$this->mysqli = new mysqli($this->DB_HOST, $this->DB_USER, $this->DB_PASS, $this->DB_NAME);
	}
/*	public function TiggerAPI(){
		if (empty($_REQUEST['x'])) {
			$_REQUEST['x']="redirectUrl";
		}
			$func = strtolower(trim(str_replace("/","",$_REQUEST['x'])));
			if((int)method_exists($this,$func) > 0)
				$this->$func();
			else
				$this->response('',404); 
		}*/
	public function redirectUrl()
	{

	}
	public function login()
	{
		$username=  mysqli_real_escape_string($this->mysqli,$_GET['username']);
		$pass=  mysqli_real_escape_string($this->mysqli,$_GET['password']);
		
		$query="select *from users  where username='".$username."' AND password='".$pass."'";
		$result = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
		if($result->num_rows > 0) 
		{
			session_start();
			$_SESSION['username']=$username;
			header("location:main/");
		}
		else{
		echo "<div style='margin:5% 40%;border:1px red solid;background-color:#999;text-align:center'>Invalid username or password</div>";
		}
}
	public function AppUsersListDataGrid()
	{
		$query="select *from user order by id DESC";
		$result = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
		$arr = array();
		if($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$a=array();
				$a['id'] = $row['id'];
				$a['app_id'] = $row['app_id'];
				$a['country'] = $row['country'];
				$a['occupation'] = $row['occupation'];	
				$a['email'] = $row['email'];	
				if ($row['app_type']==1) {
					$a['app_type'] = "Skilled Migration";
				}
				if ($row['app_type']==2) {
					$a['app_type'] = "Business Migration";
				}
				array_push($arr,$a);
			}
		}
		$json_response = json_encode($arr);
		echo $json_response;
	}
		public function getUsrInfoByAppID()
		{
		$appid= $_GET['appid'];
		$email= $_GET['email'];
		$query="select *from user where app_id='".$appid."' and email='".$email."'";
		$result = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
		$arr = array();
		if($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$a=array();
				$a['country'] = $row['country'];
				$a['occupation'] = $row['occupation'];	
				$a['email'] = $row['email'];	
				if ($row['app_type']==1) {
					$a['app_type'] = "Skilled Migration";
				}
				if ($row['app_type']==2) {
					$a['app_type'] = "Business Migration";
				}
				array_push($arr,$a);
			}
		}
		$json_response = json_encode($arr);
		echo $json_response;
		}
	public function FetchAssessmentDataById()
	{
		$appid= $_GET['appid'];
		$email= $_GET['email'];
		$sum=0;
		$query="select question, alternative_selected, point  from tb_clientinfo  where app_id='".$appid."' AND user_email='".$email."'";
		$result = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
		$arr["eduaid"] = array();
		if($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {

				$a=array();
				$a['question'] = $row['question'];
				$a['alternative_selected'] = $row['alternative_selected'];	
				$a['point'] = $row['point'];
				$sum = $sum+$a['point'];	
				array_push($arr["eduaid"],$a);
			}
			$arr["sum"] = $sum;
			$json_response = json_encode($arr);
		echo $json_response;
	}
}

	public function saveNotificationData()
	{
			$sendingdate = date('Y-m-d');
			$data = json_decode(file_get_contents("php://input"));
			$email = mysqli_real_escape_string($this->mysqli,$data->email);
			$title = mysqli_real_escape_string($this->mysqli,$data->title);
			$message = mysqli_real_escape_string($this->mysqli,$data->message);
			
			$query ="INSERT INTO notification(sendto,title,message,sendingdate)values('".$email."','".$title."','".$message."','".$sendingdate."')";
			$r=$this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
			if ($r)
			 {
				$success = array('status' => "Success", "msg" => "Notification Created Successfully.");
				echo json_encode($success);
			}

	}	
	public function saveNotificationByOccupation()
	{
		$sendingdate = date('Y-m-d');
		$data = json_decode(file_get_contents("php://input"));
		$occ = mysqli_real_escape_string($this->mysqli,$data->occ);
		$title = mysqli_real_escape_string($this->mysqli,$data->title);
		$message = mysqli_real_escape_string($this->mysqli,$data->message);

		$query ="INSERT INTO notification(sendto,title,message,sendingdate)values('".$occ."','".$title."','".$message."','".$sendingdate."')";
		$r=$this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
		if ($r) {
			$success = array('status' => "Success", "msg" => "Notification Created Successfully.");
			echo json_encode($success);
		}

	}
	public function ResturentDishInformation()
	{
		$appid= $_POST['resturentID'];
		$query="select *from dish_category order by id DESC";
		$result = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
		$arr = array();
		if($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$arr[]=$row;
			}
		}
		$json_response = json_encode($arr);
		echo $json_response;
	}

		public function NotificationEditView ()
	{
		$rid= $_GET['rid'];
		$query="select *from notification where id='".$rid."'";
		$result = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
		$arr = array();
		if($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$arr[]=$row;
			}
		}
		$json_response = json_encode($arr);
		echo $json_response;
	}


		public function OccupationList ()
	{
		
		$query="SELECT DISTINCT occupation FROM tb_occupation";
		$result = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
		$arr = array();
		if($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$arr[]=$row;
			}
		}
		$json_response = json_encode($arr);
		echo $json_response;
	}
	public function UpdateNotificationData()
	{
		
		$data = json_decode(file_get_contents("php://input"));
		$sendto = mysqli_real_escape_string($this->mysqli,$data->email);
		$title = mysqli_real_escape_string($this->mysqli,$data->title);
		$message = mysqli_real_escape_string($this->mysqli,$data->message);
		$rid = mysqli_real_escape_string($this->mysqli,$data->rid);
		$updatedate = date('y-m-d');
		$query ="UPDATE  `db_eduaid`.`notification` SET  `sendto` =  '$sendto',
				`title` =  '$title',
				`message` = '$message',
				`sendingdate` =  '$updatedate' WHERE  `notification`.`id` =$rid";
		$r=$this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
		if ($r)
		 {
			$success = array('status' => "Success", "msg" => "Notification Updated Successfully.");
			echo json_encode($success);
		}
	}

	/*########## Migration Start Question ###########*/
		public function ShowMigrationDataGrid()
		{
			$rid= $_GET['rid'];
			$query="SELECT *FROM tb_question WHERE immigrationtype='".$rid."' and file_name=''";
			$result = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
			$arr = array();
			if($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {
					$arr[]=$row;
				}
			}
			$json_response = json_encode($arr);
			echo $json_response;

		}
		public function getQuestionById()
	{
		$rid= $_GET['rid'];
		$query="select *from tb_question where id='".$rid."'";
		$result = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
		$arr = array();
		if($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$arr[]=$row;
			}
		}
		$json_response = json_encode($arr);
		echo $json_response;
	}

	public function updateQuestionInfo()
	{
		
		$data = json_decode(file_get_contents("php://input"));
		$rid = mysqli_real_escape_string($this->mysqli,$data->rid);
		$question = mysqli_real_escape_string($this->mysqli,$data->question);
		$alternative = mysqli_real_escape_string($this->mysqli,$data->alternative);
		$point = mysqli_real_escape_string($this->mysqli,$data->point);
		$caption = mysqli_real_escape_string($this->mysqli,$data->caption);
		$updatedate = date('y-m-d');
		$query ="UPDATE  `db_eduaid`.`tb_question` SET  `question` =  '$question',
				`alternative` =  '$alternative',
				`marks` = '$point',
				`caption` =  '$caption' WHERE  `tb_question`.`id` =$rid";
		$r=$this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
		if ($r)
		 {
			$success = array('status' => "Success", "msg" => "Question Updated Successfully.");
			echo json_encode($success);
		}
	}
	/*############## End of Migration Question################*/

	/*###########Tips part############*/
	public function TipsDataGrid ()
	{
		
		$query="SELECT *FROM tb_tips order by id DESC";
		$result = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
		$arr = array();
		if($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$arr[]=$row;
			}
		}
		$json_response = json_encode($arr);
		echo $json_response;
	}

	public function addTips(){
		$data = json_decode(file_get_contents("php://input"));
		$message = mysqli_real_escape_string($this->mysqli,$data->message);
		$datx=date('M d, Y');
		$query ="INSERT INTO tb_tips(details,inserted_date)values('".$message."','".$datx."')";
		$r=$this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
		if ($r) {
			$success = array('status' => "Success", "msg" => "Tips Inserted Successfully.");
			echo json_encode($success);
		}

	}
	public function RemoveTips()
	{
	$id=$_GET['rid'];
	$query ="DELETE from tb_tips WHERE id='".$id."'";
		$r=$this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
		if ($r) {
			$success = array('status' => "Success", "msg" => "Tips Inserted Successfully.");
			echo json_encode($success);
		}
	}
	public function getTipsById()
	{
		$rid= $_GET['rid'];
		$query="select *from tb_tips where id='".$rid."'";
		$result = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
		$arr = array();
		if($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$arr[]=$row;
			}
		}
		$json_response = json_encode($arr);
		echo $json_response;
	}
	public function UpdateTips()
	{
		$data = json_decode(file_get_contents("php://input"));
		
		$message = mysqli_real_escape_string($this->mysqli,$data->message);
		$rid = mysqli_real_escape_string($this->mysqli,$data->rid);
		$updatedate = date('y-m-d');
		$query ="UPDATE  `db_eduaid`.`tb_tips` SET  `details` = '$message' WHERE  `tb_tips`.`id` =$rid";
		$r=$this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
		if ($r)
		 {
			$success = array('status' => "Success", "msg" => "Tips Updated Successfully.");
			echo json_encode($success);
		}
	}
	/*Tips End*/

	public function EmailSubscription()
	{
		$query="SELECT *FROM tb_email order by id DESC";
		$result = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
		$arr = array();
		if($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$arr[]=$row;
			}
		}
		$json_response = json_encode($arr);
		echo $json_response;
	}
}	

?>