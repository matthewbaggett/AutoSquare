var directionDisplay;
var directionsService = new google.maps.DirectionsService();
var poly;
var map;
var focus = new google.maps.LatLng(waypoints[0]['lat'],waypoints[0]['lng']);

function initialize_map() {
	directionsDisplay = new google.maps.DirectionsRenderer();
	var myOptions = {
		center: focus,
		zoom: 14,
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
	
    var latlngbounds = new google.maps.LatLngBounds();
    
    var waypoint_list = '';
    
	for(var i = 0; i < waypoints.length; i++){
		
		// Because path is an MVCArray, we can simply append a new coordinate
	    // and it will automatically appear
		//console.log(waypoints[i]['location']);
		var latlng = new google.maps.LatLng(waypoints[i]['lat'], waypoints[i]['lng']);
	    path.push(latlng);
	    latlngbounds.extend(latlng);

	    // Add a new marker at the new plotted point on the polyline.
	    var marker = new google.maps.Marker({
	      position: latlng,
	      title: waypoints[i]['title'],
	      map: map
	    });
	   
	    waypoint_list += '' +
	    	'<li>' +
	    	'	<ul id="waypoint-' + waypoints[i]['id'] + '">' +
	    	'		<li class="id">' + waypoints[i]['id'] + '</li>' +
	    	'		<li class="time">' + waypoints[i]['time'] + '</li>' +
	    	'		<li class="speed">' + waypoints[i]['speed'] + '</li>' +
	    	'		<li class="bearing">' + waypoints[i]['bearing'] + '&deg;</li>' + 
	    	'	</ul>' +
	    	'</li>';
	    
	    
	    
	}
	$('.map .report ul').append(waypoint_list);
	map.setCenter(latlngbounds.getCenter());
    map.fitBounds(latlngbounds);
	
	
}
$(document).ready(function(){
	$( "#from, #to" )
		.datepicker()
		.datepicker( "option", "dateFormat","yy/mm/dd");

	initialize_map();
});