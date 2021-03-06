var directionDisplay;
var directionsService = new google.maps.DirectionsService();
var poly;
var map;
var focus;

function initialize_map() {
	focus = new google.maps.LatLng(waypoints[0]['lat'],waypoints[0]['lng']);
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
    
    var trusted_marker_pinColor = "97ec7d";
    var trusted_marker_pinImage = new google.maps.MarkerImage("http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|" + trusted_marker_pinColor,
        new google.maps.Size(21, 34),
        new google.maps.Point(0,0),
        new google.maps.Point(10, 34));
    
    var untrusted_marker_pinColor = "FE7569";
    var untrusted_marker_pinImage = new google.maps.MarkerImage("http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|" + untrusted_marker_pinColor,
        new google.maps.Size(21, 34),
        new google.maps.Point(0,0),
        new google.maps.Point(10, 34));
    
    var marker_pinShadow = new google.maps.MarkerImage("http://chart.apis.google.com/chart?chst=d_map_pin_shadow",
        new google.maps.Size(40, 37),
        new google.maps.Point(0, 0),
        new google.maps.Point(12, 35));
    
    
	for(var i = 0; i < waypoints.length; i++){
		
		// Because path is an MVCArray, we can simply append a new coordinate
	    // and it will automatically appear
		//console.log(waypoints[i]['location']);
		var latlng = new google.maps.LatLng(waypoints[i]['lat'], waypoints[i]['lng']);
	    path.push(latlng);
	    latlngbounds.extend(latlng);

	    // Add a new marker at the new plotted point on the polyline.
	    var pin_image;
	    var render_this_marker = true;
	    if(waypoints[i]['trusted'] == "Yes"){
	    	pin_image = trusted_marker_pinImage;
	    }else if(waypoints[i]['trusted'] == "Suspect"){
	    	pin_image = untrusted_marker_pinImage;
	    }else{
	    	render_this_marker = false;
	    }
	    if(render_this_marker){
		    var marker = new google.maps.Marker({
		      position: latlng,
		      title: waypoints[i]['title'],
		      map: map,
		      icon: pin_image,
		      shadow: marker_pinShadow
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
	    

	    
	    
	    
	    
	}
	$('.map .report ul').append(waypoint_list);
	map.setCenter(latlngbounds.getCenter());
    map.fitBounds(latlngbounds);
	
	
}
$(document).ready(function(){
	$( "#from, #to" )
		.datetimepicker({ 
			dateFormat: "yy-mm-dd",
			timeFormat: "hh:mm:ss",
			seperator: " ",
			minuiteGrid: 10,
		});
	$('#update_map').click(function(e){
		var start = $('#from').val();
		var end = $('#to').val();
		if(end >= start){
			window.location="/Me/Map/start/" + start + "/end/" + end;
		}else{
			alert("End cannot be before start!");
		}
	});
	if($('#map_canvas').length > 0){
		initialize_map();
	}
});