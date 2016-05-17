function MapControl() {
    
    this.map = null; //googleMap
    this.mc = null; //markerClusterer
	this.weatherLayer = null; //WeatherLayer
    this.lastInfoWindow = null;    
    this.infoWindows = Array();
    this.streetMarkers = new Array();
    this.streetMarkersById = new Array();
    this.streetExportList = new Array();
    this.streets = new Array();
    this.lastMarker = null;

    //odkazy pro AJAX akce
    this.linkPoints = null;
    this.linkViewportStreets = null;
    this.linkAddToExportList = null;
    this.linkRemoveFromExportList = null;
    this.linkWrongGps = null;
    this.linkThis = null;
    this.linkRemoveCompanyFromMap = null;

    this.filterLocation = null;
    this.loadAllEnabled = false;
    this.krajChanged = false;
    this.zoom = 10    
    this.centerLat = null;
    this.centerLng = null;
    this.colorGreen = null;
    this.colorRed = null;
    this.colorYellow = null;
    this.colorBlue = null;
    this.colorBlack = null;
	this.colorPurple = null;
    
    this.imageLocation = new google.maps.MarkerImage('/images/icons/marker_location.png',      
        new google.maps.Size(20, 24),      
        new google.maps.Point(0,0),      
        new google.maps.Point(9, 22));
        
    this.imageGreen = new google.maps.MarkerImage('/images/icons/marker_green.png',      
        new google.maps.Size(20, 24),      
        new google.maps.Point(0,0),      
        new google.maps.Point(9, 22));

    this.imageRed = new google.maps.MarkerImage('/images/icons/marker_red.png',      
        new google.maps.Size(20, 24),      
        new google.maps.Point(0,0),      
        new google.maps.Point(9, 22));

    this.imageYellow = new google.maps.MarkerImage('/images/icons/marker_yellow.png',
        new google.maps.Size(20, 24),      
        new google.maps.Point(0,0),      
        new google.maps.Point(9, 22));
        
    this.imageBlue = new google.maps.MarkerImage('/images/icons/marker_blue.png',
        new google.maps.Size(20, 24),      
        new google.maps.Point(0,0),      
        new google.maps.Point(9, 22));

    this.imageBlack = new google.maps.MarkerImage('/images/icons/marker_black.png',
        new google.maps.Size(20, 24),      
        new google.maps.Point(0,0),      
        new google.maps.Point(9, 22));
		
	this.imagePurple = new google.maps.MarkerImage('/images/icons/marker_purple.png',
        new google.maps.Size(20, 24),      
        new google.maps.Point(0,0),      
        new google.maps.Point(9, 22));

    this.shadow = new google.maps.MarkerImage('/images/icons/marker_shadow.png',      
        new google.maps.Size(22, 15),
        new google.maps.Point(0,0),
        new google.maps.Point(4, 14));
        
    this.getIcon = function (color) {        
        if (color == this.colorGreen ) {return this.imageGreen;}
        if (color == this.colorRed ) {return this.imageRed;}        
        if (color == this.colorYellow ) {return this.imageYellow;}      
        if (color == this.colorBlue ) {return this.imageBlue;}
        if (color == this.colorBlack ) {return this.imageBlack;}
		if (color == this.colorPurple ) {return this.imagePurple;}
        return false
    }


    /**
    * tato funkce má na starosti vytvoření info okénka u markeru
    */
    this.attachInfo = function (marker,point) {
        //console.info(func_get_args());        
        var content = '<div class="color'+point.color+'">'+
            point.name +"</div>";
            if(point.description != null)
            {
                content += point.description;
            }

        if ( point.id) {
            
            content += '<div class="infoBtn" style="width:310px">'+ '<a class="export" rel="'+point.id+'" onclick="mapControl.addToExportList('+point.id+')" >Připravit pro export</a>  ';
            content += '<a class="export" style="float: right;" rel="'+point.id+'" href="'+point.detailLink+'" target="_blank" >Detail</a>  ';
            content +=  '</div>';
        }
        
        var infoWindow = new google.maps.InfoWindow({
            content:content,
            maxWidth: 700
            
        });
        mapControl = this;
        google.maps.event.addListener(marker, 'click', function() {
            mapControl.lastMarker = marker;
            infoWindow.open(mapControl.map,marker);
            if ( mapControl.lastInfoWindow && infoWindow!=mapControl.lastInfoWindow) {
                mapControl.lastInfoWindow.close();
            }
            mapControl.lastInfoWindow = infoWindow;
        });
        this.infoWindows[point.id] = infoWindow;
        return infoWindow;
    }

    this.removeCompanyFromMap = function(id) {
        $.post(this.linkRemoveCompanyFromMap, { id: id}, function(data, status, jqXHR) {
            if (jqXHR.status === 200) {
                bootstrap.flashMessage('Špatná poloha byla nahlášena.', 'success');
            }
            else {
                bootstrap.flashMessage('Polohu se nepodařilo nahlásit. <br><strong>Chyba byla automaticky zaznamenána.</strong>')
            }
        });
    }

    this.addStreet = function(point) {
        var position = new google.maps.LatLng(point.lat, point.lng);
        var marker = new google.maps.Marker({
            position: position,           
            title: point.name,
            icon: point.icon,
            shadow: this.shadow
            //animation: google.maps.Animation.DROP   ,            
        });
        var infoWindow = this.attachInfo(marker, point);
        this.streetMarkersById[point.id] = marker;
        
        //když není v exportním listu přidám marker na mapu
        var isInExportList = false;
        for ( var y in this.streetExportList ) {
            if ( this.streetExportList[y] == point.id ) {
                isInExportList = true;
                continue;
            }
        }
        if ( !isInExportList) this.streetMarkers.push(marker);
                
        //přidá odkaz na ukázání markeru na mapě přes klik v datagridu
        if (  this.filterLocation && !this.krajChanged && $("#row"+point.id).length>0 ) {
            var mapControl = this;
            var odkaz = $('<a class="localize" rel="'+point.id+'">Ukázat</a>');            
            odkaz.click(function(event){
                event.preventDefault();
                mapControl.map.setCenter(position);
                mapControl.map.setZoom(14);
                infoWindow.open(mapControl.map,marker);
                if ( mapControl.lastInfoWindow) {
                    mapControl.lastInfoWindow.close();
                }                
                mapControl.lastInfoWindow = infoWindow;
                return false;
            });
            $("#row"+point.id+" td.map").append(odkaz);        
        }
        return marker;
    } 
    
    
    this.hideStreets = function (){        
        this.mc.clearMarkers();
        $(".order td.map a.localize").remove();
    }
    
    this.showStreets = function() {
        this.mc.addMarkers(this.streetMarkers);
        this.endLoading();
    }
    
        
    this.initStreets = function () {
        this.startLoading();
        var that = this;

        for (var i in this.streetMarkers) {
            this.streetMarkers[i].setMap(null);
        }
        for (var i in this.streetMarkersById) {
            this.removeMarkerFromMap(this.streetMarkersById[i].id);
        }
        this.streetMarkers = [];
        this.streetMarkersById = [];

        var filters = $("form#mapFilter input:checked");

        $.each(this.streets, function(){
            this.icon = null;

            if (
                (this.meetingStatus !== "" && this.meetingStatus !== null && (filters.length > 1 || filters.length === 0)) ||
                (filters.length === 1 && $(filters[0]).attr('name') === 'visited')
            )
            {
                this.icon = that.imageRed;
            }
            else if (
                (this.meetingStatus === "" || this.meetingStatus == null && (filters.length > 1 || filters.length === 0)) ||
                (filters.length === 1 && $(filters[0]).attr('name') === 'nonvisited')
            )
            {
                this.icon = that.imageYellow;
            }
            else if (
                (this.tv == 3 && (filters.length > 1 || filters.length === 0)) ||
                (filters.length === 1 && $(filters[0]).attr('name') === 'withO2TV')
            )
            {
                this.icon = that.imageGreen;
            }
            else if (
                (this.status == "Přímý" && (filters.length > 1 || filters.length === 0)) ||
                (filters.length === 1 && $(filters[0]).attr('name') === 'staropramen')
            )
            {
                this.icon = that.imageBlue;
            }
            else if (
                (this.blacklist == "1" && (filters.length > 1 || filters.length === 0)) ||
                (filters.length === 1 && $(filters[0]).attr('name') === 'blacklist')
            )
            {
                this.icon = that.imageBlack;
            }
            if (
                (this.isLead == 1 && (filters.length > 1 || filters.length === 0)) ||
                (filters.length === 1 && $(filters[0]).attr('name') === 'leads')
            )
            {
                this.icon = that.imagePurple;
            }

            if (this.icon !== null)
            {
                that.addStreet(this);
            }
        });


        if (this.streetMarkers.length === 0 || this.streetMarkers.length >= 5)
        { var workshop = 'provozoven'; var isShowed = "je zobrazeno";}
        else if (this.streetMarkers.length === 1)
        { var workshop = 'provozovna'; var isShowed = "je zobrazena";}
        else
        { var workshop = 'provozovny'; var isShowed = "jsou zobrazeny";}
        $("#pointCount").html("celkem "+isShowed+" <strong>"+this.streetMarkers.length+"</strong> "+workshop);
        this.endLoading();
    };

    this.startLoading = function(){$("#mapLoading").show();}
    this.endLoading = function(){$("#mapLoading").hide();}
    this.getSelectedKraj = function() {return $("#frmfiltrForm-kraj").val();}
           
    /**
     * tato funkce se stará o zpracování poslaných bodů
    */
    this.loadStreets = function() {        
        this.startLoading();                
        if (this.filterLocation || (!this.filterLocation && this.loadAllEnabled) ) {
            var mapControl = this;
            $.post(this.linkPoints,{type:'streets',location_id: this.filterLocation}, function(payload){                       
                mapControl.streets = payload.points;
                mapControl.hideStreets();
                mapControl.initStreets();                
                mapControl.showStreets();                                
            } );
        } else {
            this.endLoading();
        }
                
    }
	
	
	this.showWeather = function(event){
		
		if ( this.weatherLayer.map == null) {
			this.weatherLayer.setMap(this.map);
			$(event.currentTarget).text('Skrýt počasí');
			
		} else {
			this.weatherLayer.setMap(null);
			$(event.currentTarget).text('Zobrazit počasí');
		}
		
		
	}
    
    /**
     * tato funkce se stará o zpracování poslaných bodů
    */
    this.loadKrajStreets = function() {     
        if ( this.getSelectedKraj() && this.map ) {
            this.startLoading();        
            this.krajChanged = true;
            $("#showSelectKrajHint").slideUp('slow');
            var mapControl = this;
            $.post(this.linkPoints,{type:'krajStreets',kraj: this.getSelectedKraj()}, function(payload){                       
                mapControl.streets = payload.points;                
                mapControl.hideStreets();            
                mapControl.initStreets();                
                mapControl.showStreets();
                $("input#loadAllStreets").hide();
            });
        }
    }

    this.addToExportList = function(id) {
        var mapControl  = this;
		$.nette.ajax({
			type: 'post',
			url: this.linkAddToExportList,
			data: {id: id}
		}).success(function(payload){
			if ( mapControl.lastInfoWindow ) { 
                mapControl.lastInfoWindow.close();
            }
            mapControl.streetExportList.push(id);
            mapControl.removeMarkerFromMap(id);
            mapControl.endLoading();
		});
    }
	
    this.removeFromExportList = function(id) {
        var mapControl  = this;
        mapControl.startLoading();
		$.nette.ajax({
			type: 'post',
			url: this.linkRemoveFromExportList,
			data: {id: id}
		}).success(function(payload){
			mapControl.streetExportList.splice(id,1);
            mapControl.addMarkerToMap(id);
            mapControl.endLoading();
		});
    }
    
    this.addMarkerToMap = function(id) {
        var marker =  this.streetMarkersById[id];
        if ( marker) this.mc.addMarker(marker);
    }
    
    this.removeMarkerFromMap = function(id) {
        var marker =  this.streetMarkersById[id];
        if ( marker) this.mc.removeMarker(marker);
    }
    
    
    
    this.openLocation = function(id) {
        $.doPost(this.linkThis, {location_street_id: id} );
    }
    
    this.reportWrongGps = function(id) {
        var mapControl = this;
        $.post(this.linkWrongGps, {id:id}, function(payload){
            $.nette.success(payload);
            mapControl.lastMarker.setMap(null);
        });
    }
    
    this.init = function() {
        var myMapOptions = {
            zoom: this.zoom,
            center: new google.maps.LatLng(this.centerLat,this.centerLng),
            mapTypeId: google.maps.MapTypeId.ROADMAP  ,
            streetViewControl : true      
         };    
        this.map = new google.maps.Map(document.getElementById("mapCanvas"), myMapOptions);   
        this.mc = new MarkerClusterer(this.map,this.streets,{gridSize: 100, maxZoom: 13});
        
	    this.weatherLayer = new google.maps.weather.WeatherLayer({
			temperatureUnits: google.maps.weather.TemperatureUnit.CELSIUS
		});
		
		
        //this.streetExportList
        mapControl = this;
        $('#mapStreets :input').each(function(){
            mapControl.streetExportList.push(parseInt($(this).val()));
        });
        
        this.initStreets();
        this.showStreets();
           
        //naBINDování událostí   
        var mapControl = this;
        $("ul.mapTabs input").on("change", function(){
            mapControl.hideStreets();
            mapControl.initStreets();
            mapControl.showStreets();
        });
        
        $(".infoBtn a.export, .order td a.export").on("click", function() {
            mapControl.addToExportList($(this).attr("rel") );return false;
        }); 
        
        $("input#loadAllStreets").click(function() {
            if(confirm("Jste si jistí? Tato akce může trvat dlouho")) { 
                mapControl.loadAllEnabled = true;
                mapControl.loadStreets();
                $(this).hide();
            }
        });
        
        $(".infoBtn a.filtr").on("click", function() {
            mapControl.openLocation($(this).attr("rel") );return false;
        }); 
        
        $(".infoBtn a.wrong").on("click", function() {
            mapControl.reportWrongGps($(this).attr("rel"));return false;
        });
        
		$("#showWeather").on('click', function(event){
			mapControl.showWeather(event);
		});
    }
    
        
} 