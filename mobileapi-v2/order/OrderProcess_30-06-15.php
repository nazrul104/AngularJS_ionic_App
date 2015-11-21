<?php
/**
* By Nazrul
*/
class OrderProcess extends REST
{
	private $mysqli ='';
	private $obj='';
	function __construct()
	{
		$this->obj = new DbConnect();
		$this->mysqli = $this->obj->db_connect();
	}

	public function CreateNewOrder()
		{
			$OrderList = stripslashes(mysqli_real_escape_string($this->mysqli,$_REQUEST['OrderList']));

			$resturent_id =  mysqli_real_escape_string($this->mysqli,$_REQUEST['rest_id']);
			$user_id =  mysqli_real_escape_string($this->mysqli,$_REQUEST['user_id']);
			$order_policy_id =  mysqli_real_escape_string($this->mysqli,$_REQUEST['order_policy_id']);
			$payment_option= mysqli_real_escape_string($this->mysqli,$_REQUEST['payment_option']);

			$payment_status = mysqli_real_escape_string($this->mysqli, $_REQUEST['payment_status']);
			$paypal_transaction_id = mysqli_real_escape_string($this->mysqli, $_REQUEST['paypal_transection_id']);

			$address =  mysqli_real_escape_string($this->mysqli,$_REQUEST['address']);
			$delivery_postcode =  mysqli_real_escape_string($this->mysqli,$_REQUEST['post_code']);
			$city =  mysqli_real_escape_string($this->mysqli,$_REQUEST['city']);
			$total_amount =  mysqli_real_escape_string($this->mysqli,$_REQUEST['total_amount']);
			$grand_total =  mysqli_real_escape_string($this->mysqli,$_REQUEST['grand_total']);

			$offer_text =  mysqli_real_escape_string($this->mysqli,$_REQUEST['offer_text']);
			$discount =  mysqli_real_escape_string($this->mysqli,$_REQUEST['discount']);

			$order_time =  mysqli_real_escape_string($this->mysqli,$_REQUEST['pre_order_delivery_time']);
			$order_delivery_time= strtotime($order_time);
			$special_request =  mysqli_real_escape_string($this->mysqli,$_REQUEST['comments']);
			$created_at=strtotime(date('d-m-Y'));

			$query ="INSERT INTO restaurant_order(user_id,restaurant_id,created_at,restaurant_order_policy_id,payment_method,total_amount,grand_total,discount_amount,offer_description,delivery_time,comments, payment_status, paypal_transaction_id) values('".$user_id."','".$resturent_id."','".$created_at."','".$order_policy_id."','".$payment_option."','".$total_amount."','".$grand_total."','".$discount."','".$offer_text."','".$order_delivery_time."','".$OrderList."', '".$payment_status."', '".$paypal_transaction_id."')";
			$r=$this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
			$your_order_id=mysqli_insert_id($this->mysqli); 
			
			if ($r)
			 {
			 	$obj=json_decode($OrderList);

				foreach($obj->OrderList as $value)
				{

					 self::SaveOrderItem($your_order_id,$value->DishId,$value->DishCount);
				}
				
			 	if(self::update_profile($user_id,$city,$address,$delivery_postcode)==1)
			 	{			 
			 		/*$q = "SELECT CASE r.payment_method WHEN 0 THEN 'Cash' WHEN 1 THEN 'PayPal' WHEN 2 THEN 'PayOverPhone' ELSE 'Cash' END as 'payment_method', r.delivery_time, r.delivery_type, a.email, b.first_name, b.last_name, b.mobile_no from restaurant_order r INNER join user as a ON r.user_id = a.id inner join user_profile as b on a.id = b.user_id WHERE r.id = '".$user_id."'";	
			 		$result = $this->mysqli->query($q) or die($this->mysqli.error.__LINE__);
			 		$user_info = $result->fetch_assoc();*/

			 		/*Send email to client*/		
			 					 		
					//$send_email = self::OrderConfirmationEmail($user_id, $your_order_id);
					/*Email send end*/

					$success = array('status' => "Success", "msg" => "Your order has been saved!","order_ID"=>$your_order_id);
					echo json_encode($success);
				}
				else
				{
					$success = array('status' => "Failed", "msg" => "Sorry! reservation failed");
					echo json_encode($success);
				}
			 }
				
			
		   	$this->obj->ConnectionClose();
		}

	private function update_profile($user_id,$city,$address,$post_code)
	{

		 	$query ="UPDATE user_profile SET delivery_town='$city',delivery_address1='$address', delivery_postcode='$post_code' WHERE user_id='".$user_id."'";
			$rst=$this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
			if ($rst) 
			{
				return 1;
			}

	}
	
	public function updateOrder()
	{
		//error_reporting(E_ALL);
		$restaurant_id =  mysqli_real_escape_string($this->mysqli,$_POST['rest_id']);
		$order_id =  mysqli_real_escape_string($this->mysqli,$_POST['order_id']);
		$payment_status =  mysqli_real_escape_string($this->mysqli,$_POST['payment_status']);
		$paypal_transaction_id = mysqli_real_escape_string($this->mysqli,$_POST['transaction_id']);

		 	$query ="UPDATE restaurant_order SET payment_status='$payment_status',paypal_transaction_id='$paypal_transaction_id' WHERE restaurant_id='".$restaurant_id."' and id='".$order_id."'";
			//echo $query;
			//die();
			$rst=$this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
			
			if ($rst) 
			{
				$success = array('status' => "Success", "msg" => "Order update done!","order_ID"=>$order_id);
				echo json_encode($success);
			}
			else  {
				$success = array('status' => "Failed", "msg" => "Sorry! Order not updated");
				echo json_encode($success);
			}
			
			$this->obj->ConnectionClose();

	}
	
	

	private function SaveOrderItem($order_id,$dish_id,$q)
	{
		$query ="INSERT INTO restaurant_order_dish(restaurant_order_id,dish_id,quantity)values('".$order_id."','".$dish_id."','".$q."')";
		$this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
		
	}

	private function OrderConfirmationEmail($user_id, $your_order_id)
	{	
		$to = 'ahabir11@gmail.com';
		$subject = "Thanks for ordering with ChefOnline";
		
		$headers = 'From: ChefOnline info@chefonline.co.uk' . "\r\n" ;
		$headers .='Reply-To: '. $to . "\r\n" ;
		// $headers .= "X-Priority: 3\r\n";		
		$headers .='X-Mailer: PHP/' . phpversion();
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";

		$message = "Hi ".$user_id.", Thank you for your order. Your order id is: $your_order_id. Please keep this number.";
		$message.="<br>Best Regards<br> Smart Resturent Solution Team.";
		mail($to, $subject, $message, $headers);
		
	}


}
?>