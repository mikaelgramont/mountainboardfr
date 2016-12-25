var Lib = Lib || { };

Lib.Maps = {
		map: null,
		
		displayRegion: {
			defaultOptions: {
				center:[],
				zoom:5,
				mapTypeId: null,
				bounds:[],
				mapElementId:'',
				icons:{
				}
			}, // End displayRegion.defaultOptions
			
			options:{},
			
			map:null,
			
			markers: [],
			
			items: [],
			
			itemCount: {},
			
			itemTypeDisplay: {},
			
			infoWindows: [],
			
			showTypes: [],

			init: function(userOptions, callback){
				if (typeof google == 'undefined' || !google.maps) {
					return;
				}
				this.defaultOptions.mapTypeId = google.maps.MapTypeId.HYBRID;
				var that = this;
				var options = this.options = jQuery.extend(this.defaultOptions, userOptions);
				
				if(options.center.length == 2){
					var center = new google.maps.LatLng(options.center[0], options.center[1]);
				} else {
					var center = new google.maps.LatLng((parseFloat(options.bounds[0]) + parseFloat(options.bounds[2]))/2, (parseFloat(options.bounds[1]) + parseFloat(options.bounds[3]))/2);
				}
				
			    var mapOptions = {
			    	zoom: options.zoom,
				    center: center,
				    mapTypeId: options.mapTypeId
				};
				var map = window.map = this.map = new google.maps.Map(document.getElementById(options.mapElementId), mapOptions);

			    var updateItems = function(type) {
			    	Lib.Maps.displayRegion.loadItemsInBounds();
			    };
				
				if(options.bounds.length ==4){
				    var bounds = new google.maps.LatLngBounds(
					   	new google.maps.LatLng(options.bounds[0],options.bounds[1]),
					   	new google.maps.LatLng(options.bounds[2],options.bounds[3])
				    );
				    map.fitBounds(bounds);	
				    google.maps.event.addDomListenerOnce(map, 'bounds_changed', function() {
				    	updateItems('bounds_changed');
					    google.maps.event.addDomListener(map, 'dragend', function() {updateItems('dragend');});
					    google.maps.event.addDomListener(map, 'zoom_changed', function() {updateItems('zoom_changed');});
				    });
			    } else {
				    google.maps.event.addDomListener(map, 'dragend', function() {updateItems('dragend no bounds');});
				    google.maps.event.addDomListener(map, 'zoom_changed', function() {updateItems('zoom_changed no bounds');});
			    }
				
				this.setupPage();
			}, // End displayRegion.init		
			
			setupPage: function(){
				var that = this;
				
				var containerHtml = '		<div id="mapItemTools">\
					<ul class="mapFilters">\
					<'+'/ul>\
				<'+'/div>';
				$('#mapZone').append(containerHtml);
				
				var items = '';
				$('#mapItemsSummary li').each(function(){
					
					var displayType = $(this).find('span.itemType').text();
					var itemType = $(this).attr('class');
					that.showTypes[itemType] = true;
					
					var checked = ' checked="checked"';
					var count = $(this).find('span.count').text();
					 
					items += '<li class="'+ $(this).attr('class') +'"><label><input type="checkbox"' + checked + ' class="markerFilter" id="filter' + itemType + '" name="filter[' + itemType.toLowerCase() + ']"/> ' + displayType + ' <span class="count">(' + count + ')<'+'/span><'+'/label><'+'/li>';
				});
				if(items){
					$('.mapFilters').append(items);
				}
				$('#mapItemsSummary').remove();

				$('table.mapItems dl.position').addClass('closed');
				$('table.mapItems dl.position.closed').one('click', function(){
					$(this).removeClass('closed').addClass('open');
				});
				
				$('.mapFilters').delegate('input.markerFilter', 'click', function(){
					that.applyFilter(this);
				});
				
				$('table.mapItems').tablesorter(); //.tablesorterPager();
				
			    $("#regionTabs").tabs({
					show: function(e, ui){
						if(ui.index != 0){
							return;
						}
						google.maps.event.trigger(map, 'resize');
					}
				});
				
			}, // End displayRegion.setupPage
			
			hideInfoWindowsExcept: function(except){
    			for(var index in this.infoWindows){
    				if(index == except && except !== null){
    					this.infoWindows[index].open(this.map,this.markers[index]);
    				} else {
    					this.infoWindows[index].close();
    				}
				}
			}, // End displayRegion.hideInfoWindowsExcept
			
			resetFilter: function(){
				var items = '';
				for(var itemType in Lib.Maps.itemCount){
					
					var displayType = Lib.Maps.displayRegion.itemTypeDisplay[itemType];
					
					if(typeof(this.showTypes[itemType]) == 'undefined'){
						console.log(itemType + ' undefined in showTypes' );
						continue;
					} else {
						var checked = this.showTypes[itemType] ? ' checked="checked"' : '';
						var count = Lib.Maps.itemCount[itemType];
					}
					 
					items += '<li class="'+ itemType +'"><label><input type="checkbox"' + checked + ' class="markerFilter" id="filter' + itemType + '" name="filter[' + itemType.toLowerCase() + ']"/> ' + displayType + ' <span class="count">(' + count + ')<'+'/span><'+'/label><'+'/li>';
				}
				
				$('.mapFilters').html(items);
			},
			
			applyFilter: function(filterElement){
				this.hideInfoWindowsExcept(null);
				
				var currentType = $(filterElement).parent().parent().attr('class');
				var checked = ($(filterElement).attr('checked'));
				if(typeof(this.showTypes[currentType]) == undefined || this.showTypes[currentType] == checked){
					return;
				}

				this.showTypes[currentType] = checked;
				for(var i in this.markers){
					if(this.markers[i].itemType != currentType){
						continue;
					}

					this.markers[i].setVisible(this.showTypes[this.markers[i].itemType]);
				}
				var end = new Date().getTime();
			}, // End displayRegion.applyFilter
			
			loadMarkers: function(items){
				this.markers = [];
				this.infoWindows = [];
				
				Lib.Maps.itemCount = {};
				
				for(var i = 0, len = items.length; i < len; i++){
					var item = items[i],
						visible = this.showTypes[item.itemType];
						
				    var image = new google.maps.MarkerImage(this.options.icons[item.itemType],
			            new google.maps.Size(21.0, 34.0),
			            new google.maps.Point(0, 0),
			            new google.maps.Point(10.0, 17.0)
			        );
			        var shadow = new google.maps.MarkerImage(this.options.icons['shadow'],
			            new google.maps.Size(39.0, 34.0),
			            new google.maps.Point(0, 0),
			            new google.maps.Point(10.0, 17.0)
			        );
					
					var marker = new google.maps.Marker({
						position: new google.maps.LatLng(parseFloat(item.position[0]),parseFloat(item.position[1])), 
						map: this.map,
						title: item.singularDisplayType + ' - ' + item.title,
						visible: visible,
				        icon: image,
				        shadow: shadow
					});
					marker.itemType = item.itemType;
					

					var infoWindow = new google.maps.InfoWindow({
						content: '<div class="mapInfoWindow '+ item.itemType +'">' + item.link  + '<br/>' + item.info + '</div>'
					});
					
					infoWindow.itemId = item.itemId;

					Lib.Maps.displayRegion.addClickListener(map, marker, infoWindow);
					
					this.markers[item.itemTypeId] = marker;
					this.infoWindows[item.itemTypeId] = infoWindow;
					
					if(typeof(Lib.Maps.itemCount[item.itemType]) == 'undefined'){
						Lib.Maps.itemCount[item.itemType]  = 1;
					} else {
						Lib.Maps.itemCount[item.itemType] += 1;
					}
					Lib.Maps.displayRegion.itemTypeDisplay[item.itemType] = item.displayType;
				};
			},  // End displayRegion.loadMarkers
			
			addClickListener: function(map, marker, infoWindow){
				google.maps.event.addListener(marker, 'click', function() {
					infoWindow.open(map, marker);
				});
			},
			
			loadItemsInBounds: function() {
				var bounds = this.map.getBounds(),
					ne = bounds.getNorthEast(),
					sw = bounds.getSouthWest(),
					el = [sw.lat(), sw.lng(), ne.lat(), ne.lng()],
					that = this;
				
				$.ajax({
						url: "/ajax/getitemsinbounds/",
						dataType:'json',
						data: {'b':el.join(',')},
						success: function(data){
				    		//console.log(data.length, "items received");
				    		for(var itemId in that.markers){
				    			google.maps.event.clearListeners(that.markers[itemId]);
				    			that.markers[itemId].setMap(null);
				    		}
				    		Lib.Maps.displayRegion.loadMarkers(data);
				    		Lib.Maps.displayRegion.resetFilter();
						},
						error: function(xhr, textStatus, errorThrown){
							console.log(textStatus);
						}
		    	});			
			} // End displayRegion.loadItemsInBounds

			
		}, // End displayRegion
		
		displayItem: {
			defaultOptions: {
				center:[],
				zoom:8,
				mapTypeId: null,
				mapElementId:'',
				regionDetails:''
			}, // End displayItem.defaultOptions
			
			options:{},
			
			map:null,
			
			init: function(userOptions, callback){
				if (typeof google == 'undefined' || !google.maps) {
					return;
				}
				this.defaultOptions.mapTypeId = google.maps.MapTypeId.HYBRID;
				var options = this.options = jQuery.extend(this.defaultOptions, userOptions);
				var latLng = new google.maps.LatLng(parseFloat(options.center[0]), parseFloat(options.center[1]));
			    var mapOptions = {
			    	zoom: parseInt(options.zoom),
			    	center: latLng,
			    	mapTypeId: options.mapTypeId
			    };
			    var map = this.map = new google.maps.Map(document.getElementById(options.mapElementId), mapOptions);
				var marker = this.marker = new google.maps.Marker({
					position: latLng, 
					map: map
				});
				
				if(options.regionDetails){
					var infoWindow = new google.maps.InfoWindow({
						content: options.regionDetails
					});
					google.maps.event.addListener(marker, 'click', function() {
						infoWindow.open(map,marker);
					});	
				}
				
			} // End displayItem.init
		}, // End displayItem

		editItem: {
			defaultOptions: {
				mapElementId:'mapElement',
				center: [],
				zoom:8,
				hasMarker: false,
				bounds: [],
				mapTypeId: null,

				mapLabel:'',
				clearLocation:'clearLocation',
				locateMe:'locateMe',
				width:'100%',
				height:'20em'
			}, // End editItem.defaultOptions
			
			options:{},
			
			map:null,
			
			init: function(userOptions, callback){
				if (typeof google == 'undefined' || !google.maps) {
					return;
				}
				this.defaultOptions.mapTypeId = google.maps.MapTypeId.HYBRID;
				
				var options = this.options = jQuery.extend(this.defaultOptions, userOptions);
		
				this.setupPage();
				
				var latLng = new google.maps.LatLng(parseFloat(options.center[0]), parseFloat(options.center[1]));
			    var mapOptions = {
			    	zoom: parseInt(options.zoom),
			    	center: latLng,
			    	mapTypeId: options.mapTypeId
			    };
			    var map = this.map = new google.maps.Map(document.getElementById(options.mapElementId), mapOptions);
				if(options.hasMarker){
				    var marker = this.marker = new google.maps.Marker({
						position: latLng, 
						map: map
					});
				} else {
					var marker = this.marker = null;
				}
				if(options.bounds.length == 4){
				    var bounds = new google.maps.LatLngBounds(
					    	new google.maps.LatLng(options.bounds[0],options.bounds[1]),
					    	new google.maps.LatLng(options.bounds[2],options.bounds[3])
				    );
				    google.maps.event.addDomListenerOnce(map, 'bounds_changed', function() {
				    	var zoom = map.getZoom();
				    	Lib.Maps.restrictMapToBounds(map, bounds, zoom);
				    });
				    map.fitBounds(bounds);			    
			    }
			  	var that = this;
			    google.maps.event.addListener(map, 'click', function(event) {        
			        var position = new google.maps.LatLng(event.latLng.lat(), event.latLng.lng());
			        that.handleClick(position);
			    });

			    google.maps.event.addListener(map, 'maptypeid_changed', function(){
			    	that.updateMapType();
			    });
			    
				google.maps.event.addListener(map, 'zoom_changed', function() {
			        $('#zoom').val(that.map.getZoom());
			    });
			    
			    $('#clearLocation').click(function(){
			    	if(marker){
			    		marker.setMap(null);
			    	}
			    	$('#latitude,#longitude').val('');
			    	$('#zoom').val(0);
			    	$('#locationFlag').val(0);
			    });
			    
			    $('#locateMe').click(function(){
			    	if(!!navigator.geolocation){
			    		navigator.geolocation.getCurrentPosition(function(navPosition){
			    			var position = new google.maps.LatLng(navPosition.coords.latitude, navPosition.coords.longitude);
			    			that.updateMarker(position);
			    		});
			    	}
			    });
			    
			}, // End editItem.init
			
			setupPage: function(){
				var locateMe = '';
				if(!!navigator.geolocation){
					locateMe = '<li><a href="javascript:void(0)" class="actionLinkContainer" id="locateMe">'+ this.options.locateMe + '</a></li>';
				}
				
				$('#fieldset-locationGroup').children('p.element-group').hide().end().append('\
			                <p class="element-group">\
			                	<label class="form-element-label" id="mapLabel">'+ this.options.mapLabel + '</label>\
			                	<span id="mapElement" style="display:block;margin-top:2em;width:'+ this.options.width + '; height:'+ this.options.height + ';"></span>\
			                    <ul id="mapTools">\
			                    	<li><a href="javascript:void(0)" class="actionLinkContainer" id="clearLocation">'+ this.options.clearLocation + '</a></li>\
			                    	' + locateMe + '\
			                    </ul>\
			                </p>\
			    ');
			},
			
		  	updateMapType: function(){
		    	switch(this.map.getMapTypeId()){
		    		default:
		    		case 'roadmap':
		    			$('#mapType').val(0);
		    			break;
		    		case 'satellite':
		    			$('#mapType').val(1);
		    			break;
		    		case 'hybrid':
		    			$('#mapType').val(2);
		    			break;
		    		case 'terrain':
		    			$('#mapType').val(3);
		    			break;
		    	}
		    },
		    
		    updateMarker: function(position){
				if(this.marker == null){
				    var marker = this.marker = new google.maps.Marker({
						position: position, 
						map: this.map
					});
				} else {
					this.marker.setMap(this.map);
					this.marker.setPosition(position);
				}
		        this.map.panTo(position);
		        
		  		$('#latitude').val(position.lat());
		        $('#longitude').val(position.lng());
		        $('#locationFlag').val(1);
		        
		        this.updateMapType();
		    },
		    
		    handleClick: function(position){
		   		this.updateMarker(position);
		    }
		}, // End editItem
		
		addRegionOverlay: function(dptOverlayUrl){
			var layer = new google.maps.KmlLayer(dptOverlayUrl, {suppressInfoWindows: true});
			layer.setMap(map);
			google.maps.event.addListener(layer, 'click', function(event) {
				var position = new google.maps.LatLng(event.latLng.lat(), event.latLng.lng());
				handleClick(position);
			});			
		}, // End addRegionOverlay
		
		restrictMapToBounds: function(map, bounds, minZoomLevel){
			var checkBounds = function() {
				if (bounds.contains(map.getCenter())) {
					return;
				}

				// Out of bounds - Move the map back within the bounds
				var c = map.getCenter(),
					x = c.lng(),
					y = c.lat(),
					maxX = bounds.getNorthEast().lng(),
					maxY = bounds.getNorthEast().lat(),
					minX = bounds.getSouthWest().lng(),
					minY = bounds.getSouthWest().lat();
		
				if (x < minX) x = minX;
				if (x > maxX) x = maxX;
				if (y < minY) y = minY;
				if (y > maxY) y = maxY;
		
				map.setCenter(new google.maps.LatLng(y, x));
			}
		
			google.maps.event.addListener(map, 'dragend', checkBounds);
			
			google.maps.event.addListener(map, 'zoom_changed', function() {
				checkBounds();
				if(map.getZoom() < minZoomLevel){
					map.setZoom(minZoomLevel);
				}
			});
		} // End restrictMapToBounds
};
