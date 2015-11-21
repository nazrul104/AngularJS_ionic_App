angular.module('smartrestaurantsolutions', ['ionic','ngCordova','ionicApp.controllers','restuarent.services'])
.run(function($ionicPlatform) {
  $ionicPlatform.ready(function() {
    // Hide the accessory bar by default (remove this to show the accessory bar above the keyboard
    // for form inputs)

/*    if(window.StatusBar) {
      StatusBar.styleDefault();
    }*/
  });
})
.config(function($stateProvider, $urlRouterProvider,$httpProvider) {

   $stateProvider
    .state('search', {
      url: '/search',
      templateUrl: 'templates/search.html'
    })
    .state('settings', {
      url: '/settings',
      templateUrl: 'templates/settings.html'
    })
    .state('tabs', {
      url: "/tab",
      abstract: true,
      templateUrl: "templates/tabs.html",
      controller: 'Navleft'
    })

    .state('tabs.home', {
      url: "/home",
      views: {
        'user-tab': {
          templateUrl: "templates/home.html",
          controller: 'PayPalSuccess'
        }
      }
    })

    .state('tabs.page', {
      url: "/page/:id",
      views: {
        'home-tab': {
            templateUrl: "templates/about.html",
            controller: 'HomeTabCtrl'
        }
      }
    })


    .state('tabs.menu', {
      url: "/menu",
      views: {
        'menu-tab': {
          templateUrl: "templates/menu.html",
          controller: 'OrderOnlineTabCtrl'
        }
      }
    })
     .state('tabs.login', {
      url: "/login/:id",
      views: {
        'login-tab': {
          templateUrl: "templates/login.html",
           controller: 'ManageUser'
        }
      }
    })
    .state('tabs.cart', {
      url: "/cart",
      views: {
        'cart-tab': {
          templateUrl: "templates/cart.html",
           controller: 'cart'
        }
      }
    })
    
    .state('tabs.navstack', {
      url: "/navstack",
      views: {
        'about-tab': {
          templateUrl: "templates/nav-stack.html"
        }
      }
    })

      .state('tabs.registration', {
      url: "/registration/:id",
      views: {
        'registration-tab': {
           templateUrl: "templates/login.html",
           controller: 'ManageUser'
        }
      }
    })

      .state('tabs.signout', {
      url: "/signout/:id",
      views: {
        'signout-tab': {
           templateUrl: "templates/login.html",
           controller: 'ManageUser'
        }
      }
    })

    .state('tabs.reservation', {
      url: "/reservation",
      views: {
        'reservation-tab': {
          templateUrl: "templates/reservation.html",
           controller: 'ReservationCtrl'
        }
      }
    })

    .state('tabs.contact', {
      url: "/contact",
      views: {
        'contact-tab': {
          templateUrl: "templates/contact.html",
           controller: 'ContactCtrl'
        }
      }
    })

    .state('tabs.process', {
      url: "/process",
      views: {
        'cart-tab': {
          templateUrl: "templates/cart_preview.html",
           controller: 'cart'

        }
      }
    })
      .state('tabs.anyoffer', {
      url: "/anyoffer",
      views: {
        'cart-tab': {
          templateUrl: "templates/anyoffer_view.html",
           controller: 'cart'

        }
      }
    })
      .state('tabs.deliveryarea', {
      url: "/deliveryarea",
      views: {
        'dev-tab': {
          templateUrl: "templates/cart_deliveryarea.html",
           controller: 'cart'

        }
      }
    })
    .state('tabs.verification', {
      url: "/verification",
      views: {
        'cart-tab': {
          templateUrl: "templates/verification.html",
           controller: 'cart'
        }
      }
    })

    .state('tabs.success', {
      url: "/success",
      views: {
        'user-tab': {
          templateUrl: "templates/cart_success.html",
           controller: 'cart'

        }
      }
    })

    .state('tabs.reset', {
      url: "/reset/:id",
      views: {
        'user-tab': {
          templateUrl: "templates/login.html",
           controller: 'ManageUser'

        }
      }
    })

    .state('tabs.about', {
      url: "/about/:id",
      views: {
        'user-tab': {
          templateUrl: "templates/login.html",
           controller: 'ManageUser'

        }
      }
    })
        .state('tabs.gallery', {
      url: "/gallery/:id",
      views: {
        'user-tab': {
          templateUrl: "templates/login.html",
           controller: 'ManageUser'

        }
      }
    })
    .state('tabs.favourite', {
      url: "/favourite/:id",
      views: {
        'user-tab': {
          templateUrl: "templates/login.html",
           controller: 'ManageUser'

        }
      }
    })

     .state('tabs.myaccount', {
      url: "/myaccount/:id",
      views: {
        'user-tab': {
           templateUrl: "templates/login.html",
           controller: 'ManageUser'
        }
      }
    });
     
   $urlRouterProvider.otherwise("/tab/menu");
})

