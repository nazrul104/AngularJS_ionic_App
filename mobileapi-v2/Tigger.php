<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set('Europe/London');
require 'db_connect.php';
require 'Rest.inc.php';
if(isset($_REQUEST['funId']))
{
	switch($_REQUEST['funId'])
	{
		case 1:
			
		break;
		case 2:
			require("order/OrderOnline.php");
			$obj=new OrderOnline();	
			$obj->RestaurantDish();
		break;
			
		case 3:
			require("login/ManageUser.php");
			$obj=new ManageUser();	
			$obj->Login();
		break;

		case 4:
			require("login/ManageUser.php");
			$obj=new ManageUser();	
			$obj->ForgetPassword();
		break;

		case 5:
			require("order/OrderOnline.php");
			$obj=new OrderOnline();	
			$obj->getresturentInformation();
		break;
		case 6:
			require("order/OrderOnline.php");
			$obj=new OrderOnline();	
			$obj->searchResturent();
			break;
		case 7:
			require("login/UserProfile.php");
			$obj=new UserProfile();	
			$obj->profile();
			break;
		case 8:
			require("login/ManageUser.php");
			$obj=new ManageUser();	
			$obj->RegisterUser();
			break;
		case 9:
			require("reservation/reservation.php");
			$obj=new Reservation();	
			$obj->PostReservation();
			break;
		case 10:
			require("login/ManageUser.php");
			$obj=new ManageUser();	
			$obj->ResetPassword();
			break;

		case 11:
			require("setting/setting.php");
			$obj=new Setting();	
			$obj->ResturentSchedule();
			break;

		case 12:
			require("order/OrderProcess.php");
			$obj=new OrderProcess();	
			$obj->CreateNewOrder();
			break;

		case 13:
			require("order/OrderOnline.php");
			$obj=new OrderOnline();	
			$obj->getPreviousOrders();
			break;

		case 14: 
			require("order/OrderOnline.php");
			$obj=new OrderOnline();	
			$obj->getPreviousOrderDetails();
			break;

		case 15: 
			require("login/UserProfile.php");
			$obj = new UserProfile();
			$obj->updateProfileInfo();
			break;
		case 16: 
			require("order/OrderOnline.php");
			$obj = new OrderOnline();
			$obj->getUserFavouriteList();
			break;

		case 17:
			require("order/OrderOnline.php");
			$obj = new OrderOnline();
			$obj->getOfferTabInformation();
			break;
		case 18:
			require('setting/setting.php');
			$obj = new setting();
			$obj->calculateLatLong();
			break;
		case 19:
			require('order/OrderOnline.php');
			$obj = new OrderOnline();
			$obj->getRestaurantIonic();
			break;


		/*Update restaurant table with logo, slider_image, accept reservation information from backofffice*/


		case 20:
			require("setting/setting.php");
			$obj = new setting();
			$obj->updateRestaurantLogo();
			break;
		case 21:
			require("setting/setting.php");
			$obj = new setting();
			$obj->updateRestaurantSliderImage();
			break;
		case 22:
			require("setting/setting.php");
			$obj = new setting();
			$obj->updateRestaurantReservationInfo();
			break;
		case 23:
			require("setting/setting.php");
			$obj = new setting();
			$obj->updasteDeliveryPostcodeList();
			break;

		/***********Update From Backoffice ENDS***************/

		case 24: //incomplete
			require('order/OrderOnline.php');
			$obj = new OrderOnline();
			$obj->getRestaurantBusinessHour();
			break;
		case 25:
			require('order/OrderOnline.php');
			$obj = new OrderOnline();
			$obj->getRestaurantDeliveryArea();
			break;
			
		/**************** RESERVATION GET AND UPDATE ***************/
		
		case 26:
			require('reservation/reservation.php');	
			$obj = new Reservation();
			$obj->getReservation();
			break; 
		case 27:
			require('reservation/reservation.php');	
			$obj = new Reservation();
			$obj->updateReservationStatus();
			break;
			
		case 28:
			//error_reporting(E_ALL);
			//var_dump($_REQUEST);
			require("order/OrderProcess.php");
			$obj=new OrderProcess();	
			$obj->updateOrder();
			break;
			
		case 29:
		require("order/OrderProcess.php");
			$obj=new OrderProcess();	
			$obj->CheckPaypalOrderStatusForIonic();
			break;
	}
}
?>