var directionDisplay;
var directionsService = new google.maps.DirectionsService();
var poly;
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
	
	initialize_overlay();

}
function initialize_overlay(){
	
	var polyOptions = {
		      strokeColor: '#000000',
		      strokeOpacity: 1.0,
		      strokeWeight: 3
    }
    poly = new google.maps.Polyline(polyOptions);
    poly.setMap(map);

    var path = poly.getPath();
	
	for(var i = 0; i < waypoints.length; i++){
		
		// Because path is an MVCArray, we can simply append a new coordinate
	    // and it will automatically appear
		//console.log(waypoints[i]['location']);
		var latlng = new google.maps.LatLng(waypoints[i]['lat'], waypoints[i]['lng']);
	    path.push(latlng);

	    // Add a new marker at the new plotted point on the polyline.
	    var marker = new google.maps.Marker({
	      position: latlng,
	      title: waypoints[i]['title'],
	      map: map
	    });
	}
	
	
}
$(document).ready(function(){
	initialize_map();
});