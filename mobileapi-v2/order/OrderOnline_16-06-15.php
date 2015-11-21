<?php

class OrderOnline extends REST
{

  private $mysqli ='';
  private $obj='';

  function __construct()
  {
    $this->obj = new DbConnect();
    $this->mysqli = $this->obj->db_connect();
  } 

    public function RestaurantDish()
    {
      /*header('Content-Type: application/json; charset=utf-8');*/
      header('Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT');
      $resturent_id=mysqli_real_escape_string($this->mysqli,$_REQUEST['rest_id']);
      if(self::getRestaurantOrderPolicy($resturent_id)!=null)
      {
      $query="SELECT name,id, restaurant_id FROM restaurant_cuisine WHERE restaurant_id='".$resturent_id."'";
      $this->mysqli->set_charset("utf8");
      $result = $this->mysqli->query($query); 
      $response["app"] = array();
      $arr=array();
      if($result->num_rows > 0)
      {
        while($row = $result->fetch_assoc()) 
        {
          $arr["restaurant_id"] = $row['restaurant_id'];
          $arr["Cuisine_name"]=$row['name'];
          $wrp["child"]=array();

          $arr['discount']=self::getDiscount($resturent_id);
          $arr['offer'] = self::getOfferInformation($resturent_id);
          $arr['order_policy']=self::getRestaurantOrderPolicy($resturent_id);
          //$arr['restuarent_schedule']=self::getResturentSchedule($resturent_id);

          $result2=$this->mysqli->query("SELECT id as category_id,name,description FROM dish_category WHERE restaurant_cuisine_id='".$row['id']."' AND status = 1 ORDER BY name ASC");
          $this->mysqli->set_charset("utf8");
          if($result2->num_rows > 0)
          {
            while($row2 = $result2->fetch_assoc())
            {
              $cat = array();
              $cat["Category_ID"]=$row2['category_id'];
              $cat["Category_Name"]=$row2['name'];
              $cat["Category_Description"]=$row2['description'];
              $cat["Dish_Information"]=self::getDishInformation($resturent_id,$row2['category_id']);
              // $cat['order_policy']=self::getRestaurantOrderPolicy($resturent_id);
              array_push($wrp["child"],$cat);
            }
                $arr["category"]=$wrp["child"];
                array_push($response['app'], $arr); 
          }
        }
      }
      echo json_encode($response,JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
     }
     else
     {
     	$response["app"] = array();
     	$callback = array('status' => "Failed", "msg" => "Sorry! No information found.");
     	array_push($response["app"],$callback);
     	echo json_encode($response);
     }
      $this->obj->ConnectionClose();
    }
    
    private function getResturentSchedule($resturent_id)
    {
      /*$resturent_id=mysqli_real_escape_string($this->mysqli,$_POST['rest_id']);*/
      $days=array(1=>"Monday",2=>"Tuesday",3=>"Wednesday",4=>"Thursday",5=>"Friday",6=>"Saturday",7=>"Sunday");
      $tempDate = date('Y-m-d');
      $dayName=date('l', strtotime( $tempDate));
      
    
      $arr["schedule"]=array();

      foreach ($days as $key => $value)
      {
        $list['list']=array();
        $list['weekday_id']=$key;
        $list['day_name']=$value;

        $query="SELECT  *FROM restaurant_schedule WHERE restaurant_id='".$resturent_id."' AND weekday='".$key."' ORDER BY  weekday ASC";
        $this->mysqli->set_charset("utf8");
        $result = $this->mysqli->query($query);
        if($result->num_rows > 0)
        {
          while($row = $result->fetch_assoc()) 
          {
              if ($row['status']==1) 
              {
                $Listdays=array();  
                $Listdays['shift']=$row['shift'];
                $Listdays['shift']=$row['shift'];
                $Listdays['opening_time']=date('h:i:s A', $row['opening_time']);
                $Listdays['closing_time']=date('h:i:s A', $row['closing_time']);
                array_push($list["list"],$Listdays);
              }
              
          }
        
        }
        array_push($arr["schedule"],$list);
      }
      return $arr;
    }


    private function getDishInformation($restID,$catID)
    {
      $query="SELECT *FROM restaurant_dish WHERE restaurant_id='".$restID."' AND dish_category_id ='".$catID."' AND status = 1 ORDER BY name ASC";
      $result = $this->mysqli->query($query);
      $this->mysqli->set_charset("utf8");
      $arr["ItemList"]=array();
      if($result->num_rows > 0) 
      {
        while($row = $result->fetch_assoc())
        {
          if($row['parent_dish_id']==0)
          {
            $dish = array();
            $dish['Dish_id']=$row['id'];
            $dish['Dish_Name'] = $row['name'];
            $dish['Dish_Description'] = $string = trim(preg_replace('/\s+/', ' ', $row['dish_description']));
            
            $dish['Dish_Rating'] = $row['rating_count'];
            $dish['Dish_Total_Rating'] = $row['total_rating'];
            $dish['Dish_Spice_Level'] = $row['spice_level'];
            $dish['Dish_Allergens'] = ($row['allergens'] !="") ? explode(',', $row['allergens']) : null;
            $dish['options']=self::getOptionData($restID,$row['id']);
            if ($dish['options']==null) 
            {
              $dish['Dish_Price'] =$row['price'];
            }
            else
            {
              $dish['Dish_Price'] =self::getMinPrice($row['id']);
            }
            array_push($arr["ItemList"],$dish);
  
          }
        }
      }
      return $arr;
      $this->obj->ConnectionClose();
    }
    
    private function getCommonData($col,$pid)
    {
      $query="SELECT $col FROM restaurant_dish WHERE id='".$pid."'";
      $result = $this->mysqli->query($query);
      $this->mysqli->set_charset("utf8");
      $row = $result->fetch_assoc();
      return $row[$col];
      $this->obj->ConnectionClose();
    }
    private function getMinPrice($pid)
    {
      $query="SELECT price, min(price) FROM restaurant_dish WHERE parent_dish_id='".$pid."'";
      $result = $this->mysqli->query($query);
      $this->mysqli->set_charset("utf8");
      $row = $result->fetch_assoc();
      return $row['price'];
      $this->obj->ConnectionClose();
    } 
    public function getOptionData($restID,$pid)
    {
        $query="SELECT id as self_id,name,parent_dish_id,price,dish_description,rating_count,total_rating,spice_level,allergens FROM restaurant_dish WHERE restaurant_id='".$restID."' AND parent_dish_id ='".$pid."'";
        $result = $this->mysqli->query($query);
        $this->mysqli->set_charset("utf8");
        $arr["OptionList"]=array();
        if($result->num_rows > 0) 
        {
          while($row = $result->fetch_assoc())
          {
            $options = array();
            $options['parent_dish_id']=$row['parent_dish_id'];
            $options['self_id']=$row['self_id'];
            $options['option_name']=$row['name'];
            $options['option_price']=$row['price'];
            $options['option_Description'] = $row['dish_description'];
            array_push($arr["OptionList"],$options);
          
          }
        }
        else
        {
          return null;
        }
        return $arr;
        $this->obj->ConnectionClose();
    }
  /*get map data*/
  private function getPolicyStatus($restid, $value)
  {
    $value = strtolower($value);
    $query = "select status from restaurant_order_policy where LOWER(name) = '".$value."' AND restaurant_id = '".$restid."' AND status != '-1'";
    $result = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
    $row = $result->fetch_assoc();
    return ($row['status'] == 1) ? 1 : 0;
  }


  private function getOfferInformation($restid)
  {
        $query="SELECT * FROM restaurant_offer WHERE restaurant_id = '".$restid."' AND status = 1";
        $result = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
        // $arr = array();
        $arr['offer_list'] = array();
        if($result->num_rows > 0)
         {
          $arr['status'] = 1;          
          while($row = $result->fetch_assoc()) 
          {
            // $arr['offer_list']=$row;
            $resturent=array();
            $resturent['id']=$row['id'];
            $resturent['status']=$row['status'];
            $resturent['restaurant_id']=$row['restaurant_id'];
            $resturent['description']=$row['description'];
            $resturent['offer_title']=$row['offer_title'];
            $resturent['image']=$row['image'];
            $resturent['thumb_name']=$row['thumb_name'];
            $resturent['eligible_amount']=$row['eligible_amount'];
            $resturent['restaurant_order_policy_id']=$row['restaurant_order_policy_id'];
            $resturent['position']=$row['position'];
            $resturent['image_from']=$row['image_from'];
            $resturent['offer_for']=$row['offer_for'];
            array_push($arr["offer_list"],$resturent);
          }
        }  
        else{
          $arr['status'] = 0;
        }    

        if(count($arr['offer_list']) > 0)
        {
          return $arr;
        } 
        else
        {
          return array('status'=> 0);
        }
        
  }

  public function getRestaurantDeliveryArea()
  {
    $rest_id = mysqli_real_escape_string($this->mysqli, $_REQUEST['rest_id']);
    $query = "SELECT delivery_radius, free_delivery_radius, postcode_list, delivery_charge_per_mile FROM restaurant_delivery_area WHERE restaurant_id = '".$rest_id."' LIMIT 1";
    $rows = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
    $arr['app'] = array();
    if($rows->num_rows > 0)
    {
      $row = $rows->fetch_assoc();
      $data = array();
      $data['rest_id'] = $rest_id;
      $data['delivery_radius'] = $row['delivery_radius'];
      $data['free_delivery_radius'] = $row['free_delivery_radius'];
      $data['delivery_charge_per_mile'] = $row['delivery_charge_per_mile'];
      $data['postcode_list'] = $row['postcode_list']; 
      array_push($arr['app'], $data);
      echo json_encode($arr);
    }
    else
    {
      $callback = array('status' => "Failed", "msg" => "Sorry! No information found.");
      array_push($arr['app'],$callback);
      echo json_encode($arr);
    }
  }

  public function getresturentInformation()
  {
    
    $query_where=" WHERE a.is_policy_set = 1 ";
    if(isset($_REQUEST['postcode']))
    {
      $code= mysqli_real_escape_string($this->mysqli, $_REQUEST['postcode']);
      $query_where .=" AND a.postcode LIKE '$code%'";
    }

      $query="select a.id as rest_id,a.logo,a.slider_image,a.restaurant_name,a.address1,a.address2,a.city,a.domain,a.latitude,a.longitude,a.postcode,a.business_tel, a.accept_reservation FROM restaurant a $query_where";
      $this->mysqli->set_charset("utf8");
      $result = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
      $arr["app"] = array();
      if($result->num_rows > 0) 
      {
        while($row = $result->fetch_assoc()) 
        {
          $resturent=array();
          $resturent['rest_id']=$row['rest_id'];
          $resturent['logo']=$row['logo'];
          $resturent['slider_image']= rtrim($row['slider_image'], ',');
          $resturent['restaurant_name']=$row['restaurant_name'];
          $resturent['address1']=$row['address1'];
          $resturent['address2']=$row['address2'];
          $resturent['city']=$row['city'];
          $resturent['domain']=$row['domain'];
          $resturent['latitude']=$row['latitude'];
          $resturent['longitude']=$row['longitude'];
          $resturent['postcode']=$row['postcode'];
          $resturent['business_tel']=$row['business_tel'];
          $resturent['accept_reservation']=($row['accept_reservation']!=0) ? 1 : 0;
          $resturent['accept_collection']= self::getPolicyStatus($row['rest_id'], "Collection");
          $resturent['accept_delivery']= self::getPolicyStatus($row['rest_id'], "Delivery");
          $resturent['restuarent_schedule']=self::getResturentSchedule($row['rest_id']);
          $resturent['available_cuisine']=self::getCuisine($row['rest_id']);
          $resturent['discount']=self::getDiscount($row['rest_id']);
          $resturent['order_policy']=self::getRestaurantOrderPolicy($row['rest_id']);
          $resturent['offer']=self::getOfferInformation($row['rest_id']);
          
          array_push($arr["app"],$resturent);
        }
        $json_response = json_encode($arr);
        echo $json_response;
      }
      else
      {
        $callback = array('status' => "Failed", "msg" => "Sorry! No information found.");
        array_push($arr["app"],$callback);
        echo json_encode($arr);
      }
    
  }
  private function getCuisine($restID)
  {
      $query="SELECT id as cid, name FROM restaurant_cuisine WHERE restaurant_id='".$restID."' AND status=1 ORDER BY sort_order ASC";
      $result = $this->mysqli->query($query);
      $this->mysqli->set_charset("utf8");
      $arr["cuisine"] = array();
      if($result->num_rows > 0) 
      {
        while($row = $result->fetch_assoc())
        {
          $cusine=array();
          $cusine['cuisine_id']=$row['cid'];
          $cusine['name']=$row['name'];
          array_push($arr["cuisine"],$cusine);
        }
        return $arr;
        $this->obj->ConnectionClose();
      }else{
        return null;
      }

  }
  private function getDiscount($restID)
  {
      $query="SELECT a.id as discount_id, a.discount_title,a.type,a.amount,a.eligible_amount,a.discount_description,a.discount_position,a.status,b.id as restaurant_order_policy_id,b.name as order_type FROM restaurant_discount a,restaurant_order_policy b WHERE a.restaurant_id='".$restID."' AND b.id=a.discount_for";
      $result = $this->mysqli->query($query);
      $this->mysqli->set_charset("utf8");
      $arr['off'] = array();
      if($result->num_rows > 0) 
      {
        while($row = $result->fetch_assoc())
        {
          if ($row['status']==1)
           {
            $dis=array();
            $dis['discount_id'] = $row['discount_id'];
            $dis['restaurant_order_policy_id']=$row['restaurant_order_policy_id'];
            $dis['order_type']=$row['order_type'];
            if ($row['type']==1)
             {
              $dis['discount_type']="Percentage";
             }
            if ($row['type']==2)
            {
              $dis['discount_type']="Fixed";
            }
            $dis['discount_amount']=$row['amount'];
            $dis['discount_title']=$row['discount_title'];
            $dis['discount_description']=$row['discount_description'];
            $dis['eligible_amount']=$row['eligible_amount'];
            array_push($arr["off"],$dis);
          }
        }
        if(count($arr) > 0)
        {
          $arr['status'] = 1;
          return $arr;
        }else
        {
          return array('status'=> 0);
        }
        $this->obj->ConnectionClose();
      }
      else
      {
        return array('status'=> 0);
      }
  }
  private function getRestaurantOrderPolicy($restID)
  {
      $part="";
      if (!empty($_REQUEST['ordertype'])) 
      {
        $part=" AND name='".$_REQUEST['ordertype']."'";
      }

      $query="SELECT id as policy_id, name, policy_time, min_order, status FROM restaurant_order_policy WHERE restaurant_id='".$restID."' AND status = 1 $part";
      $result = $this->mysqli->query($query);
      $this->mysqli->set_charset("utf8");
      $arr["policy"] = array();
      if($result->num_rows > 0) 
      {
        while($row = $result->fetch_assoc())
        {
          $policy=array();
          $policy['policy_id']=$row['policy_id'];
          $policy['policy_name']=$row['name'];
          $policy['policy_time']=$row['policy_time'];
          $policy['min_order']=$row['min_order'];
          $policy['status']=$row['status'];
          array_push($arr["policy"],$policy);
        }
          return $arr;
          $this->obj->ConnectionClose();
      }
      else
      {
        return null;
      }
  }
   public function searchResturent()
   {
    $active_count = 0;
    $notactive_count = 0;
    $query_where="";
    $filter_query = "";

    $arr["app"] = array();
    if ($_REQUEST['searchText']!="")
     {
      $code=$_REQUEST['searchText'];
      $query_where =" WHERE a.postcode LIKE '$code%' OR a.restaurant_name LIKE '%$code%'";

      if($_REQUEST['ordertype'] == "Delivery" || $_REQUEST['ordertype'] == "Collection")
      {
        $filter_query = " , b.name HAVING b.name = '".$_REQUEST['ordertype']."' AND b.status = 1";       
      }
      else if($_REQUEST['ordertype'] == "Reservation")
      {
        $query_where .= " AND a.accept_reservation = 1";
      }      
    
      $query="select a.id as rest_id, a.*, b.name, b.status from restaurant a LEFT JOIN restaurant_order_policy b ON a.id = b.restaurant_id $query_where AND a.is_policy_set = 1 GROUP BY a.id $filter_query";

      $this->mysqli->set_charset("utf8");
      $result = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);

      if($result->num_rows > 0) 
      {
        while($row = $result->fetch_assoc()) 
        {
          if($row['is_policy_set'] == 1)
          {
            $resturent=array();
            $resturent['rest_id']=$row['rest_id'];
            $resturent['logo']=$row['logo'];
            $resturent['slider_image']= rtrim($row['slider_image'], ',');
            $resturent['restaurant_name']=$row['restaurant_name'];
            $resturent['address1']=$row['address1'];
            $resturent['address2']=$row['address2'];
            $resturent['city']=$row['city'];
            $resturent['domain']=$row['domain'];
            $resturent['latitude']=$row['latitude'];
            $resturent['longitude']=$row['longitude'];
            $resturent['postcode']=$row['postcode'];
            $resturent['business_tel']=$row['business_tel'];
            $resturent['accept_reservation']=($row['accept_reservation']!=0) ? 1 : 0;
            $resturent['accept_collection']= self::getPolicyStatus($row['rest_id'], "Collection");
            $resturent['accept_delivery']= self::getPolicyStatus($row['rest_id'], "Delivery");
            $resturent['restuarent_schedule']=self::getResturentSchedule($row['rest_id']);
            $resturent['available_cuisine']=self::getCuisine($row['rest_id']);
            $resturent['discount']=self::getDiscount($row['rest_id']);
            $resturent['order_policy']=self::getRestaurantOrderPolicy($row['rest_id']);  
            $resturent['offer']=self::getOfferInformation($row['rest_id']);  
             
            array_push($arr["app"],$resturent);
            $active_count++;
          }     
          else{
            $notactive_count++;
          }   
        }
        
        if($active_count > 0)
        {
          $json_response = json_encode($arr);
          echo $json_response;
        }
        else
        {
          $callback = array('status' => "Failed", "msg" => "Sorry! No restaurant found with your keywords.");
          array_push($arr["app"],$callback);
          echo json_encode($arr);
        }
      }
      else
      {
        $callback = array('status' => "Failed", "msg" => "Sorry! No information found.");
        array_push($arr["app"],$callback);
        echo json_encode($arr);
      }
    }
    else
    {
      $callback = array('status' => "Failed", "msg" => "No input text given.");
      array_push($arr["app"],$callback);
      echo json_encode($arr);
    }     
  }

  public function getPreviousOrders(){
    $userid =  mysqli_real_escape_string($this->mysqli,$_REQUEST['userid']);   
    $arr["orders"] = array();
    $count = 0;
    if(!empty($userid))
    {
      // $query_where=" WHERE a.user_id = '".$userid."'";
      $query = "select a.id as order_id, CASE WHEN a.grand_total THEN a.grand_total ELSE '0.00' END as grand_total, a.created_at as order_date, CASE a.payment_method WHEN 0 THEN 'Cash' WHEN 1 THEN 'PayPal' WHEN 2 THEN 'PayOverPhone' ELSE 'Cash' END as 'payment_method', CASE a.payment_status WHEN 1 THEN 'Confirmed' ELSE 'Not Confirmed' END as payment_status, b.id as rest_id, b.restaurant_name, b.logo as restaurant_logo, b.address1,b.address2,b.city,b.domain,b.latitude,b.longitude,b.postcode,b.business_tel, b.accept_reservation from restaurant_order a inner join restaurant b on a.restaurant_id = b.id where a.user_id = '$userid' ORDER BY a.id DESC";
      $this->mysqli->set_charset("utf8");
      $result = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);

      if($result->num_rows > 0) 
      {
        while($row = $result->fetch_assoc()) 
        {
          $order=array();
          $order['order_no'] = $row['order_id'];
          $order['rest_id'] = $row['rest_id']; 
          $order['restaurant_name'] = $row['restaurant_name']; 
          $order['logo'] = $row['restaurant_logo']; 
          $order['grand_total'] = $row['grand_total']; 
          $order['order_date'] = date('M d, Y',$row['order_date']);
          $order['payment_method']=$row['payment_method'];
          $order['payment_status']=$row['payment_status'];
          $order['address1']=$row['address1'];
          $order['address2']=$row['address2'];
          $order['city']=$row['city'];
          $order['domain']=$row['domain'];
          $order['latitude']=$row['latitude'];
          $order['longitude']=$row['longitude'];
          $order['postcode']=$row['postcode'];
          $order['business_tel']=$row['business_tel'];
          $order['accept_reservation']=($row['accept_reservation']!=0) ? 1 : 0;
          $order['available_cuisine']=self::getCuisine($row['rest_id']);
          $order['discount']=self::getDiscount($row['rest_id']);          
          array_push($arr["orders"],$order);
          $count++;
        }
        $arr['results'] = array();
        $callback = array('status' => "Success", "msg" => "You have ".$count.". orders");
        array_push($arr["results"], $callback);
        $json_response = json_encode($arr);
        echo $json_response;
      }
      else
      {
        $callback = array('status' => "Failed", "msg" => "Sorry! No orders found.");
        array_push($arr["orders"],$callback);
        echo json_encode($arr);

      }
    }
    else
    {
      $callback = array('status' => "Failed", "msg" => "No input text given.");
      array_push($arr["orders"],$callback);
      echo json_encode($arr);
    }
  }

  public function getOrderedDish($orderId)
  {
    $order_id = mysqli_real_escape_string($this->mysqli, $orderId);

    $query="SELECT r.dish_id, r.quantity, r_dish_cat.name as dish_category_name, r_dish.name as dish_name, r_dish.dish_description, r_dish.price as dish_price, r.quantity * r_dish.price as dish_ordered_price, r_dish.image as dish_image from restaurant_order_dish r inner join restaurant_dish r_dish on r.dish_id = r_dish.id inner join dish_category r_dish_cat on r_dish.dish_category_id = r_dish_cat.id where r.restaurant_order_id = '".$order_id."'";
    $result = $this->mysqli->query($query);
    $this->mysqli->set_charset("utf8");
    $arr["dish_choose"] = array();
    if($result->num_rows > 0) 
    {
      while($row = $result->fetch_assoc())
      {
        $dish_choose=array();
        $dish_choose['dish_id']=$row['dish_id'];
        $dish_choose['dish_name']=$row['dish_name'];
        $dish_choose['dish_description']=$row['dish_description'];
        $dish_choose['dish_category_name']=$row['dish_category_name'];
        $dish_choose['dish_price']=$row['dish_price'];
        $dish_choose['quantity']=$row['quantity'];
        $dish_choose['dish_ordered_price']= round($row['dish_ordered_price'], 2);
        $dish_choose['dish_image']=$row['dish_image'];
        array_push($arr["dish_choose"],$dish_choose);
      }
        return $arr;
        $this->obj->ConnectionClose();
    }
    else
    {
      return null;
    }


  }

  public function getPreviousOrderDetails()
  {
    $order_id = mysqli_real_escape_string($this->mysqli, $_POST['order_id']);
    $query_where ="";
    $arr["order"] = array();
    $query = "select a.id as order_id, CASE WHEN a.grand_total THEN a.grand_total ELSE '0.00' END as grand_total, a.created_at as order_date, a.order_type, CASE a.payment_method WHEN 0 THEN 'Cash' WHEN 1 THEN 'PayPal' WHEN 2 THEN 'PayOverPhone' ELSE 'Cash' END as 'payment_method', CASE a.payment_status WHEN 1 THEN 'Confirmed' ELSE 'Not Confirmed' END as payment_status, b.id as rest_id, b.restaurant_name, b.logo as restaurant_logo, b.address1,b.address2,b.city,b.domain,b.latitude,b.longitude,b.postcode,b.business_tel, b.accept_reservation from restaurant_order a inner join restaurant b on a.restaurant_id = b.id where a.id = '$order_id'";
      $this->mysqli->set_charset("utf8");
      $result = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);       
      if($result->num_rows > 0) 
      {
        while($row = $result->fetch_assoc()) 
        {
          $order=array();
          $order['order_no'] = $row['order_id'];
          $order['rest_id'] = $row['rest_id']; 
          $order['restaurant_name'] = $row['restaurant_name']; 
          $order['logo'] = $row['restaurant_logo']; 
          $order['grand_total'] = $row['grand_total']; 
          $order['order_date'] = date('M d, Y',$row['order_date']);
          $order['order_type'] = $row['order_type'];
          $order['payment_method'] = $row['payment_method'];
          $order['payment_status'] = $row['payment_status'];
          $order['address1']=$row['address1'];
          $order['address2']=$row['address2'];
          $order['city']=$row['city'];
          $order['domain']=$row['domain'];
          $order['latitude']=$row['latitude'];
          $order['longitude']=$row['longitude'];
          $order['postcode']=$row['postcode'];
          $order['business_tel']=$row['business_tel'];
          $order['discount'] = self::getDiscount($row['rest_id']);

          $order['ordered_dish']=self::getOrderedDish($row['order_id']);

          $order['accept_collection']= self::getPolicyStatus($row['rest_id'], "Collection");
          $order['accept_delivery']= self::getPolicyStatus($row['rest_id'], "Delivery");
          $order['accept_reservation']=($row['accept_reservation']!=0) ? 1 : 0;
	        $order['restuarent_schedule']=self::getResturentSchedule($row['rest_id']);
	        $order['available_cuisine']=self::getCuisine($row['rest_id']);
          $order['order_policy']=self::getRestaurantOrderPolicy($row['rest_id']);
          $order['offer']=self::getOfferInformation($row['rest_id']);

          array_push($arr["order"],$order);
        }
        $json_response = json_encode($arr);
        echo $json_response;
      }
      else
      {
        $callback = array('status' => "Failed", "msg" => "Sorry! No orders found.");
        array_push($arr["order"],$callback);
        echo json_encode($arr);
      }       
  }


  private function getFavDishInformation($dishId)
  {
    $query="SELECT a.id as dish_id, b.name as dish_category, a.restaurant_id as rest_id, a.parent_dish_id, a.is_parent, a.name as dish_name, a.dish_description, a.price as dish_price, a.spice_level, a.rating_count, a.total_rating, a.allergens FROM restaurant_dish a INNER JOIN dish_category b ON a.dish_category_id = b.id WHERE a.id='".$dishId."' AND a.status = 1 ORDER BY a.name ASC";
    $result = $this->mysqli->query($query);
    $this->mysqli->set_charset("utf8");
    $arr =array();
    if($result->num_rows > 0) 
    {
      while($row = $result->fetch_assoc())
      {
        if($row['parent_dish_id']==0)
        {
          $dish = array();
          $dish['Dish_id']=$row['dish_id'];
          $dish['Dish_Name'] = $row['dish_name'];
          $dish['Dish_Category'] = $row['dish_category'];
          $dish['Dish_Description'] = $string = trim(preg_replace('/\s+/', ' ', $row['dish_description']));          
          $dish['Dish_Rating'] = $row['rating_count'];
          $dish['Dish_Total_Rating'] = $row['total_rating'];
          $dish['Dish_Spice_Level'] = $row['spice_level'];
          $dish['Dish_Allergens'] = ($row['allergens'] !="") ? explode(',', $row['allergens']) : null;
          $dish['Options']=self::getOptionData($row['rest_id'],$row['dish_id']);
          if ($dish['Options']==null) 
          {
            $dish['Dish_Price'] =$row['dish_price'];
          }
          else
          {
            $dish['Dish_Price'] =self::getMinPrice($row['dish_id']);
          }
          array_push($arr,$dish);
        }
      }
    }
    return $arr;
    $this->obj->ConnectionClose();
  }

  public function getUserFavouriteList()
  {
    $customer_id = mysqli_real_escape_string($this->mysqli, $_REQUEST['customer_id']);
    $rest_id = mysqli_real_escape_string($this->mysqli, $_REQUEST['rest_id']);
    $query_param = "";
    if(!empty($rest_id) && $rest_id > 0)
    {
      $query_param = " AND restaurant_id = '".$rest_id."'";
    }


    $arr['favourite'] = array();
    $query = "SELECT fav.id as fav_id, fav.restaurant_id, fav.target_id, fav.user_id, fav.type as cus_fav_type FROM restaurant_customer_favourite fav WHERE fav.user_id = '".$customer_id."' $query_param GROUP BY fav.target_id";
    $this->mysqli->set_charset("utf8");
    $result = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);  

    if($result->num_rows > 0)
    {
       while($row = $result->fetch_assoc()) 
      {
        $fav = array();
        $fav['fav_id'] = $row['fav_id'];
        $fav['restaurant_id'] = $row['restaurant_id'];
        $fav['user_id'] = $row['user_id'];        
        $fav['cus_fav_dish'] = ($row['cus_fav_type'] == 1) ? self::getFavDishInformation($row['target_id']) : "No information found";
        array_push($arr['favourite'], $fav);
      } 
       echo json_encode($arr);     
    } 
    else
    {
      $callback = array('status' => "Failed", "msg" => "Sorry! No information found.");
      array_push($arr["favourite"],$callback);
      echo json_encode($arr);
    }    

  }

  public function getOfferTabInformation()
  {
      $query_where=" WHERE a.is_policy_set = 1 AND b.status = 1 GROUP BY b.restaurant_id HAVING COUNT(b.restaurant_id) > 0 ORDER BY RAND() LIMIT 10";

      $query="select a.id as rest_id,a.logo, a.slider_image, a.restaurant_name,a.address1,a.address2,a.city,a.domain,a.latitude,a.longitude,a.postcode,a.business_tel, a.accept_reservation FROM restaurant a INNER JOIN restaurant_offer b On a.id = b.restaurant_id $query_where";
      $this->mysqli->set_charset("utf8");
      $result = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
      $arr["app"] = array();
      if($result->num_rows > 0) 
      {
        while($row = $result->fetch_assoc()) 
        {
            $resturent=array();           
            $resturent['rest_id']=$row['rest_id'];            
            $resturent['logo']=$row['logo'];
            $resturent['slider_image']= rtrim($row['slider_image'], ',');
            $resturent['restaurant_name']=$row['restaurant_name'];
            $resturent['address1']=$row['address1'];
            $resturent['address2']=$row['address2'];
            $resturent['city']=$row['city'];
            $resturent['domain']=$row['domain'];
            $resturent['latitude']=$row['latitude'];
            $resturent['longitude']=$row['longitude'];
            $resturent['postcode']=$row['postcode'];
            $resturent['business_tel']=$row['business_tel'];
            $resturent['accept_reservation']=($row['accept_reservation']!=0) ? 1 : 0;
            $resturent['accept_collection']= self::getPolicyStatus($row['rest_id'], "Collection");
            $resturent['accept_delivery']= self::getPolicyStatus($row['rest_id'], "Delivery");
            $resturent['restuarent_schedule']=self::getResturentSchedule($row['rest_id']);
            $resturent['available_cuisine']=self::getCuisine($row['rest_id']);
            $resturent['discount']=self::getDiscount($row['rest_id']);
            $resturent['order_policy']=self::getRestaurantOrderPolicy($row['rest_id']);
            $resturent['offer']=self::getOfferInformation($row['rest_id']);
            
            array_push($arr["app"],$resturent);
        }
        $json_response = json_encode($arr);
        echo $json_response;
      }
      else
      {
        $callback = array('status' => "Failed", "msg" => "Sorry! No information found.");
        array_push($arr["app"],$callback);
        echo json_encode($arr);
      }
  }

  public function getRestaurantIonic()
  {
    header('Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT');
    /*$rest_id = mysqli_real_escape_string($this->mysqli, $_REQUEST['rest_id']);
    $query_where = "";
    $arr["app"] = array();

    $query = "SELECT id as rest_id, restaurant_name, address1,logo, slider_image, address2, city, town, latitude, longitude, postcode, business_tel FROM restaurant WHERE id = '$rest_id'";
    $result = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
    if($result->num_rows > 0)
    {
      while($row = $result->fetch_assoc())
      {
        $data=array();
        $data['restaurant_id'] = $row['rest_id'];
		    $data['logo']=$row['logo'];
        $data['slider_image']= rtrim($row['slider_image'], ',');
        $data['restaurant_name'] = $row['restaurant_name'];
        $data['address'] = $row['address1']." ".$row['address2'].", ".$row['town'].", ".$row['city']." - ".$row['postcode'];
        $data['latitude'] = $row['latitude'];
        $data['longitude'] = $row['longitude'];
        $data['business_tel'] = $row['business_tel'];     
        $data['restuarent_schedule']=self::getResturentSchedule($row['rest_id']);
        $data['discount']=self::getDiscount($resturent_id);
        $data['offer'] = self::getOfferInformation($resturent_id);
        $data['order_policy']=self::getRestaurantOrderPolicy($resturent_id);
        array_push($arr["app"],$data);     
      }
      $json_response = json_encode($arr);
      echo $json_response;
    }
    else
    {
      $callback = array('status' => "Failed", "msg" => "Sorry! No information found.");
      array_push($arr["app"],$callback);
      echo json_encode($arr);
    }*/
     $rest_id = mysqli_real_escape_string($this->mysqli, $_REQUEST['rest_id']);
     if(self::getRestaurantOrderPolicy($rest_id)!=null)
     {
     $query="SELECT a.name, a.id, a.restaurant_id, b.restaurant_name, b.address1, b.logo, b.slider_image, b.address2, b.city, b.town, b.latitude, b.longitude, b.postcode, b.business_tel FROM restaurant_cuisine a INNER JOIN restaurant b ON a.restaurant_id = b.id WHERE a.restaurant_id='".$rest_id."'";
     $this->mysqli->set_charset("utf8");
     $result = $this->mysqli->query($query); 
     $response["app"] = array();
     $arr=array();
     if($result->num_rows > 0)
     {
       while($row = $result->fetch_assoc()) 
       {
         $arr["restaurant_id"] = $row['restaurant_id'];
         $arr['logo']=$row['logo'];
         $arr['slider_image']= rtrim($row['slider_image'], ',');
         $arr['logo']=$row['logo'];
         $arr['slider_image']= rtrim($row['slider_image'], ',');
         $arr['restaurant_name'] = $row['restaurant_name'];
         $arr['address'] = $row['address1']." ".$row['address2'].", ".$row['town'].", ".$row['city']." - ".$row['postcode'];
         $arr['latitude'] = $row['latitude'];
         $arr['longitude'] = $row['longitude'];
         $arr['business_tel'] = $row['business_tel'];
         $arr["Cuisine_name"]=$row['name'];
         $wrp["child"]=array();

         $arr['discount']=self::getDiscount($rest_id);
         $arr['offer'] = self::getOfferInformation($rest_id);
         $arr['order_policy']=self::getRestaurantOrderPolicy($rest_id);
         //$arr['restuarent_schedule']=self::getResturentSchedule($resturent_id);

         $result2=$this->mysqli->query("SELECT id as category_id,name,description FROM dish_category WHERE restaurant_cuisine_id='".$row['id']."' AND status = 1 ORDER BY name ASC");
         $this->mysqli->set_charset("utf8");
         if($result2->num_rows > 0)
         {
           while($row2 = $result2->fetch_assoc())
           {
             $cat = array();
             $cat["Category_ID"]=$row2['category_id'];
             $cat["Category_Name"]=$row2['name'];
             $cat["Category_Description"]=$row2['description'];
             $cat["Dish_Information"]=self::getDishInformation($rest_id,$row2['category_id']);
             // $cat['order_policy']=self::getRestaurantOrderPolicy($resturent_id);
             array_push($wrp["child"],$cat);
           }
               $arr["category"]=$wrp["child"];
               array_push($response['app'], $arr); 
         }
       }
     }
     echo json_encode($response,JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
    }
    else
    {
      $response["app"] = array();
      $callback = array('status' => "Failed", "msg" => "Sorry! No information found.");
      array_push($response["app"],$callback);
      echo json_encode($response);
    }
     $this->obj->ConnectionClose();


  }


  public function getRestaurantBusinessHour()
  {
    $query_where = "";
    $rest_id = mysqli_real_escape_string($this->mysqli, $_REQUEST['rest_id']);
    $order_policy_id = mysqli_real_escape_string($this->mysqli, $_REQUEST['order_policy_id']);
    $weekday = mysqli_real_escape_string($this->mysqli, $_REQUEST['weekday']);
    $days=array(1=>"Monday",2=>"Tuesday",3=>"Wednesday",4=>"Thursday",5=>"Friday",6=>"Saturday",7=>"Sunday");

    if(!empty($order_policy_id))
    {
      $query_where .= " AND a.order_policy_id = '".$order_policy_id."'";
    }
    if(!empty($weekday))
    {
      $query_where .= " AND a.weekday = '".$weekday."'";
    }

    $query = "SELECT a.restaurant_id as rest_id, a.order_policy_id, b.name as policy_name, a.weekday, a.minutes, a.shift FROM restaurant_business_hour a INNER JOIN restaurant_order_policy b ON a.order_policy_id = b.id WHERE a.restaurant_id = '".$rest_id."' $query_where AND a.status = 1 GROUP BY a.weekday ORDER BY a.weekday ASC";

    $result = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
    $arr['business_schedule'] = array();
    if($result->num_rows > 0)
    {
      while($row = $result->fetch_assoc())
      {
        $sch = array();
        $sch['rest_id'] = $row['rest_id'];
        $sch['order_policy_id'] = $row['order_policy_id'];
        $sch['policy_name'] = $row['policy_name'];
        $sch['weekday'] = $row['weekday'];
        $sch['weekday_name'] = $days[$row['weekday']];
        $sch['minutes'] = $row['minutes'];
        $sch['shift'] = $row['shift'];
        array_push($arr['business_schedule'], $sch);
      }    
      echo json_encode($arr);
    }
    else
    {
      $callback = array('status' => "Failed", "msg" => "No information found.");
      array_push($arr['business_schedule'],$callback);
      echo json_encode($arr);
    }


  }



}
?>