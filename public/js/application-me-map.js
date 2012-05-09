var directionDisplay;
var directionsService = new google.maps.DirectionsService();
var map;
var manchester = new google.maps.LatLng(53.48, -2.24);

function initialize_map() {
	directionsDisplay = new google.maps.DirectionsRenderer();
	var myOptions = {
		center: manchester,
		zoom: 6,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	};
	map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
	directionsDisplay.setMap(map);
	
	
	var request = {
	        origin: waypoints.shift, 
	        destination: waypoints.pop,
	        waypoints: waypoints,
	        optimizeWaypoints: true,
	        travelMode: google.maps.DirectionsTravelMode.DRIVING
	    };
    directionsService.route(request, function(response, status) {
      if (status == google.maps.DirectionsStatus.OK) {
        directionsDisplay.setDirections(response);
        var route = response.routes[0];
        var summaryPanel = document.getElementById("directions_panel");
        //summaryPanel.innerHTML = "";
        // For each route, display summary information.
        for (var i = 0; i < route.legs.length; i++) {
          var routeSegment = i + 1;
          //summaryPanel.innerHTML += "<b>Route Segment: " + routeSegment + "</b><br />";
          //summaryPanel.innerHTML += route.legs[i].start_address + " to ";
          //summaryPanel.innerHTML += route.legs[i].end_address + "<br />";
          //summaryPanel.innerHTML += route.legs[i].distance.text + "<br /><br />";
        }
      }
    });
}
$(document).ready(function(){
	initialize_map();
});