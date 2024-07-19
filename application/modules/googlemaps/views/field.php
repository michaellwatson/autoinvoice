<style type="text/css">
#submitButton{
	display:none;
}
.marker{
	cursor: pointer;
}
.delete_marker{
	color: red;
	cursor:pointer;
}
.highlight{
	background-color: #99FFFF;
}
.unlocked{
	cursor: pointer;
}

td > i.fa-unlock.unlocked {
  display: none;
}

tr.highlight td > i.fa-unlock.unlocked {
  display: inline;
}
</style>
<?php

    if(isset($listing)){
        if(isset($listing['ad_'.$field['adv_column']])){
            $value =  $listing['ad_'.$field['adv_column']];
        }
    }else{
        $value =  '';

    }

    //$value =  $listing['ad_'.$field['adv_column']];
    

    $field_name = $field['adv_column'];
    $FormsTablesmodel = FormsTablesmodel::findOrFail($field['adv_linkedtableid']);


    if(strtolower($FormsTablesmodel->ft_database_table) == strtolower($field['adv_table'])){
        //are we doing a lookup on the same table?
        if(isset($listing['ad_id'])){
            $value = $listing['ad_id'];
        }
    }

    $field_id                   = $field['adv_id'];
    $Advertfield                = Advertfieldsmodel::find($field_id);

    $adv_config                 = (array)json_decode($Advertfield->adv_config);

    $lat_field      = $adv_config['lat'];
    $lng_field      = $adv_config['lng'];

    $Latfield               = Advertfieldsmodel::findorFail($lat_field);
    $Lngfield               = Advertfieldsmodel::findorFail($lng_field);

    $entry = $this->db->where('ad_id', $value)->get($FormsTablesmodel->ft_database_table)->row_array();
	
   	if(($entry['ad_'.$Latfield->adv_column] == '') || ($entry['ad_'.$Lngfield->adv_column] == '')){
   		
   		//should really pull this in from a linked postode field but i'll come back to that later
   		$address = $entry['ad_No'].' '.$entry['ad_Street'].', '.$entry['ad_Town'].', '.$entry['ad_Postcode'];

   		$url = "https://api.addressy.com/Geocoding/International/Geocode/v1.10/xmla.ws?";
	    $url .= "&Key=" . urlencode('PT11-BY93-CX13-ZU44');
	    $url .= "&Country=" . urlencode('UK');
	    $url .= "&Location=" . urlencode($address);

	    //Make the request to Postcode Anywhere and parse the XML returned
	    $file = simplexml_load_file($url);

	    //Check for an error, if there is one then throw an exception
	    if ($file->Columns->Column->attributes()->Name == "Error") 
	    {
	         throw new Exception("[ID] " . $file->Rows->Row->attributes()->Error . " [DESCRIPTION] " . $file->Rows->Row->attributes()->Description . " [CAUSE] " . $file->Rows->Row->attributes()->Cause . " [RESOLUTION] " . $file->Rows->Row->attributes()->Resolution);
	    }

	    //Copy the data
	    if ( !empty($file->Rows) )
	    {
	        foreach ($file->Rows->Row as $item)
	        {
	            $loc = array('lat'=>$item->attributes()->Latitude[0][0],'lng'=>$item->attributes()->Longitude[0][0]);
	        }
	    }
	    $loc = json_decode(json_encode($loc), true);

	    $update['ad_'.$Latfield->adv_column] = $loc['lat'][0];
	    $update['ad_'.$Lngfield->adv_column] = $loc['lng'][0];
	    $this->db->where('ad_id', $value);
	    $this->db->update($FormsTablesmodel->ft_database_table, $update);

	    $entry['ad_'.$Latfield->adv_column] = $loc['lat'][0];
	    $entry['ad_'.$Lngfield->adv_column] = $loc['lng'][0];
	    
   	}

    ?>
    <script>
    
    var satellite = false;
	var map = '';
	var markers = [];
	var markers_pos = [];
	var marker_ids = []; 
	var currentMarkerId = 0;
	var locked_ids = [];

	function photoMap() {

		var dimensions = $('#dimensions').val();
		switch(dimensions){
			case "400x400":
				$('.modal-dialog').removeClass('modal-lg');
			break;
			case "640x400":
				$('.modal-dialog').addClass('modal-lg');
			break;
			case "720x720":
				$('.modal-dialog').addClass('modal-lg');
			break;
		}
	    var url = $('#url').val();
	    posit = $('#newLat').val()+","+$('#newLng').val();
	    //alert(posit);
	    //alert(map.getZoom());

	    //generate the image url
	    if ($('#newLat').val() != '') {
	        if (satellite == true) {
	            //url = url.replace(/zoom=16/i,'zoom='+map.getZoom());
	            var url = "https://maps.googleapis.com/maps/api/staticmap?key=<?php echo $api_key;?>&size="+$('#dimensions').val()+"&center=" + $('#newLat').val() + " " + $('#newLng').val() + "&markers=color:red|" + $('#newLat').val() + " " + $('#newLng').val() + "&maptype=satellite&zoom=16";
	            url = url.replace(/zoom=16/i, 'zoom=' + map.getZoom());
	        } else {
	            var url = "https://maps.googleapis.com/maps/api/streetview?key=<?php echo $api_key;?>&size="+$('#dimensions').val()+"&location=" + posit + "&heading=" + $('#map_heading').val() + "&pitch=" + $('#map_pitch').val() + "&zoom=3";
	        }
	    } else {
	        var posit = $('#map_position').val();
	        posit = posit.replace('(', '');
	        posit = posit.replace(')', '');
	        if (satellite == true) {
	            var url = "https://maps.googleapis.com/maps/api/staticmap?key=<?php echo $api_key;?>&size="+$('#dimensions').val()+"&center=" + posit + "&markers=color:red|" + posit + "&maptype=satellite&zoom=16";
	            url = url.replace(/zoom=16/i, 'zoom=' + map.getZoom());
	        } else {
	            var url = "https://maps.googleapis.com/maps/api/streetview?key=<?php echo $api_key;?>&size="+$('#dimensions').val()+"&location=" + posit + "&heading=" + $('#map_heading').val() + "&pitch=" + $('#map_pitch').val() + "&zoom=3";
	        }
	    }

	    console.log(url);
	    $('#photoGrabImage').html('<img src="' + url + '">');
	    $('.photo_modal').modal('show');
	    if (satellite == true) {
	        $('.modal-content').addClass('bigModal');
	    } else {
	        $('.modal-content').removeClass('bigModal');
	    }
	    $('.span2').slider('setValue', 90);
	    $('.span3').slider('setValue', 16);
	    $('#url').val(url);
	    $('#url2').val(url);

	    //alert(url);
	}

	function lockMap() {
	    if (satellite == false) {
	        var heading = $('#map_heading').val();
	        var pitch = $('#map_pitch').val();
	        var position = $('#map_position').val();
	        var zoom = $('#map_zoom').val();

	        $.post("<?php echo base_url('index.php/street_view/lockMap');?>", {
	            id: <?php echo $this->uri->segment(3); ?>,
	            map_heading : heading,
	            map_pitch: pitch,
	            map_position: position,
	            map_zoom: zoom
	        }, function(data) {
	            if (data.status == 1) {
	                for (i = 0; i < 3; i++) {
	                    $('#pano').css({
	                        'border': '#3A87B3 3px solid'
	                    });
	                    $('#lockMap').fadeTo('fast', 0.5).fadeTo('fast', 1.0);
	                }
	            }
	            //$('#lockMap').hide();
	            //$('#unlockMap').show();
	        }, "json");
	    } else {
	        var new_lat = $('#newLat').val();
	        var new_lng = $('#newLng').val();
	        var new_zoom = map.getZoom();

	        $.post("<?php echo base_url('index.php/street_view/lockMapSatellite');?>", {
	            id: <?php echo $this->uri->segment(3); ?>,
	            new_lat : new_lat,
	            new_lng: new_lng,
	            new_zoom: new_zoom
	        }, function(data) {
	            if (data.status == 1) {
	                for (i = 0; i < 3; i++) {
	                    $('#pano').css({
	                        'border': '#3A87B3 3px solid'
	                    });
	                    $('#lockMap').fadeTo('fast', 0.5).fadeTo('fast', 1.0);
	                }
	            }
	        }, "json");
	    }
	}

	var locations = Array();

	function codeAddress(address) {
	    //var address = document.getElementById('address').value;
	    geocoder.geocode({
	        'address': address
	    }, function(results, status) {
	        if (status == google.maps.GeocoderStatus.OK) {
	            /*map.setCenter(results[0].geometry.location);
			  var marker = new google.maps.Marker({
				  map: map,
				  position: results[0].geometry.location
			  });*/
	            console.log(results[0].geometry.location);
	        } else {
	            alert('Geocode was not successful for the following reason: ' + status);
	        }
	    });
	}

	function initialize() {
	    satellite = false;
	    $('#slide2').hide();
	    $('#slide1').show();
	    $('#satelliteMsg').hide();

	    locations = [$('#newLat').val(), $('#newLng').val()]
	    //alert(locations);
	    console.log(google.maps.LatLng(locations[0], locations[1]));

	    //alert($('#map_zoom').val());
	    //alert($('#map_heading').val());
	    //alert($('#map_pitch').val());

	    var panoramaOptions = {
			position: new google.maps.LatLng(locations[0], locations[1]),
		 	zoom: parseFloat($('#map_zoom').val()),
		  	pov: {
		    	heading: parseFloat($('#map_heading').val()),
		    	pitch: parseFloat($('#map_pitch').val())
		  	}
		};

	    //map.setMapType(GoogleMap.MAP_TYPE_SATELLITE);
	    //var streetView = new 
	    console.log(panoramaOptions);
	    var panorama = new google.maps.StreetViewPanorama(document.getElementById('pano'), panoramaOptions);
	    var letter = 'https://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=A|FF0000|000000';
	    //alert(locations[1]);
	    //alert(locations[2]);
	    marker = new google.maps.Marker({
	        position: new google.maps.LatLng(locations[0], locations[1]),
	        map: panorama,
	        icon: letter,
	    });
	    
	    
	    setTimeout(function(){

		    google.maps.event.addListener(panorama, 'position_changed', function() {
		        console.log(panorama.getPosition());
		        $('#map_position').val(panorama.getPosition());
		    });

		    google.maps.event.addListener(panorama, 'pov_changed', function() {
		        $('#map_heading').val(panorama.getPov().heading + '');
		        $('#map_pitch').val(panorama.getPov().pitch + '');
		    });

		    google.maps.event.addListener(panorama, 'zoom_changed', function() {
		        $('#map_zoom').val(panorama.getZoom());
		    });

	    }, 1000);
	}

	function loadScript() {
	    var script = document.createElement('script');
	    script.type = 'text/javascript';
	    script.src = 'https://maps.googleapis.com/maps/api/js?key=<?php echo $api_key;?>&v=3.exp&' + 'callback=initialize';
	    document.body.appendChild(script);
	}

	function loadSatellite() {
	    $('#satelliteMsg').show();
	    $('#slide2').show();
	    $('#slide1').hide();
	    satellite = true;
	    if ($('#newLat').val() == '') {
	        <?php
	        if ($house['map_position'] != '') {
	            $positions = str_replace('(', '', $house['map_position']);
	            $positions = str_replace(')', '', $positions);
	            $p = explode(',', $positions); ?>
	            var myLatlng = new google.maps.LatLng( <?php echo $p[0] ?> , <?php echo $p[1] ?> ); <?php
	        } else {
	            ?>
	            var myLatlng = new google.maps.LatLng(locations[0], locations[1]); <?php
	        } ?>
	    } else {
	        var myLatlng = new google.maps.LatLng($('#newLat').val(), $('#newLng').val());
	    }

	    var theZoom = 16;
	    if (($('#newZoom').val() != '') && ($('#newZoom').val() != '0')) {
	        theZoom = $('#newZoom').val();
	        //alert(theZoom);
	        theZoom = parseFloat(theZoom);
	    }
	    var mapOptions = {
	        zoom: theZoom,
	        center: myLatlng,
	        mapTypeId: google.maps.MapTypeId.SATELLITE
	    };

	    map = new google.maps.Map(document.getElementById("pano"), mapOptions);

	    markers.push(new google.maps.Marker({
	        position: myLatlng,
	        map: map,
	    }));
	    
	    <?php foreach($markers as $m){?>

    	new google.maps.Marker({
        	position:  new google.maps.LatLng(<?php echo $m->lat;?>, <?php echo $m->lng;?>),
        	map: map,
    	});

    	<?php } ?>

	    google.maps.event.addListener(map, "dblclick", function(event) {
	        var latitude = event.latLng.lat();
	        var longitude = event.latLng.lng();

	        loadPano(latitude, longitude, true, true);

	    }); //end addListener

	}

	function loadPano(lat, lng, save = false, streetview = false){

		//alert( latitude + ', ' + longitude );
        var mapOptions = {
            zoom: parseFloat($('#map_zoom').val()),
            center: myLatlng = new google.maps.LatLng(lat, lng),
            mapTypeId: google.maps.MapTypeId.SATELLITE,
           	pov : {
	            heading : parseFloat($('#map_heading').val()),
	            pitch : parseFloat($('#map_pitch').val()),
	        }
        };
        map = new google.maps.Map(document.getElementById("pano"), mapOptions);

        $('#newLat').val(lat);
        $('#newLng').val(lng);
        latlng = new google.maps.LatLng(lat, lng);

        map.panTo(latlng);
        var d = new Date();
        //marker.setMap(map);
        var marker = new google.maps.Marker({
            position: myLatlng,
            map: map,
        });

        addMarkers(map, false, true);

        if(save){
	       	$.ajax({
				url: '<?php echo base_url('googlemaps/Googlemaps/saveLatLng');?>', // the URL of the controller method that will handle the AJAX request
				type: 'POST', // use the HTTP POST method
				dataType: 'json', // expect a JSON response
				data: { // the data to be sent in the AJAX request
					lat: lat, // the latitude value to be saved
				    lng: lng, // the longitude value to be saved
				    entry_id: '<?php echo $listing['ad_id'];?>',
				},
				success: function(response) { // the callback function to handle the AJAX response
				    console.log(response); // log the response to the console

				   	markers.push(new google.maps.Marker({
				        position: new google.maps.LatLng(lat, lng),
				        map: map,
				    }));


					markers_pos.push(lat+','+lng);
					marker_ids.push(response.marker_id);
					locked_ids.push(0);
					currentMarkerId = response.marker_id;
					alert(currentMarkerId);
					addMarkers(map, false, true);
				},
				error: function(xhr, status, error) { // the callback function to handle any errors
				    console.error(xhr.responseText); // log the error message to the console
				}
			});
       	}
       	if(streetview){
       		initialize();
       	}

        $('#previewSatellite').attr('src', 'https://maps.googleapis.com/maps/api/staticmap?key=<?php echo $api_key;?>&size=640x490&center=' + $('#newLat').val() + ' ' + $('#newLng').val() + '&markers=color:red|' + $('#newLat').val() + ' ' + $('#newLng').val() + '&maptype=satellite&zoom=' + map.getZoom() + '&time=' + d.getTime());
	}

	window.onload = loadScript;


$(document).ready(function() {

	
	$(document).on("click", ".unlocked", function () {

		var pos = $(this).attr('data-id');
		//alert(pos);
		locked_ids[pos] = 1;

		$(this).removeClass("unlocked");

		$(this).removeClass("fa-unlock").addClass("fa-lock");

		let map_heading = $('#map_heading').val();
		let map_pitch 	= $('#map_pitch').val();
		let map_zoom 	= $('#map_zoom').val();

		$.post( "<?php echo base_url('googlemaps/Googlemaps/lockMap');?>", { 
			
			entry_id: 		'<?php echo $listing['ad_id'];?>',
			map_heading: 	map_heading,
			map_pitch: 		map_pitch,
			map_zoom:    	map_zoom, 
			marker_id: 		currentMarkerId, 

		}, function( data ) {

			if(data.status == 1){
				toastr.success(data.msg);
			}else{
				toastr.error(data.msg);
			}
		}, "json");

	});

	$('#saveImageButton').on('click',function(){

		let checked = $('#useField').is(':checked');
		var l = Ladda.create( this );
		l.start();
		$.post( "<?php echo base_url('googlemaps/Googlemaps/saveGoogleImage');?>", { 
			
			url2:$( "#url2" ).val(), 
			entry_id: '<?php echo $listing['ad_id'];?>',
			useField: checked,
			saveToField: $('#saveToField').val() 

		}, function( data ) {
			l.stop();
			$('#photoGrabModal').modal('hide');
			if(data.status == 1){
				toastr.success(data.msg);
			}else{
				toastr.error(data.msg);
			}
		}, "json");
	});
				
	$('.span2').slider().on('slideStop', function(ev){
    	$('#photoGrabImage').html('<img src="'+$('#url').val()+'&fov='+$(this).val()+'">');
        $('#url2').val($('#url').val()+'&fov='+$(this).val());
	});

    $('.span3').slider().on('slideStop', function(ev){
        var url = $('#url').val();
        //yourString.replace(/(\d+)/g, "zoom=$1");
        //url = url.replace(/zoom=\[\d+\]/g,'zoom='+$(this).val());
        url = url.substring(0, url.length - 2);
        url+=$(this).val();
        //alert(url);
        $('#photoGrabImage').html('<img src="'+url+'">');
        $('#url2').val(url);
    });

    $(document).on("click", ".marker", function () {

    	let dataid = $(this).attr('data-id');
    	currentMarkerId = $(this).attr('data-sid');

    	var theZoom = 16;
	    if (($('#newZoom').val() != '') && ($('#newZoom').val() != '0')) {
	        theZoom = $('#newZoom').val();
	        //alert(theZoom);
	        theZoom = parseInt(theZoom);
	    }
	    points = (markers_pos[dataid]).split(',');

        $.ajax({
			url: '<?php echo base_url('googlemaps/Googlemaps/getAdditonalMapInfo');?>', // the URL of the controller method that will handle the AJAX request
			type: 'POST', // use the HTTP POST method
			dataType: 'json', // expect a JSON response
			data: { // the data to be sent in the AJAX request
				marker_id: currentMarkerId,
			    entry_id: '<?php echo $listing['ad_id'];?>',
			},
			success: function(response) { // the callback function to handle the AJAX response

				console.log(response.msg.map_heading);

				if (typeof response.msg.map_heading !== 'undefined') {

					//alert(response.msg.map_heading);
					$('#map_heading').val(response.msg.map_heading);
				}

				if (typeof response.msg.map_zoom !== 'undefined') {

					//alert(response.msg.map_zoom);
					$('#map_zoom').val(response.msg.map_zoom);
				}

				if (typeof response.msg.map_pitch !== 'undefined') {

					//alert(response.msg.map_pitch);
					$('#map_pitch').val(response.msg.map_pitch);
				}

				loadPano(points[0], points[1], false, true);

			},
			error: function(xhr, status, error) { // the callback function to handle any errors
			    console.error(xhr.responseText); // log the error message to the console
			}
		});

	    /*
    	var mapOptions = {
	        zoom: theZoom,
	        center: new google.maps.LatLng(points[0], points[1]),
	        mapTypeId: google.maps.MapTypeId.SATELLITE
	    };

	    map = new google.maps.Map(document.getElementById("pano"), mapOptions);

	    markers.push(new google.maps.Marker({
	        position: new google.maps.LatLng(markers_pos[dataid]),
	        map: map,
	    }));
	    */
		

    });

    
    $(document).on("click", ".delete_marker", function () {
    	let sid = $(this).data('sid');
    	let id = $(this).data('id');
    	//alert(sid); 
    	$('.row_'+sid).remove();
    	console.log(markers[id]);
    	markers[id].setMap(null);
    	google.maps.event.trigger(map, 'resize');

    		$.ajax({
				url: '<?php echo base_url('googlemaps/Googlemaps/deleteLatLng');?>', // the URL of the controller method that will handle the AJAX request
				type: 'POST', // use the HTTP POST method
				dataType: 'json', // expect a JSON response
				data: { // the data to be sent in the AJAX request
					sid: sid,
				    entry_id: '<?php echo $listing['ad_id'];?>',
				},
				success: function(response) { // the callback function to handle the AJAX response

					markers.splice(id,1);
					markers_pos.splice(id,1);
					marker_ids.splice(id,1);
					locked_ids.splice(id,1);
					addMarkers(map, false, true);

				},
				error: function(xhr, status, error) { // the callback function to handle any errors
				    console.error(xhr.responseText); // log the error message to the console
				}
			});
    });

    $("#useField").bootstrapSwitch({
    	"offText" : "File", 
    	"onText" : "Field",
    	"onSwitchChange" : function(event, state){
            if(state){
            	$('#saveToField').attr('disabled', false);
            }else{
            	$('#saveToField').attr('disabled','disabled');
            }
        },
    });

});

function addMarkers(map, addtoList = true, draw = true){

	$('.markers').html('');
	if(addtoList){
	markers_pos.push(locations[0]+','+locations[1]);

	marker_ids.push(0);
	//check default locked status
	locked_ids.push(0);

	markers.push(new google.maps.Marker({
        position: new google.maps.LatLng(locations[0], locations[1]),
        map: map,
    }));

	<?php foreach($markers as $m){?>

    	markers.push(new google.maps.Marker({
        	position:  new google.maps.LatLng(<?php echo $m->lat;?>, <?php echo $m->lng;?>),
        	map: map,
    	}));

    	markers_pos.push('<?php echo $m->lat;?>,<?php echo $m->lng;?>');

    	marker_ids.push(<?php echo $m->id;?>);

    	locked_ids.push(<?php echo $m->locked;?>);

    <?php } ?>
	}

    if(draw){
	    let html = '<table style="width:100%;">';
	  	//alert(markers_pos.length);
	  	let highlight = '';
	  	let lock_status = '';
	    
		for (let i = 0; i < markers_pos.length; i++) {

			if(marker_ids[i] == 0){

				if(currentMarkerId == marker_ids[i]){
					highlight = 'highlight';
				}else{
					highlight = '';
				}
				lock_status = (locked_ids[i]) ? `fa-lock` : `fa-unlock unlocked`;

				html += `<tr class="row_`+marker_ids[i]+` `+highlight+`" data-sid="`+marker_ids[i]+`"><td><a class="marker" data-id="`+i+`"><img src="<?php echo base_url('assets/img/google_maps_icon.png');?>" width="30px"> ${markers_pos[i]}</a></td><td><i class="fa `+lock_status+`" aria-hidden="true" data-id="0"></i>
</td><td></td></tr>`;
			}else{

				if(currentMarkerId == marker_ids[i]){
					highlight = 'highlight';
				}else{
					highlight = '';
				}
				lock_status = (locked_ids[i]) ? `fa-lock` : `fa-unlock unlocked`;

				html += `<tr class="row_`+marker_ids[i]+` `+highlight+`"><td><a class="marker" data-id="`+i+`" data-sid="`+marker_ids[i]+`"><img src="<?php echo base_url('assets/img/google_maps_icon.png');?>" width="30px"> ${markers_pos[i]}</a></td><td><i class="fa `+lock_status+`" aria-hidden="true" data-id="`+i+`"></i>
</td><td><i class="fa fa-times delete_marker" aria-hidden="true" data-id="`+i+`" data-sid="`+marker_ids[i]+`"></i></td></tr>`;
			}
		  	
		}

		html += '</table>';

		$('.markers').html(html);
	}

}
setTimeout(function(){
	addMarkers(map);
}, 1000);
</script>

<div class="row">
	<div class="col-sm-12">

		<input type="hidden" id="map_heading" name="map_heading" value="<?php if($house['map_heading'] != ''){ echo $house['map_heading']; }else{ echo '34'; }  ?>" />
	    <input type="hidden" id="map_pitch" name="map_pitch" value="<?php if($house['map_pitch'] != ''){ echo $house['map_pitch']; }else{ echo '10'; }  ?>" />
	    <input type="hidden" id="map_position" name="map_position"  value="<?php echo $entry['ad_'.$Latfield->adv_column].','.$entry['ad_'.$Lngfield->adv_column];?>"/>
	    <input type="hidden" id="map_zoom" name="map_zoom"  />

	    <input type="hidden" id="newLat" value="<?php echo $entry['ad_'.$Latfield->adv_column];?>">
		<input type="hidden" id="newLng" value="<?php echo $entry['ad_'.$Lngfield->adv_column];?>">
		<input type="hidden" id="newZoom" value="<?php echo $house['new_zoom']?>">

        <a href="javascript:initialize();">Streetview</a> | <a href="javascript:loadSatellite();">Satellite</a>
        <span id="satelliteMsg" style="display:none;">Double Click on the map to reposition the centre</span>
        <div id="pano" style="height: 500px;width:100%;float:left;position:relative;background-color:#F1F1F1;<?php if($house['map_position']!=''){?>border:#3A87B3 5px solid<?php }?>"></div>
        <img id="previewSatellite" style="width: 100px;position: absolute;right: 0px;margin-top: 20px;margin-right: 15px;">
        <!--
        <a id="lockMap" name="lockMap" class="btn btn-primary pull-right" onclick="lockMap();" title="Lock Map Position">
        	<i class="fa fa-lock"></i>
        </a>
    	-->
    	<a id="photoMap" name="photoMap" class="btn btn-primary pull-right" onclick="photoMap();" title="Take a Photo of Map Position">
    		<i class="fa fa-camera"></i>
    	</a>

    </div>
    <div class="col-sm-3 col-md-3">
    	<div class="card">
  			<div class="card-body">
    			<h4>Markers</h4>

    			<div class="markers"></div>
    		</div>
    	</div>
    </div>
</div>


<div class="modal photo_modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">

      	<h4 class="modal-title">Street View Image Grab</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        
      </div>
      <div class="modal-body" style="text-align:center;">

      	<div class="row">
	      	<div class="col-sm-12 col-md-12">
		      	<select class="form-control" name="dimensions" id="dimensions" onchange="photoMap()">
				  	<option value="400x400">Small Square 400x400</option>
				  	<option value="640x400">Small Rectangle 640x400</option>
				  	<option value="720x720">Square 720x720</option>
				</select>
			</div>
		</div>

        <table>
	        <tr>
		        <td>
		        	<div id="photoGrabImage">
		        	</div>
		        </td>
		        <td>
		        	<div id="slide1">
		          		<input type="text" class="span2" value="" data-slider-min="10" data-slider-max="120" data-slider-step="1" data-slider-value="90" data-slider-orientation="vertical" data-slider-selection="after" data-slider-tooltip="hide">
		        	</div>
		        	<div id="slide2">
		          		<input type="text" class="span3" value="" data-slider-min="16" data-slider-max="20" data-slider-step="1" data-slider-value="16" data-slider-orientation="vertical" data-slider-selection="after" data-slider-tooltip="hide">
		        	</div>
		        </td>
	        </tr>
        </table>
        <input id="url" type="hidden" value="" />
        <input id="url2" type="hidden" value="" />

      </div>
      <div class="modal-body" style="text-align:left;">
      	<h4 class="modal-title">Select destination</h4>

      	<div class="row">
	      	<div class="col-sm-3 col-md-3 pt-1">
	      		<input type="checkbox" name="useField" id="useField">
	      	</div>
	      	<div class="col-sm-9 col-md-9">
		      	<select class="form-control" name="saveToField" id="saveToField" disabled="disabled">
				  <option selected>Please Select</option>
				  	<?php foreach($fields as $f){?>
				  	<option value="<?php echo $f->adv_id;?>"><?php echo $f->adv_text;?></option>
					<?php } ?>
				</select>
			</div>
		</div>


      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" data-style="expand-left" id="saveImageButton" class="btn btn-primary ladda-button">
        	<span class="ladda-label">Save Image</span>
        </button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->