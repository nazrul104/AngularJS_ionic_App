/*******Author N@zrul; May 2015*******/
angular.module('ionicApp.controllers', [])

.controller('NavCtrl', function($scope, $ionicSideMenuDelegate,$ionicModal, basket) 
{
    $scope.showMenu = function ()
     {
      $ionicSideMenuDelegate.toggleLeft();
    };
    $scope.showRightMenu = function () 
    {
      $ionicSideMenuDelegate.toggleRight();
    };
})
.controller('Navleft', function($scope, $ionicSideMenuDelegate,$ionicModal,$http, basket) 
{

})

.controller('OrderOnlineTabCtrl', function($scope,$http, $ionicPopup,$ionicModal, $timeout,$ionicSlideBoxDelegate, $location, $ionicLoading,basket,$filter, $ionicSideMenuDelegate)
 {

  $scope.message='';
  $scope.slider=["img/banner/banner_img1.jpg","img/banner/banner_img2.jpg","img/banner/banner_img3.jpg"];
  $scope.dish_allergence=["Fish"," ","Nuts","Egg","Milk"," "," ","CRUSTACEANS"];
  $scope.total_amount=basket.cartTotalAmount();
  $scope.total_item= basket.cartDataCounter();
  $scope.str_dish_name="";
  $scope.cartdata=[];
   $scope.badgeitem=basket.cartDataCounter();
  /*###########Create Right Navigation Menu##########*/
   var element = angular.element(document.querySelector('#mymenu'));
   $scope.isLogged=basket.isLoggedIn();
   if(angular.equals($scope.isLogged,"true")==true)
   {
    element.html(createHTML_1());
   }
    if(angular.equals($scope.isLogged,"true")==false){
    element.html(createHTML_0());
   }
   /*########## Loading Menu dish Items ##############*/
   $scope.cart=[];
   $ionicLoading.show({
      template: 'Loading...'
    });

    $http.get(basket.API()+'Tigger.php?funId=19&rest_id='+basket.REST_ID(),{cache:true}).
    success(function(data, status, headers, config)
    { 
              $ionicLoading.hide();
              window.localStorage.setItem("Rsetting",JSON.stringify(data.app[0]));
              $scope.setting=JSON.parse(window.localStorage.getItem("Rsetting"));  
              basket.setBuzTel($scope.setting.business_tel);
              $scope.bizzTel=$scope.setting.business_tel;
              $scope.datalist=data.app; /*assign responce data to view(menu.html)*/
              window.localStorage.setItem("schedule",JSON.stringify(data.app[0].restuarent_schedule));
              window.localStorage.setItem("policy",JSON.stringify(data.app[0].order_policy.policy));
              if (data.app[0].discount.status==1)
               {
                  window.localStorage.setItem("discount",JSON.stringify(data.app[0].discount.off));
              };
              /*####################### SHOW OFFER POPUP ######################*/
              if (data.app[0].offer.status==1&&basket.popup_data()==0)
               {
                    window.localStorage.setItem("offer",JSON.stringify(data.app[0].offer.offer_list));
                    var off=window.localStorage.getItem("offer");
                    var t='';
                    t+='<div class="list">';
                     $scope.datalist=data.app; 
                     $scope.datalist.forEach(function(e, i)
                     {
                        if(i==0)
                        {
                          e.offer.offer_list.forEach(function(element, index){
                            console.log(index);
                          t+='<a class="item item-avatar" href="#"><img style="width:60px" ng-src='+element.image+'> <h2 style="font-size:12px">'+element.offer_title+'</h2><p style="font-size:11px">'+element.description+'</p></a>';
                          });
                        }
                     });

                      data.app[0].discount.off.forEach(function(element, index){
                            console.log(index);
                          t+='<a class="item item-avatar" href="#"><img style="width:60px" ng-src='+element.image+'> <h2 style="font-size:12px">'+element.discount_title+'</h2><p style="font-size:11px">'+element.discount_description+'</p></a>';
                          });


                     t+='</div>';
                   var myPopup = $ionicPopup.show({
                     template:t ,
                     title: 'Special Offers!',
                     subTitle: '',
                     scope: $scope,
                     buttons: [
                       { text: 'Continue' }
                     ]
                   });
                   myPopup.then(function(res) {
                    
                   });
                   $timeout(function() {
                      myPopup.close();
                   }, 21400);
                    basket.POPUP();
                    $scope.HideOfferPopUp=function()
                    {
                      myPopup.close();
                    }
              }
  }).
  error(function(data, status, headers, config)
   {
     $scope.networkerror=1;
     $ionicLoading.hide();
     console.log(status);
  });


/*ADD item in cart*/
$scope.addItemInCart=function(item_id,item_name,item_price,option_name)
{
     if(option_name!=""){
       $scope.str_dish_name=option_name+" "+item_name;
     }
     else
     {
      $scope.str_dish_name=item_name;
     }
    $scope.qty=1;
    $scope.session_id='';
    $scope.session_id=window.localStorage.getItem("session_id");
    $scope.cdata={DishId:item_id,item_name:$scope.str_dish_name,item_price:item_price,DishCount:$scope.qty};
    $scope.odata={DishId:item_id,DishCount:$scope.qty};
    $scope.isGet=0;
    if(basket.getCartData().OrderList!=0)
    {
      basket.getCartData().OrderList.forEach(function(e, i)
      {
          if(e.DishId==item_id)
          {
            $scope.cdata={DishId:item_id,item_name:$scope.str_dish_name,item_price:item_price,DishCount:e.DishCount+1};
            $scope.odata={DishId:item_id,DishCount:e.DishCount+1};
            basket.updateCartdata(i,$scope.cdata,$scope.odata);
            $scope.isGet=1;
          }
      });
      
      if($scope.isGet!=1)
      {
        basket.setCartData($scope.cdata,$scope.odata);
      
      }
      $ionicLoading.hide();
    }
    else
    {
     basket.setCartData($scope.cdata,$scope.odata);
    }

    var element = angular.element(document.querySelector('.ion-ios7-cart'));
    element.html("<span class='cartbadge'>"+basket.cartDataCounter()+"</span>");  
    $scope.total_amount=basket.cartTotalAmount();
    $scope.total_item=basket.cartDataCounter();
}
/*EOF*/

/*Item list toggle*/
   $scope.toggleGroup = function(group)
    {
      if ($scope.isGroupShown(group)) 
      {
        $scope.shownGroup = null;
      } 
      else
      {
        $scope.shownGroup = group;
      }
    };
  $scope.isGroupShown = function(group) 
  {
    return $scope.shownGroup === group;
  };
   
 /*end*/
/*######################OPTION ITEM MODAL POP UP######################*/
  $scope.closeLogin = function() {
    $scope.modal_option.hide();
  };
  /*modal for selecting option*/
    $ionicModal.fromTemplateUrl('templates/modal-option.html',
   {
    scope: $scope
   }).then(function(modal) {
    $scope.modal_option = modal;
  });
    // Triggered in the login modal to close it
  $scope.closeOption = function() {
    $scope.modal.hide();
  };

  $scope.parent_dish_name="";
  $scope.showOption = function(option_id,dish_name)
   {
      $scope.parent_dish_name=dish_name;
      $scope.option_item=[];
      $scope.modal_option.show();
      $scope.datalist.forEach(function(e, i){
            e.category.forEach(function(element, index){
              element.Dish_Information.ItemList.forEach(function(x, y){
                if(x.Dish_id===option_id)
                {
                  $scope.option_item.push(x.options.OptionList);  
                }
              });
            });
      });
  };
/***********END OF OPTION MODAL*************/

/*******************ADD ITEM TO FAV LIST**********************/
    $scope.addFav=function(dish_id)
    {
        if(basket.isLoggedIn()=="false")
        {
           $ionicPopup.alert({ title: 'Alert',cssClass:'btnsubmit',template: "Please login first"});
           return false;
        }
        $ionicLoading.show({template: 'Adding...'});
        if(window.localStorage.getItem("UserDetails"))
        {
            $scope.user=JSON.parse(window.localStorage.getItem("UserDetails"));
            $http.post(basket.API()+'Tigger.php?funId=36&customer_id='+$scope.user.userid+"&rest_id="+basket.REST_ID()+"&dish_id="+dish_id+"&type="+1).
            success(function(data, status, headers, config) 
            {
               $ionicLoading.hide();
            });
        }
        else
        {
            $ionicLoading.hide();
            $ionicPopup.alert({title: 'Alert',cssClass:'btnsubmit',template: "Please login first"});
            return false;
        }
    }
    /*EOF*/
})
.controller('cart', function($scope,$cordovaInAppBrowser,$filter,$rootScope,$http,$ionicPopup,$stateParams, $ionicModal,$timeout,$ionicSlideBoxDelegate, $location, $ionicLoading,basket,$ionicSideMenuDelegate) 
{

  var currentdate = new Date();
  var currentdate2 = new Date();
  var weekday_id=currentdate.getDay();
  var arr=[];

  $scope.session_id='';
  $scope.my={};
  $scope.total_amount=0.00;
  $scope.grand_amount=0.00;
  $scope.min_order=0.00;
  $scope.session_id=window.localStorage.getItem("session_id");
  $scope.isLogged=false;
  $scope.datalist={};
  $scope.dev_option="";
  var tel=basket.getBuzTel().split(',');
  $scope.user=JSON.parse(window.localStorage.getItem("UserDetails"));
  $scope.restTel=tel[0];

  $scope.isRestuarentOpen=basket.getIsRestuarentOpen();

  $scope.open_time='';
  $scope.closed_time='';
  $scope.arrTime=[];
  $scope.timeSegment=0;
  $scope.policy_time=0;
  $scope.inc=0;
  $scope.networkerror=0;
  $scope.btn_placeorder=true;
  $scope.restatus=1;
  $scope.verification_code=0;
  $scope.isCartChecked=false;

  $scope.discount=0.00;
  $scope.obtained_discount=0.00;
  $scope.discount_text="";
  $scope.testvcode=0;
  $scope.eligible_date =  currentdate.toISOString();
  var element = angular.element(document.querySelector('.tab-item tab-item-active'));
  element.html("<span class='cartbadge'>"+basket.cartDataCounter()+"</span>");

  if(basket.GetDiscount()>0)
  {
    $scope.obtained_discount=basket.GetDiscount();
  }
$scope.offer_text=basket.GetOfferText();
    $scope.generateTimeFormate=function()
    {
      var months = new Array();
      months[0] = "January";
      months[1] = "February";
      months[2] = "March";
      months[3] = "April";
      months[4] = "May";
      months[5] = "June";
      months[6] = "July";
      months[7] = "August";
      months[8] = "September";
      months[9] = "October";
      months[10] = "November";
      months[11] = "December";

       var cdate=new Date();
       var year = cdate.getFullYear()
       var month= months[cdate.getMonth()];
       var today= cdate.getDate(); 
       if(month<10)
       {
         month=0+""+month;
       }
       if(today<10)
       {
          today=0+""+today;
       }
       var restdate=month+" "+today.toString()+" "+year.toString();
       return restdate;
    }

   $scope.minDate = $scope.generateTimeFormate();
   $scope.paypal_transection_id='';
    /*###########Create Right Menu##########*/
     var element = angular.element(document.querySelector('#mymenu'));
     $scope.isLogged=basket.isLoggedIn();
     if(angular.equals($scope.isLogged,"true")==true)
     {
       element.html(createHTML_1());
     }
      if(angular.equals($scope.isLogged,"true")==false)
      {
       element.html(createHTML_0());
      }
  /*end*/


  if ($scope.user!=null)
   {
     $scope.my.address=$scope.user.address1;
   }
  $scope.policy=JSON.parse(window.localStorage.getItem("policy")); 
  if(basket.getCartData().OrderList.length!=0)
  {
     $scope.datalist=basket.getCartData().OrderList;
     $scope.total_amount=basket.cartTotalAmount();
  }
  else
  {
    $scope.restatus=0;
  }


 var element = angular.element(document.querySelector('.tab-item tab-item-active'));
 element.html("<span class='cartbadge'>"+$scope.datalist.length+"</span>");
/*##########CART ITEM INCREMENT###############*/
    $scope.updateAdd=function(item_id)
    {
          basket.getCartData().OrderList.forEach(function(e, i)
          {
                  if(e.DishId==item_id)
                  {
                    $scope.cdata={DishId:e.DishId,item_name:e.item_name,item_price:e.item_price,DishCount:e.DishCount+1};
                    $scope.odata={DishId:item_id,DishCount:e.DishCount+1};
                    basket.updateCartdata(i,$scope.cdata,$scope.odata);
                    $scope.isGet=1;
                    $scope.datalist=basket.getCartData().OrderList;
                  }
          });
        $scope.total_amount=basket.cartTotalAmount();
    }
/*################ DECREAMENT OF CART ITEM ################*/
  $scope.updateMinus=function(item_id)
  {
    basket.getCartData().OrderList.forEach(function(e, i)
            {
                if(e.DishId==item_id)
                {
                    if(e.DishCount>1)
                    {
                      $scope.cdata={DishId:e.DishId,item_name:e.item_name,item_price:e.item_price,DishCount:e.DishCount-1};
                      $scope.odata={DishId:item_id,DishCount:e.DishCount-1};
                      basket.updateCartdata(i,$scope.cdata,$scope.odata);
                      $scope.isGet=1;
                      $scope.datalist=basket.getCartData().OrderList;
                    }
                    else
                    {
                     $scope.removecartItem(item_id);
                    }
                }
            });
          $scope.total_amount=basket.cartTotalAmount();
  }
/*# REMOVE CART ITEM #*/
    $scope.removecartItem=function(item_id)
    {
      basket.cartDataRemoveById(item_id);
      if(basket.getCartData().OrderList!=0)
      {
        $scope.datalist=basket.getCartData().OrderList;
      }
      else
      {
        $scope.restatus=0;
      }
      $scope.total_amount=basket.cartTotalAmount();
    }
    /*REMOVE SINGLE ROW*/
    $scope.removeCartRow=function(item_id)
    {
        basket.removeCartRow(item_id);
        if(basket.getCartData().OrderList!=0)
        {
          $scope.datalist=basket.getCartData().OrderList;
        }
        else
        {
          $scope.restatus=0;
        }
        $scope.total_amount=basket.cartTotalAmount();
    }

/*EOF*/
/*############ PUSH OFFERS IN ARRAY*/
$scope.orderOffer=function(order_policy_id)
{
    basket.offerClear();
    if(window.localStorage.getItem("offer"))
    {
        $scope.offer=JSON.parse(window.localStorage.getItem("offer"));
      
        if(basket.cartTotalAmount()!=0)
        {
            $scope.total_amount=basket.cartTotalAmount();
            $scope.offer.forEach(function(e, i){

            if(order_policy_id==e.restaurant_order_policy_id || e.restaurant_order_policy_id==0)
            {

              if ($scope.total_amount>=e.eligible_amount)
               {  
                  basket.offerSave(e);
                  basket.offerShow();
               }
            }
          });
       }
    }
}
/*EOF*/
/*Discount function*/
  $scope.orderDiscount=function(order_policy_id)
  {

      basket.discountClear();
       $scope.discounts=[];
       if(window.localStorage.getItem("discount"))
       {

          $scope.discount=JSON.parse(window.localStorage.getItem("discount"));
         
          $scope.total_amount=basket.cartTotalAmount();
          $scope.discount.forEach(function(e, i){
            if(order_policy_id==e.restaurant_order_policy_id || e.restaurant_order_policy_id==0)
            {

              if ($scope.total_amount>=e.eligible_amount)
               {
                
                  basket.discountSave(e);
               }
            }
          });
       }
  }
/*##End of discount##*/

/*###########SHOW OFFER AND DISCOUNT#############*/

       $scope.DoAccept=function(acceptType,OfferText)
       {
        /*###
              1=accepted offer;
              2=accepted discount;
        ###*/

        if (acceptType==1 && OfferText!='') 
        {

                basket.SetOfferText(OfferText);
                $scope.qty=1;
                var item_id=0;
                var item_price=0.00;
                $scope.session_id='';
                $scope.session_id=window.localStorage.getItem("session_id");
                $scope.cdata={DishId:0,item_name:basket.GetOfferText(),item_price:0,DishCount:$scope.qty};
                $scope.odata={DishId:0,DishCount:$scope.qty};
                //basket.setCartData($scope.cdata,$scope.odata);
                $scope.offer_text=basket.GetOfferText();
                $scope.offers=[];
               $scope.discounts=[];

               if(basket.GetDiscount()>0)
               {
                  basket.SetDiscount(0);
                  $scope.obtained_discount=0;
               }
        };

        if (acceptType==2)
         {

            $scope.discount_text=OfferText;
           if ($scope.discounts[0].discount_type=="Percentage")
            {
              $scope.dis=$scope.total_amount*parseFloat($scope.discounts[0].discount_amount);
              $scope.obtained_discount=$scope.dis/100;
              $scope.grand_amount=$scope.total_amount-$scope.obtained_discount; 
              basket.SetDiscount($scope.obtained_discount);
           };
            if ($scope.discounts[0].discount_type=="Fixed")
             {
              $scope.total_amount=$scope.total_amount-parseFloat($scope.discounts[0].discount_amount);
             };

             if(basket.GetOfferText()!="")
             {
               basket.SetOfferText("");
               $scope.offer_text ="";
             }
        };
           $scope.offers=[];
        $scope.discounts=[];
       }
             $ionicModal.fromTemplateUrl('templates/datemodal.html', 
        function(modal) {
            $scope.datemodal = modal;
        },
        {
        scope: $scope, 
        animation: 'slide-in-up'
        }
    );
     $scope.opendateModal = function() { $scope.datemodal.show();};
     $scope.closedateModal = function(modal) {$scope.datemodal.hide();$scope.my.datepicker = modal;};
     $scope.changeOrderOptionOnSelect=function(policy_id,policy_name,policy_time)
      {
        $scope.policy_time=policy_time;
        basket.setOrderOption(policy_id,policy_name);
        window.localStorage.setItem("selected_policy",policy_name);
        window.localStorage.setItem("selected_policy_time",policy_time);
        window.localStorage.setItem("selected_policy_id",policy_id);
        $scope.dev_option=policy_name;  
        $scope.policy=JSON.parse(window.localStorage.getItem("policy")); 
        $scope.policy.forEach(function(e, i){
        if(e.policy_id==policy_id)
        {
          $scope.min_order=e.min_order;
        }
        });

      }
     $scope.confirmOrderOPtion=function(){$scope.my.order_option=window.localStorage.getItem('selected_policy');}

     $scope.showMSg=function(data)
      {
         var alertPopup = $ionicPopup.alert({
         title: 'Alert',
         cssClass:'btnsubmit',
         template: data
        });
        alertPopup.then(function(res)
        {
        /* console.log('Exception!'+status);*/
        });
      }
     $scope.placeOrder=function()
    {
      /*check whether cart has data or not*/
      if($scope.restatus==0)
      {
          $scope.showMSg('Empty Shopping Cart');
          return false;
      }



         if($scope.dev_option==null)
          {
              $scope.showMSg('Please select  COLLECTION or DELIVERY');
              return false;
          }
          else
          {
                  if(basket.isLoggedIn()=="false")
                  {
                     $location.path('/tab/login/1');
                     return false;
                  }
                     $scope.user=JSON.parse(window.localStorage.getItem("UserDetails"));
                     if($scope.user==null)
                     {
                        $location.path('/tab/login/1');
                        return false;
                     };
            var policy_id=window.localStorage.getItem("selected_policy_id");

            if($scope.dev_option=="Delivery")
            {
              $scope.policy=JSON.parse(window.localStorage.getItem("policy")); 
              $scope.policy.forEach(function(e, i){
                if(e.policy_name==$scope.dev_option)
                {
                 var min_amt=parseInt(e.min_order);
                 var ordered_amount=basket.cartTotalAmount();
                 if(ordered_amount<min_amt)
                 {
                   $scope.showMSg('Minimum order amount: &pound'+min_amt);
                   return false;
                 }
                 else
                 {
              if(basket.offerShow().length>0 || basket.discountShow().length>0)
             {

              $scope.offers=basket.offerShow();
              $scope.discounts=basket.discountShow(); 
              $location.path('/tab/anyoffer');
             }
             else
             {
              $location.path('/tab/process');    
             }
                 }
                }
              });
            }else
            {
              $scope.orderOffer(policy_id);
              $scope.orderDiscount(policy_id);
             
             $scope.scheduleProcess();
             if(basket.offerShow().length>0 || basket.discountShow().length>0)
             {

              $scope.offers=basket.offerShow();
              $scope.discounts=basket.discountShow(); 
              $location.path('/tab/anyoffer');
             }
             else
             {
              $location.path('/tab/process');    
             }
            
            }  
          } 
    }

    $scope.gotoConfirmOrderPage=function()
    {
       $location.path('/tab/process');    
    }
    
    $scope.scheduleProcess=function()
    {
        $scope.schedule=JSON.parse(window.localStorage.getItem("schedule"));
          for (var i = 0; i <$scope.schedule.schedule.length; i++) 
          {

            if($scope.schedule.schedule[i]['weekday_id']==weekday_id)
            {
               

                if($scope.schedule.schedule[i]['list'].length==1)
                {
                  var opening=$scope.schedule.schedule[i]['list'][0]['opening_time'];
                  var closing=$scope.schedule.schedule[i]['list'][0]['closing_time'];     
                }

                if($scope.schedule.schedule[i]['list'].length==2)
                {

                  var opening1=$scope.schedule.schedule[i]['list'][0]['opening_time'];
                  var closing1=$scope.schedule.schedule[i]['list'][0]['closing_time'];     
                  var opening2=$scope.schedule.schedule[i]['list'][1]['opening_time'];
                  var closing2=$scope.schedule.schedule[i]['list'][1]['closing_time'];  

                  $scope.policy_time=window.localStorage.getItem("selected_policy_time");

                  $http.post(basket.CMS_API()+"config.php?x=TimeProcess&opening1="+opening1+"&opening2="+opening2+"&closing1="+closing1+"&closing2="+closing2+"&current_time="+$scope.generateTimeFormate()+"&policy_time="+$scope.policy_time).
                  success(function(data, status, headers, config) 
                  {
                  /* console.log(data);*/
                    if(data!="false")
                    {
                      basket.setDeliveryTime(data);  
                      basket.setIsRestuarentOpen(true);
                    } 
                  });
                }    
            }
          } 
    }

    $scope.checkPaymentOption=function(option) {
     basket.setPaymentOption(option);
    if(option==111)
    {

       $http.post(basket.CMS_API()+'config.php?x=ShowCartData&session_id='+$scope.session_id).
       success(function(data, status, headers, config) 
       {
         
          $scope.total_amount=data.total_amount;
          $scope.grand_amount=data.total_amount-basket.GetDiscount();
          $scope.discount=basket.GetDiscount();
          $scope.offer_text=basket.GetOfferText();
          $scope.user=JSON.parse(window.localStorage.getItem("UserDetails"));
          $scope.payment_status=basket.getPaymentOption();
       });
    }

    if(option==0){
      $scope.paypal_transection_id='';
    }
   }
   $scope.vcode=0;
   $scope.check_verification_code=function()
   {
      var mypop=$ionicPopup.prompt({
   title: 'Verify Code',
   template: 'Enter verification code',
   inputType: 'number',
   inputPlaceholder: ''
 }).then(function(res) {
  if(res!=undefined && res!="")
  {
   $scope.verification_code=res;
   $scope.confirmPlaceOrder();
  }
 });


   }
   /*########Check Paypal Payment status after back########*/
        if(basket.getPayPalStatus()==1)
        {
          var v=window.localStorage.getItem('paypal_status');
        }
    /*end*/
basket.setPaymentOption(-1);
    $scope.confirmPlaceOrder=function()
    {

         if($scope.my.pre_order_delivery_time==undefined)
       {
         $scope.showMSg('Please Select '+$scope.my.order_option+' Time');
           return false;
       }
        if(basket.getPaymentOption()==-1)
        {
           $scope.showMSg('Please Select Payment Option');
           return false;
        }
       

       $ionicLoading.show({
              template: 'Processing...'
            });

            $scope.total_amount=basket.cartTotalAmount();
            $scope.grand_amount=basket.cartTotalAmount()-basket.GetDiscount();
            $scope.discount=basket.GetDiscount();
            $scope.offer_text=basket.GetOfferText();

            $scope.user=JSON.parse(window.localStorage.getItem("UserDetails"));
            $scope.payment_status=basket.getPaymentOption();
            if($scope.payment_status==0)
            {
             if($scope.my.devtime)
             {
              $scope.my.pre_order_delivery_time=$scope.my.devtime;
             }
             $http.post(basket.API()+"Tigger.php?funId=12&rest_id="+basket.REST_ID()+"&user_id="+$scope.user.userid+"&order_policy_id="+basket.getOrderOption()+"&post_code="+$scope.user.postcode+"&OrderList="+JSON.stringify(basket.abcd())+"&address="+$scope.user.address1+"&city="+$scope.user.town+"&payment_option="+basket.getPaymentOption()+"&total_amount="+$scope.total_amount+"&grand_total="+$scope.grand_amount+"&discount="+$scope.obtained_discount+"&offer_text="+$scope.offer_text+"&pre_order_delivery_time="+$scope.my.pre_order_delivery_time+"&comments="+$scope.my.comments+"&payment_status=0&paypal_transection_id="+$scope.paypal_transection_id+"&verification_code="+$scope.verification_code).success(function(data)
                {
                  console.log(data);
                if(data.status=="sms_sent")
                {
                   $scope.vcode = data.code;
                   $scope.testvcode=data.code;
                   console.log($scope.testvcode);
                   $scope.check_verification_code();
                   $ionicLoading.hide();
                };
                if(data.status=="Failed")
                {
                   $scope.showMSg(data.msg);
                    $ionicLoading.hide();
                   return false;
                }
                 if (data.status=="Success")
                 {
                      console.log(data);
                      $ionicLoading.hide();
                      var element = angular.element(document.querySelector('.ion-ios7-cart'));
                      element.html("<span class='cartbadge'>0</span>");
                       $ionicLoading.hide(); 
                      window.localStorage.removeItem("selected_policy");
                      window.localStorage.removeItem("session_id");
                      window.localStorage.removeItem("policy"); 
                      window.localStorage.removeItem("total_amount");
                      basket.setOrderId(data.order_ID);
                      basket.clearCart(); 
                      /*basket.getCartData();*/
                      $location.path('/tab/success');
                 }else{
                  $ionicLoading.hide(); 
                 }
                });

            }
            /*For Paypal*/
             if($scope.payment_status==1)
             {
              
                 basket.setPayPalStatus(1);
                 $ionicLoading.hide();
                var options = {
                location: 'yes',
                clearcache: 'yes',
                toolbar: 'no'
              };
           $ionicLoading.show({
              template: 'Redirecting to Paypal...'
            });

           $http.post(basket.CMS_API()+'payment.php',{user_id:$scope.user.userid,total_amount: $scope.total_amount,grand_total:$scope.grand_amount,rest_id:basket.REST_ID()}).success(function(paypalurl)
           {

            var p_url=paypalurl.split("|");
            $scope.paypal_transection_id=p_url[1];
            basket.setPayPalId($scope.paypal_transection_id);
          
            $cordovaInAppBrowser.open(p_url[0], '_blank', options)
            .then(function(event)
            {

            })
            .catch(function(event) {
            
            });

               $http.post(basket.API()+'Tigger.php?funId=12&rest_id='+basket.REST_ID()+'&user_id='+$scope.user.userid+'&order_policy_id='+basket.getOrderOption()+'&post_code='+$scope.user.postcode+'&OrderList='+JSON.stringify(data)+'&address='+$scope.address+'&city='+$scope.user.city+'&payment_option='+basket.getPaymentOption()+'&total_amount='+$scope.total_amount+'&grand_total='+$scope.grand_amount+'&discount='+$scope.discount+'&offer_text='+$scope.offer_text+'&pre_order_delivery_time='+$scope.my.pre_order_delivery_time+'&comments='+$scope.my.comments+'&payment_status=0&paypal_transection_id='+$scope.paypal_transection_id).success(function(data)
                {
                 if (data.status=="Success")
                 {
                  /*console.log(data.order_ID);
                  console.log(data.payment_status);*/
                 };
            });
                window.localStorage.removeItem("selected_policy");
                window.localStorage.removeItem("session_id");
                window.localStorage.removeItem("policy"); 
                $ionicLoading.hide();
               $location.path('/tab/success');
           });          
      }
    }
    /*####Cash success in details####*/
  
    if(basket.getOrderId()!=0)
    {
     /* console.log(basket.getOrderId());*/
     $ionicLoading.show({
              template: 'Processing...'
            });
        $http.get(basket.API()+'Tigger.php?funId=14&order_id='+basket.getOrderId()).success(function (data) 
        {

           $scope.orderdetails=data;
           console.log($scope.orderdetails);
          $scope.grand_total=parseFloat($scope.orderdetails.order[0].grand_total);
          $ionicLoading.hide();
         });
    }
    /*it's for process page*/

    $scope.my.order_option=window.localStorage.getItem('selected_policy');
    $scope.dev_option= $scope.my.order_option;
    if ($scope.my.order_option==0) {
      $location.path('/tab/cart');    
    };

    /*######################PayPal Functions##################*/

  $rootScope.$on('$cordovaInAppBrowser:loadstart', function(e, event){
console.log('starting....');
  });

  $rootScope.$on('$cordovaInAppBrowser:loadstop', function(e, event){
    // insert CSS via code / file
    $cordovaInAppBrowser.insertCSS({
      code: 'body {background-color:blue;}'
    });

    // insert Javascript via code / file
    $cordovaInAppBrowser.executeScript({
      file: 'script.js'
    });
  });

  $rootScope.$on('$cordovaInAppBrowser:loaderror', function(e, event){

  });

  $rootScope.$on('$cordovaInAppBrowser:exit', function(e, event){
 $cordovaInAppBrowser.close();
  });
  
    /*######################end of paypal#####################*/
    /*paypal check payment by watch in db and dynamically redirect the page to success*/
    $scope.checkpaypal_status=0;
     $scope.paypal_transection_id=basket.getPayPalId();
     console.log($scope.paypal_transection_id);
     console.log('');
     if($scope.paypal_transection_id!='')
     {
        $scope.$watch('checkpaypal_status', function(newVal, oldVal)
         {
          if(newVal!==1)
          {
             $http.get(basket.API()+'Tigger.php?funId=30&rest_id='+basket.REST_ID()+'&transaction_id='+$scope.paypal_transection_id).success(function (largeLoad) {
                $scope.checkpaypal_status=largeLoad;
               /* console.log(largeLoad);*/
                console.log("payment_status:"+largeLoad.orders[0].payment_status);
                if(largeLoad.orders[0].payment_status==1)
                {
                   basket.setPayPalId('');
                   basket.setOrderId(largeLoad.orders[0].order_no);
                   $location.path('/tab/home');    
                }
            });
          }
       
         // console.log($scope.checkpaypal_status);
        });
     }
     /*restuarent schedule and get delivery time*/
  $scope.scheduleProcess();
  if(basket.getDeliveryTime()!="")
  {
    $scope.my.devtime=basket.getDeliveryTime(); 
  }

 $scope.offers=basket.offerShow();
 $scope.offer_text=basket.GetOfferText();
/* console.log($scope.offer_text);*/
 $scope.discounts=basket.discountShow();
})

.controller('PayPalSuccess', function($scope,$http, $ionicModal,basket) 
{
$scope.grand_total=0.00;
 $http.get(basket.API()+'Tigger.php?funId=14&order_id='+basket.getOrderId()).success(function (data) {
   $scope.orderdetails=data;
  console.log($scope.orderdetails);
  $scope.grand_total=parseFloat($scope.orderdetails.order[0].grand_total);
 });
})

.controller('ManageUser', function($scope,$http,$location,$ionicPopup, $ionicLoading,basket,$ionicSideMenuDelegate,$stateParams,$ionicModal) 
{
     $scope.networkerror=0;
    $scope.pageName="";
     $scope.isLogged=basket.isLoggedIn();
    $scope.pageid=$stateParams.id;
    console.log($scope.pageid);
    basket.SetTemp($scope.pageid);
    $scope.user=JSON.parse(window.localStorage.getItem("UserDetails"));

    if($scope.pageid==1){
      $scope.pageName="Login";
    }
/*if($scope.pageid==2){
   $location.path('/tab/login/2');
   $scope.pageName="Registration";
}*/
if($scope.pageid==11){
   $scope.pageName="Forgot Password";
}
if($scope.pageid==12)
{
   $scope.pageName="Forgot Password";
   if($scope.user.town==null){
    $scope.user.town="";     
   }
}
    if($scope.pageid==3)
    {
       $scope.msg=0;
       $scope.pageName="My Account";
    if($scope.isLogged=='false')
    {
     $location.path('/tab/login/1');
    }
    $scope.prvloading=true;
    $http.get(basket.API()+'Tigger.php?funId=13&userid='+$scope.user.userid).success(function(data, status, headers, config)
      {
          if (data.status=="Failed") {
             $scope.msg=1;
              $scope.prvloading=false;
          }else{
             $scope.previousOrders=data.orders; 
              $scope.prvloading=false;
          }
    }).
   error(function(data, status, headers, config)
   {
             $scope.networkerror=1;
   });
    }
    if($scope.pageid==10){
        $ionicLoading.show({
      template: 'Loading...'
    });
          $http.get(basket.API()+'Tigger.php?funId=13&userid='+$scope.user.userid).success(function(data, status, headers, config)
      {
          if (data.status=="Failed") {
             $scope.msg=1;
              $scope.prvloading=false;
               $ionicLoading.hide();
          }else{
             $scope.previousOrders=data.orders; 
              $scope.prvloading=false;
               $ionicLoading.hide();
          }
    }).
   error(function(data, status, headers, config)
   {
             $scope.networkerror=1;
   });
    }
    if($scope.pageid==4){
       $scope.pageName="Change Password";
    }
        if($scope.pageid==8)
        {
           $scope.networkerror=0;
           $ionicLoading.show({
      template: 'Loading...'
    });
         $scope.pageName="About Us";
         $http.get(basket.CMS_API()+"config.php?x=getAboutUs").success(function(data){
         
         var element = angular.element(document.querySelector('#aboutus'));
         element.html(data);
          $ionicLoading.hide();
         }).
         error(function(data, status, headers, config)
         {
              $ionicLoading.hide();
             $location.path('/tab/menu');
        });
    }
        if($scope.pageid==9)
        {
               $ionicLoading.show({
      template: 'Loading...'
    });
         $scope.pageName="Gallery";
         $http.get(basket.CMS_API()+"config.php?x=getGalleryImage").success(function(data){
          $scope.gimage=data;
            $ionicLoading.hide();
         }).
         error(function(data, status, headers, config)
         {
          $ionicLoading.hide();
          $location.path('/tab/menu');
        });
        }
if($scope.pageid==5)
    {
      
       $scope.pageName="My Favourite";
       $ionicLoading.show({
       template: 'Loading...'
    });
    $scope.msg=0;
    $scope.user=JSON.parse(window.localStorage.getItem("UserDetails"));
    console.log($scope.user.userid);
    $http.get(basket.API()+'Tigger.php?funId=16&customer_id='+$scope.user.userid+'&rest_id='+basket.REST_ID()).
    success(function(data, status, headers, config)
    {
       console.log(config);
       $ionicLoading.hide();
       $scope.datalist=data.favourite; 
       if($scope.datalist[0].status=="Failed")
       {
        $scope.msg=1;
       }
    }).
     error(function(data, status, headers, config)
         {
            $ionicLoading.hide();
              $location.path('/tab/menu');
        });
   }
   if($scope.pageid==7)
   {
       basket.setLoggedIn(false);
       $location.path('/tab/login/1');
   }

  if($ionicSideMenuDelegate.isOpenRight()===true)
    {
         $ionicSideMenuDelegate.toggleRight();
    } 
  /*###########Create Right Menu##########*/
    var element = angular.element(document.querySelector('#mymenu'));
     $scope.isLogged=basket.isLoggedIn();
     if(angular.equals($scope.isLogged,"true")==true){
     element.html(createHTML_1());
     }
      if(angular.equals($scope.isLogged,"true")==false){
      element.html(createHTML_0());
     }

  /*end*/
  $scope.user=JSON.parse(window.localStorage.getItem("UserDetails"));
  $scope.login={};
  $scope.registration={};
  $scope.loginAuthorization=function()
  {
 if($scope.login.username!=undefined && $scope.login.password!=undefined){
  
   $ionicLoading.show({
       template: 'Waiting...'
      });
    $http.post(basket.API()+'Tigger.php?funId=3&username='+$scope.login.username+'&password='+$scope.login.password).success(function(data, status, headers, config)
    {

        if (data.status=="Success")
         {
            basket.setLoggedIn(true);
            window.localStorage.setItem("UserDetails",JSON.stringify(data.UserDetails));
            if(basket.cartDataCounter()>0)
            {
              $location.path('/tab/cart');
              $ionicLoading.hide();
            }
            else
            {
              $location.path('/tab/myaccount/3');
              $ionicLoading.hide();
            }
           
          }
          else
          {
            $ionicLoading.hide();
             var alertPopup = $ionicPopup.alert({
                 title: 'Alert!',
                 template: "Invalid email or password"
               });
            return false;
          }
    }).
    error(function(data, status, headers, config)
   {
    $ionicLoading.hide();
   $location.path('/tab/menu');
    });
   }else{
     $ionicLoading.hide();
     $ionicPopup.alert({
                 title: 'Alert!',
                 template: "Please enter valid email and password"
     });
   }
  }
    $scope.reset={};
  $scope.ResetPassword=function()
  {
    
     $scope.user=JSON.parse(window.localStorage.getItem("UserDetails"));

     if($scope.reset.oldpassword==null)
    {
      $scope.showMSg("Please enter old password");
      return false;
    }
        if($scope.reset.newpassword==null)
    {
      $scope.showMSg("Please enter new passowrd");
      return false;
    }

     if($scope.reset.confirmpassword!=$scope.reset.newpassword)
     {
        $scope.showMSg("Please confirm password");
        return false;
     }

 $ionicLoading.show({
       template: 'Waiting...'
      });
     $http.post(basket.API()+'Tigger.php?funId=10&email='+$scope.user.email+'&previouspassword='+$scope.reset.oldpassword+'&newpassword='+$scope.reset.newpassword).success(function(data, status, headers, config)
     {
        $ionicLoading.hide();
         $ionicPopup.alert({
                 title: 'Message!',
                 template: data.msg
               });
        $scope.reset.oldpassword="";
        $scope.reset.newpassword="";
        $scope.reset=[];
    }).
    error(function(data, status, headers, config)
    {
      $ionicLoading.hide();
      $location.path('/tab/menu');

    });
  }
$scope.showMSg=function(data)
{
         var alertPopup = $ionicPopup.alert({
                 title: 'Alert',
                 cssClass:'btnsubmit',
                 template: data
               });
               alertPopup.then(function(res) {
                 console.log('Exception!'+status);
               });
}

    $scope.matchPassword=function()
    {
      if($scope.registration.password!=$scope.registration.password2)
      {
        $scope.showMSg("Password does not match");
        return false;
      }     
    }

$scope.checkString=function(myData)
{

  var stringOnly = /^[A-Za-z]+$/;
  if(!stringOnly.test(myData))
  {
    $scope.showMSg("Invalid name, do not use number or any special character");
    return false;
  }
}
  $scope.registration=function()
  {
    /*Form Validation*/
    

    if($scope.registration.fname==null)
    {
      $scope.showMSg("Please enter your first name");
      return false;
    }
        if($scope.registration.email==null)
    {
      $scope.showMSg("Please enter valid email address");
      return false;
    }
    if($scope.registration.mobile==null)
    {
      $scope.showMSg("Please enter mobile number");
      return false;
    }
     if($scope.registration.postcode==null)
    {
      $scope.showMSg("Please enter your postcode");
      return false;
    }
     if($scope.registration.address1==null)
    {
      $scope.showMSg("Please enter address");
      return false;
    }
     if($scope.registration.postcode==null)
    {
      $scope.showMSg("Please enter your postcode");
      return false;
    }

    if($scope.registration.town==null)
    {
      $scope.showMSg("Please enter name of your city");
      return false;
    }

     if($scope.registration.password==null)
    {
      $scope.showMSg("Please enter a password");
      return false;
    }

    if($scope.registration.password2==null)
    {
      $scope.showMSg("Please confirm your password");
      return false;
    }

     if($scope.registration.password2!=$scope.registration.password)
    {
      $scope.showMSg("Password does not match");
      return false;
    }
 if($scope.registration.password.length<6)
  {
      $scope.showMSg("Password must be 6 to 16");
         return false;
  }
if($scope.registration.lname==null){
  $scope.registration.lname="";
}
 if($scope.registration.address2==null){
  $scope.registration.address2="";
}
 if($scope.registration.tel==null){
  $scope.registration.tel="";
}
if($scope.registration.country==null){
  $scope.registration.country="";
}
    var dob=$scope.registration.dobmonth+" "+$scope.registration.dobday;
    var doa=$scope.registration.admonth+" "+$scope.registration.adday;
  /*Form validation end*/
    $ionicLoading.show({
       template: 'Creating your account...'
      });
      $http.post(basket.API()+'Tigger.php?funId=8&fname='+$scope.registration.fname+'&lname='+$scope.registration.lname+'&email='+$scope.registration.email+'&mobile_no='+$scope.registration.mobile+'&telephone_no='+$scope.registration.tel+'&postcode='+$scope.registration.postcode+'&address1='+$scope.registration.address1+'&address2='+$scope.registration.address2+'&city='+$scope.registration.town+'&country='+$scope.registration.country+'&password='+$scope.registration.password+"&dob_date="+$scope.dob+"&doa="+$scope.doa).success(function(data, status, headers, config)
      {
           $ionicLoading.hide();
           if (data.status=="Success")
           {
               basket.setLoggedIn(true);
              window.localStorage.setItem("UserDetails",JSON.stringify(data.UserDetails));
               var alertPopup = $ionicPopup.alert({
                 title: 'Confirmation',
                 template: data.msg
               });
               alertPopup.then(function(res) {
                $location.path('/tab/myaccount/3');
               });   
            }


           if (data.status=="Failed")
           {
                   var alertPopup = $ionicPopup.alert({
                     title: 'Alert',
                     template: data.msg
                   });
                   alertPopup.then(function(res) {
                     $location.path('/tab/login/1');
                     console.log('Exception!'+status);
                   });

           }
      }).
     error(function(data, status, headers, config)
    {
      $ionicLoading.hide();
       $location.path('/tab/menu');
    });
  }
/*  if($scope.pageid==2){
   $location.path('/tab/registration/2');
    return false;
}
*/
if($scope.pageid==6){
   $scope.pageName="Order Details";
   $scope.grand_total=0.00;
    $ionicLoading.show({
       template: 'Processing...'
      });
  $http.get(basket.API()+'Tigger.php?funId=14&order_id='+basket.getOrderId()).success(function (data) {
  $scope.orderdetails=data;
  $scope.grand_total=parseFloat($scope.orderdetails.order[0].grand_total);
   $ionicLoading.hide();
   }).

   error(function(data, status, headers, config)
   {
     $ionicLoading.hide();
        $location.path('/tab/menu');
  });
}
$scope.PreviousOrderInDetails=function(oid)
{
  basket.setOrderId(oid);
}
/*show modal*/
  $ionicModal.fromTemplateUrl('templates/my-modal.html',
   {
    scope: $scope
  }).then(function(modal) {
    $scope.modal = modal;
  });
    // Triggered in the login modal to close it
  $scope.closeLogin = function() {
    $scope.modal.hide();
  };

  // Open the login modal
  $scope.showmodal = function() {
    $scope.modal.show();
  };
/*end of modal part*/

$scope.UpdateUserDetails=function()
{

if($scope.user.first_name=="")
{
    $scope.showMSg("First name field cannot be empty!");
    return false;
}
if($scope.user.email=="")
{
    $scope.showMSg("Email field cannot be empty!");
    return false;
}
if($scope.user.mobile_no=="")
{
    $scope.showMSg("Mobile no field cannot be empty!");
    return false;
}
if($scope.user.postcode=="")
{
    $scope.showMSg("Postcode field cannot be empty!");
    return false;
}
if($scope.user.address1=="")
{
    $scope.showMSg("Address1 field cannot be empty!");
    return false;
}

if($scope.user.town=="")
{
    $scope.showMSg("City field cannot be empty!");
    return false;
}

  $http.get(basket.API()+"Tigger.php?funId=15&userid="+$scope.user.userid+"&fname="+$scope.user.first_name+"&lname="+$scope.user.last_name+"&email="+$scope.user.email+"&mobile_no="+$scope.user.mobile_no+"&telephone_no="+$scope.user.telephone_no+"&postcode="+$scope.user.postcode+"&address1="+$scope.user.address1+"&address2="+$scope.user.address2+"&city="+$scope.user.town+"&country="+$scope.user.country+"&dob_date="+$scope.user.date_of_birth+"&doa="+$scope.user.date_of_anniversery).
  success(function(data, status, headers, config)
  {
    console.log(data);
    if(data.status=="Success")
    {
         $ionicPopup.alert({
         title: 'Message',
         template: data.msg
       });
         $scope.closeLogin();
    }
    else
    {
       $ionicPopup.alert({
         title: 'Message',
         template: 'Cannot update your profile right now!'
       });
    }
  }).
 error(function(data, status, headers, config)
 {
      $ionicLoading.hide();
      $location.path('/tab/menu');

});
}

/*push item in cart*/
/*push item in cart*/
$scope.str_dish_name="";
$scope.cartdata=[];
$scope.addItemInCart=function(item_id,item_name,item_price,option_name)
{

 if(option_name!=""){
   $scope.str_dish_name=option_name+" "+item_name;
 }
 else
 {
  $scope.str_dish_name=item_name;
 }


    $scope.qty=1;
    $scope.session_id='';
    $scope.session_id=window.localStorage.getItem("session_id");
    $scope.cdata={DishId:item_id,item_name:$scope.str_dish_name,item_price:item_price,DishCount:$scope.qty};
    $scope.odata={DishId:item_id,DishCount:$scope.qty};
    $scope.isGet=0;
    if(basket.getCartData().OrderList!=0)
    {
      basket.getCartData().OrderList.forEach(function(e, i)
      {
          if(e.DishId==item_id)
          {
            $scope.cdata={DishId:item_id,item_name:$scope.str_dish_name,item_price:item_price,DishCount:e.DishCount+1};
            $scope.odata={DishId:item_id,DishCount:e.DishCount+1};
            basket.updateCartdata(i,$scope.cdata,$scope.odata);
            $scope.isGet=1;
          }
      });
      
      if($scope.isGet!=1)
      {
        basket.setCartData($scope.cdata,$scope.odata);
       console.log(basket.getCartData());
      }
      $ionicLoading.hide();
    }
    else
    {
     
       basket.setCartData($scope.cdata,$scope.odata);
       console.log(basket.abcd());

    }

    var element = angular.element(document.querySelector('.ion-ios7-cart'));
    element.html("<span class='cartbadge'>"+basket.cartDataCounter()+"</span>");  
    console.log(element);
    $scope.total_amount=basket.cartTotalAmount();
    $scope.total_item=basket.cartDataCounter();
}
/*end*/
/*end*/
/*Forgot password reset*/
$scope.foo={};
$scope.ForgotPassword=function()
{
  $http.post(basket.API()+'Tigger.php?funId=4&email='+$scope.foo.forgotpass).success(function(data, status, headers, config)
  {
    console.log(data.app.msg);
    $ionicPopup.alert({
                 title: 'Message',
                 template: data.app.msg
               });
  });
}
})

.controller('AccountCtrl', function($scope,$http,  $location, $ionicModal, $ionicLoading,basket,$ionicSideMenuDelegate) 
{
   /* if(basket.isConfigureda()==0){
    $location.path('/tab/menu');
  }*/
  if($ionicSideMenuDelegate.isOpenRight()===true)
    {
         $ionicSideMenuDelegate.toggleRight();
    }
    else{
        $ionicSideMenuDelegate.toggleLeft();
     } 
   $scope.isLogged=basket.isLoggedIn();
   if($scope.isLogged==false){
     $location.path('/tab/login');
     return false;
  }
  /*###########Create Right Menu##########*/
    var element = angular.element(document.querySelector('#mymenu'));
     $scope.isLogged=basket.isLoggedIn();
     if(angular.equals($scope.isLogged,"true")==true){
     element.html(createHTML_1());
     }
      if(angular.equals($scope.isLogged,"true")==false){
      element.html(createHTML_0());
     }

  /*end*/

  $scope.user=JSON.parse(window.localStorage.getItem("UserDetails"));

  if(!window.localStorage.getItem('isLogged'))
  {
     $location.path('/tab/login/1');
     return false;
  }

   $http.get(basket.API()+'Tigger.php?funId=13&userid='+$scope.user.userid).success(function(data, status, headers, config)
    {
        if (data.results.status="Success") 
        {
           $scope.msg=data.results.msg;
           $scope.previousOrders=data.orders; 
        }else{
          $scope.msg="You have no orders";
        }   
    }).
   error(function(data, status, headers, config)
   {
        var alertPopup = $ionicPopup.alert({
                 title: 'Alert!',
                 template: data+" "+status
               });
               alertPopup.then(function(res) {
                 console.log('Exception!'+status);
               });   
   });
/*show modal*/
  $ionicModal.fromTemplateUrl('templates/my-modal.html',
   {
    scope: $scope
  }).then(function(modal) {
    $scope.modal = modal;
  });
    // Triggered in the login modal to close it
  $scope.closeLogin = function() {
    $scope.modal.hide();
  };

  // Open the login modal
  $scope.showmodal = function() {
    $scope.modal.show();
  };
/*end of modal part*/
})


.controller('ContactCtrl', function($scope, basket,$stateParams,$http, $ionicSideMenuDelegate,$location) 
{
   /* if(basket.isConfigured()==0){
    $location.path('/tab/menu');
  }*/
     $scope.page={}
     $scope.rst_tel={};
     $scope.pageTitle="";
     var element = angular.element(document.querySelector('#mymenu'));
     $scope.isLogged=basket.isLoggedIn();
     if(angular.equals($scope.isLogged,"true")==true)
     {
      element.html(createHTML_1());

     }
      if(angular.equals($scope.isLogged,"true")==false){
      element.html(createHTML_0());
     }

 var contact_page = angular.element(document.querySelector('#contactpage'));


   $http.get(basket.CMS_API()+'config.php?x=ContactPage',{cache:true}).
    success(function(data, status, headers, config)
    {
       // $scope.data=data;     
       contact_page.html(data);
    }).
     error(function(data, status, headers, config)
     {
             
    });

       /*Restuarent setting*/
  if (!window.localStorage.getItem("Rsetting")) {
      $http.get(basket.API()+'Tigger.php?funId=19&rest_id='+basket.REST_ID(),{cache:true}).success(function(data, status, headers, config)
      {
        window.localStorage.setItem("Rsetting",JSON.stringify(data.app[0]));
        $scope.setting=JSON.parse(window.localStorage.getItem("Rsetting"));
        $scope.bizzTel=$scope.setting.business_tel;
        var x=$scope.setting.address.replace(/<[^>]+>/gm, '');
        $scope.address = x.replace( /,/g, "");
        $scope.rst_tel= $scope.bizzTel.split(",");
      });
    }else{
      $scope.setting=JSON.parse(window.localStorage.getItem("Rsetting"));
      $scope.bizzTel=$scope.setting.business_tel;
      var x=$scope.setting.address.replace(/<[^>]+>/gm, '');
        $scope.address = x.replace( /,/g, "");
      $scope.rst_tel= $scope.bizzTel.split(",");  
    }
  /*end*/

})

.controller('ReservationCtrl', function($scope,$http, $ionicModal, $timeout,$ionicSlideBoxDelegate,$ionicPopup, $cordovaDatePicker,$location, $ionicLoading,basket,$ionicSideMenuDelegate) 
{
   /* if(basket.isConfigured()==0){
    $location.path('/tab/menu');
  }*/
     $scope.page={}
     $scope.res={};
     $scope.pageTitle="";
     var element = angular.element(document.querySelector('#mymenu'));
     $scope.isLogged=basket.isLoggedIn();
/*minimu date for reservation*/
       var cdate=new Date();
       var year = cdate.getFullYear()
       var month= cdate.getMonth()+1;
       var today= cdate.getDate(); 
       if(month<10)
       {
         month=0+""+month;
       }
       if(today<10)
       {
          today=0+""+today;
       }
       var restdate=year.toString()+"-"+month.toString()+"-"+today.toString();
      $scope.mindate = restdate;

    /*minimum date for reservation*/
     if(angular.equals($scope.isLogged,"true")==true)
     {
      element.html(createHTML_1());

     }
      if(angular.equals($scope.isLogged,"true")==false){
      element.html(createHTML_0());
     }
      $ionicModal.fromTemplateUrl('templates/datemodal.html', 
        function(modal) {
            $scope.datemodal = modal;
        },
        {
        // Use our scope for the scope of the modal to keep it simple
        scope: $scope, 
        // The animation we want to use for the modal entrance
        animation: 'slide-in-up'
        }
    );
     $scope.opendateModal = function() { $scope.datemodal.show();};
     $scope.closedateModal = function(modal)
      {
        $scope.datemodal.hide();
        $scope.res.datepicker = modal;
      };
     /*add reservation*/
     $scope.showMSg=function(data)
{
         var alertPopup = $ionicPopup.alert({
                 title: 'Alert',
                 template: data
               });
               alertPopup.then(function(res) {
                 console.log('Exception!'+status);
               });
}

$scope.guestValidation=function()
{
  if($scope.res.guest<1)
  {
       $scope.showMSg("Number of guest must be at least one");
       $scope.res.guest=1;
       return false;
  }
}
     $scope.addReservation=function()
     {
      

               if($scope.res.fname==null)
                {
                  $scope.showMSg("Please enter your full name");
                  return false;
                }

                if($scope.res.email==null)
                {
                  $scope.showMSg("Please enter valid email address");
                  return false;
                }

                 if($scope.res.mobile==null)
                {
                  $scope.showMSg("Please enter mobile number");
                  return false;
                }

                 if($scope.res.datepicker==null)
                {
                  $scope.showMSg("Please select date");
                  return false;
                }

                 if($scope.res.mytime==null)
                {
                  $scope.showMSg("Please select time");
                  return false;
                }

                 if($scope.res.guest==null)
                {
                  $scope.showMSg("Please enter number of guest");
                  return false;
                }
        $ionicLoading.show({
               template: 'Waiting...'
        });
         $http.post(basket.API()+'Tigger.php?funId=9&fullName='+$scope.res.fname+'&email='+$scope.res.email+'&mobile_no='+$scope.res.mobile+'&reservation_date='+$scope.res.datepicker+'&reservation_time='+$scope.res.mytime+'&guest='+$scope.res.guest+'&special_request='+$scope.res.special_req+'&rest_id='+basket.REST_ID()).success(function(data, status, headers, config)
          {
          if(data.status=="Success")
          {
              var alertPopup = $ionicPopup.alert({
               title: 'Message',
               template: data.msg
             });   
            $scope.res={};
            $ionicLoading.hide();
          }
          else
          {
             $ionicLoading.hide();
             var alertPopup = $ionicPopup.alert({
               title: 'Alert',
               template:"Sorry! reservation failed"
             });   
          }
             
          }).
         error(function(data, status, headers, config)
         {
              var alertPopup = $ionicPopup.alert({
               title: 'Alert',
               template:"No internet connection available"
             });  

         });
     }
     /*end*/
})

.controller('FavCtrl', function($scope,$http, $ionicModal, $timeout,$ionicSlideBoxDelegate, $location, $ionicLoading,basket,$filter, $ionicSideMenuDelegate) 
{
     $scope.user=JSON.parse(window.localStorage.getItem("UserDetails"));
     $scope.page={}
     $scope.pageTitle="";
     var element = angular.element(document.querySelector('#mymenu'));
     $scope.isLogged=basket.isLoggedIn();
     if(angular.equals($scope.isLogged,"true")==true)
     {
      element.html(createHTML_1());

     }
      if(angular.equals($scope.isLogged,"true")==false){
      element.html(createHTML_0());
     }

    var contact_page = angular.element(document.querySelector('#contactpage'));

      $ionicLoading.show({
      template: 'Loading...'
    });
  $scope.user=JSON.parse(window.localStorage.getItem("UserDetails"));
    $http.get(basket.API()+'Tigger.php?funId=16&customer_id='+$scope.user.userid+'rest_id='+basket.REST_ID()).
    success(function(data, status, headers, config)
    {
      console.log(data);
          $ionicLoading.hide();
       $scope.datalist=data.favourite; 
       console.log($scope.datalist);
    }).
     error(function(data, status, headers, config)
     {
         $ionicLoading.hide();
    });
})

.controller('abcd', function($scope,$http, $ionicModal, $timeout,$ionicSlideBoxDelegate, $location, $ionicLoading,basket,$filter, $ionicSideMenuDelegate) 
{

 })
function createHTML_0()
{
//alert(con);
var temp='';
 temp+='<ul class="list">';
                  temp+='<li>';
                temp+='<a class="item" menu-close nav-clear href="#/tab/about/8"><i class="ion-ios7-home"></i> About us</a>';
              temp+='</li>';
               temp+='<li>';
                temp+='<a class="item" menu-close nav-clear href="#/tab/gallery/9"><i class="ion-images"></i> Gallery</a>';
              temp+='</li>';
                temp+='<li>';
                temp+='<a class="item" menu-close nav-clear href="#/tab/login/1"><i class="ion-log-in"></i> Sign In</a>';
              temp+='</li>';
             temp+=' <li>';
               temp+='<a class="item" menu-close nav-clear href="#/tab/registration/2"><i class="ion-compose"></i> Registration</a>';
             temp+='</li>';
            temp+='</ul>';
  return temp;
}


function createHTML_1()
{
//alert(con);
var temp='';
 temp+='<ul class="list">';
               temp+=' <li>';
               temp+=' <a class="item" menu-close nav-clear href="#/tab/myaccount/3"><i class="ion-ios7-person"></i> My Account</a>';
              temp+='</li>';
               temp+='<li>';
                temp+='<a class="item" menu-close nav-clear href="#/tab/about/8"><i class="ion-ios7-home"></i> About us</a>';
              temp+='</li>';
               temp+='<li>';
                temp+='<a class="item" menu-close nav-clear href="#/tab/gallery/9"><i class="ion-images"></i> Gallery</a>';
              temp+='</li>';
        /*        temp+='<li>';
                temp+='<a class="item" menu-close nav-clear href="#/tab/contact"><i class="ion-ios7-location"></i> Contact Us</a>';
              temp+='</li>';*/
            
             /* temp+='<li>';
                temp+='<a class="item" menu-close nav-clear href="#/tab/reset/4"><i class="ion-ios7-compose"></i> Change Password</a>';
              temp+='</li>';
              temp+='<li>';
                temp+='<a class="item" menu-close nav-clear href="#/tab/registration">Order History</a>';
              temp+='</li>';
             temp+=' <li>';
                temp+='<a class="item" menu-close nav-clear href="#/tab/favourite/5"><i class="ion-heart"></i> My Favourite</a>';
              temp+='</li>';*/
               temp+='<li>';
               temp+='<a class="item" menu-close nav-clear href="#/tab/signout/7"><i class="ion-log-in"></i> Sign Out</a>';
              temp+='</li>';
            temp+='</ul>';

  return temp;
}

