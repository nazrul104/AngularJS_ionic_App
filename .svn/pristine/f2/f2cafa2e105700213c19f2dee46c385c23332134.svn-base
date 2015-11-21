angular.module('SRS_API', [])
.controller('MenuList', function($scope, $locale,$http) {

	$scope.Itemmenu=function(rid)
	{
			
		$http.post('Tigger.php', {rest_id:rid,funId:2}).
		  success(function(data, status, headers, config) {
		    console.log(data);
		  }).
		  error(function(data, status, headers, config) {
		  	console.log(status);
		  });
	}
});