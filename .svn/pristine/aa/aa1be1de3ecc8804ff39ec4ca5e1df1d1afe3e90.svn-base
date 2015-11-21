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
			 		$q = "SELECT res.restaurant_name,r.id, r.restaurant_id, CASE r.payment_method WHEN 0 THEN 'Cash' WHEN 1 THEN 'PayPal' WHEN 2 THEN 'PayOverPhone' ELSE 'Cash' END as 'payment_method', r.delivery_time, r.delivery_type, r.comments, a.email, b.first_name, b.last_name, b.address1, b.mobile_no from restaurant_order r INNER join user as a ON r.user_id = a.id inner join user_profile as b on a.id = b.user_id INNER JOIN restaurant res ON r.restaurant_id = res.id WHERE r.id = '".$your_order_id."' LIMIT 1";	
			 		$result = $this->mysqli->query($q) or die($this->mysqli.error.__LINE__);
			 		$order_info = $result->fetch_assoc();
			 		
			 		$username = $order_info['first_name']." ".$order_info['last_name'];
			 		$restaurant_name = $order_info['restaurant_name'];		
			 		$subject = "Order Confirmation";
			 		$to = $order_info['email'];
			 		$msg = "";
			 		$msg .= '<p style="font-size: 14px; font-family: arial; color: #000">Hi '.$username.', Thank you for your order with <strong>'.$restaurant_name.'</strong>.</p>'; 
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
			 								<td colspan="2" style="font-size: 16px; padding: 5px;" height="30px">ORDER DETAILS</td>
			 							</tr>
			 							<tr>
			 								<td style="padding-left:10px;"><strong>Payment Type:</strong></td>
			 								<td style="padding-left:10px;">'.$order_info['payment_method'].'</td>
			 							</tr>
			 							<tr>
			 								<td style="padding-left:10px;"><strong>Collection Date:</strong></td>
			 								<td style="padding-left:10px;">'. date('d-m-Y h:m A', $order_info['delivery_time']).'</td>
			 							</tr>			 							
			 							<tr>
			 								<td colspan="2" style="border-bottom: 1px #999 dashed;">&nbsp;</td>
			 							</tr>
										<tr style="background:#CCCCCC">
											<td colspan="2" style="border-bottom: 1px #999 dashed;padding: 5px 0;">Your Details</td>
										</tr>			 							
			 							<tr>
			 								<td style="padding-left:10px;"><strong>Fullname:</strong></td>
			 								<td style="padding-left:10px;">'.$order_info["first_name"]." ".$order_info["last_name"] .'</td>
			 							</tr>
			 							<tr>
			 								<td style="padding-left:10px;"><strong>Address:</strong></td>
			 								<td style="padding-left:10px;">'.$order_info['address1'].' </td>
			 							</tr>
			 							<tr style="margin-bottom: 5px; border-bottom: 1px #999 dashed;">
			 								<td style="padding-left:10px;"><strong>Mobile:</strong></td>
			 								<td style="padding-left:10px;">'.$order_info["mobile_no"].' </td>
			 							</tr>			 							
			 						</tbody>
			 					</table>';
			 		$order_list_details = json_decode($order_info['comments'], true);
			 		$msg .= '<table>
								<tr style="background-color: #ddd; height: 30px; color: #000;"><td colspan="4" align="center">Item List</td></tr>
			 				';
			 		$msg .= '<tr style="border-bottom: 1px #999 dashed;border color: #000;"><td>#</td><td>Dish Name</td><td>Quanity</td><td align="center">Price</td></tr>';

			 		foreach ($order_list_value['OrderList'] as $key => $value) {
			 				$msg .= '<tr style="border-bottom: 1px #999 dashed;"><td>'.$key + 1.'</td><td>'.$value["DishName"].'</td><td width="60">'.$value["DishCount"].'</td><td width="60" align="right"> £'.$value["DishPrice"].'</td></tr>
			 				';
			 			}
			 		$msg .= '<tr style="border-top: 1px #000 solid;border-bottom: 1px #999 dashed;"><td colspan="3"></td><td> £'.$value['total_amount'].'</td></tr>';	
			 		$msg .= '<tr style="border-bottom: 1px #999 dashed;"><td colspan="3"></td><td> £'.$value['discount_amount'].'</td></tr>';	
			 		$msg .= '<tr style=""><td colspan="3"></td><td> £'.$value['grand_total'].'</td></tr>';	


			 		$msg .= '</table>';
			 		$msg .= '</td></tr></tbody></table>
			 					</td></tr></tbody></table>					
			 				';			 		
			 		$msg .="<br>Best Regards<br> <b>Smart Restaurent Solution Team.</b>";

			 		/*Send email to client*/				 				 					 	
					$send_email = self::OrderConfirmationEmail($to, $subject, $msg);
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
	
	/*================================================
	=            Update restauarant order            =
	================================================*/
	
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
	
	/*-----  End of Update restauarant order  ------*/
	
	
	private function SaveOrderItem($order_id,$dish_id,$q)
	{
		$query ="INSERT INTO restaurant_order_dish(restaurant_order_id,dish_id,quantity)values('".$order_id."','".$dish_id."','".$q."')";
		$this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
		
	}

	/*===================================================================
	=            Send restaurant order details to user email            =
	===================================================================*/
	
	private function OrderConfirmationEmail($to, $subject, $message)
	{	
		// $to = 'ahabir11@gmail.com';		
		$headers = 'From: ChefOnline info@chefonline.co.uk' . "\r\n" ;
		$headers .='Reply-To: '. $to . "\r\n" ;
		// $headers .= "X-Priority: 3\r\n";		
		$headers .='X-Mailer: PHP/' . phpversion();
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";				
		mail($to, $subject, $message, $headers);
		return 1;		
	}
	
	/*-----  End of Send restaurant order details to user email  ------*/

	

}
?>