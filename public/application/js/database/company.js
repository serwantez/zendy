/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$(function() {
    var mapa;
    var dymek;
    var marker;
    
    startMap = function() {
        var coords = new google.maps.LatLng(53.41935400090768,14.58160400390625);
        var options = {
            zoom: 15,
            center: coords,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        mapa = new google.maps.Map(document.getElementById("map1"),options); 
        dymek = new google.maps.InfoWindow();
        marker = new google.maps.Marker();
    }
    
    $(document).ready(function(){
        startMap();
        var geocoder = new google.maps.Geocoder();
 
        setMarker = function(wyniki) {
            mapa.setCenter(wyniki[0].geometry.location);
            marker.setPosition(wyniki[0].geometry.location);
            marker.setMap(mapa);

            dymek.open(mapa, marker);
            dymek.setContent('<strong>Poszukiwany adres</strong>');
        }
        
        resultGeocode = function(wyniki, status) {
            if(status == google.maps.GeocoderStatus.OK) {
                $('#coordinates').val(wyniki[0].geometry.location.lat()+','+wyniki[0].geometry.location.lng());
                setMarker(wyniki);
            }
            else {
                $('#coordinates').val('');
                console.log('Nie znaleziono adresu');
            }
        /*$('#ZendY_Db_Form_Element_IconButton_6').click();  
            var current = parseInt($('#ZendY_Db_Form_Element_Expr_1').val());
            var units = parseInt($('#ZendY_Db_Form_Element_Expr_2').val());
            if ((current+1)<=units) {
                $('#ZendY_Db_Form_Element_IconButton_3').click();
                var adres =  $('#address').val()+ ',' + $('#postal_code').val() + ' ' + $('#city').val();
                console.log('Zaczynam szukać adresu '+adres);
                geocoder.geocode({
                    address: adres
                }, resultGeocode);
            } else alert('Przekroczono końcowy offset');*/
            
        }
        
        importAllCoordinates = function() {
            //wysłanie synchronicznego zapytania ajax'em
            $('#ZendY_Db_Form_Element_IconButton_1').click();

            var adres =  $('#address').val()+ ',' + $('#postal_code').val() + ' ' + $('#city').val();
            console.log('Zaczynam szukać adresu '+adres);
            geocoder.geocode({
                address: adres
            }, resultGeocode);

        }
        
        $('#coordinates').dblclick(function() {
            var adres =  $('#address').val()+ ',' + $('#postal_code').val() + ' ' + $('#city').val();
            marker.setMap(null); // ukrywamy marker
            geocoder.geocode({
                address: adres
            }, resultGeocode);
        //importAllCoordinates();
        });
        
        $('#coordinates').change(function() {
            if($('#coordinates').val()) {
                var c = $('#coordinates').val().split(',');
                var p = new google.maps.LatLng(c[0],c[1]);
                mapa.setCenter(p);
                marker.setPosition(p);
                marker.setMap(mapa);
            }
        });
        
        google.maps.event.addListener(mapa, 'click', function(event) {
            $('#coordinates').val(event.latLng.lat()+','+event.latLng.lng());
        });
        
    });
 
});