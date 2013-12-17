/**
 * Widget NetMap
 */

(function($) {
    $.fn.extend({
        netMap: function(options) {
            var defaults = {
                map: {
                    zoom: 7
                },
                line: {
                    strokeColor: "#000000",
                    strokeOpacity: 1.0,
                    strokeWeight: 2
                },
                currentLine: {
                    strokeColor: "#FF0000",
                    strokeOpacity: 1.0,
                    strokeWeight: 2
                },
                point: {
                    strokeColor: '#000000',
                    strokeOpacity: 1.0,
                    strokeWeight: 2,
                    fillColor: '#000000',
                    fillOpacity: 1.0,
                    radius: 15,
                    clickable: true
                }
            }
            options =  $.extend(defaults, options);
            var valuer = $('#'+options.id);
            var coorder = $('#'+options.id+'_coords');
            //console.log(this[0]);
            var map = new google.maps.Map(this[0],options.map);
            var line = new Array();
            var point = new Array();
            var infoWindow = new google.maps.InfoWindow();
            
            this.refresh = function() {
                var center = map.getCenter();        
                google.maps.event.trigger(map, "resize");        
                map.setCenter(center);
            }
            
            addLine = function(id, name, coords) {
                if(coords) {
                    line[id] = new google.maps.Polyline(options.line);
                    //console.log('Dodaję linię '+id);
                    var lp = coords.split(';');
                    var points = new Array();
                    for(var i in lp) {
                        var c = lp[i].split(',');
                        points[i] = new google.maps.LatLng(c[0],c[1]);
                    }
                    line[id].setPath(points);
                    //map.setCenter(points[lp.length-1]);
                    google.maps.event.addListener(line[id], 'click', function(event){
                        valuer.val(id);
                        valuer.trigger("change");
                    });                    
                    line[id].setMap(map);
                }       
            };
            
            this.addPoint = function(id, fields) {
                if(fields['coordinates']) {
                    //point[id] = new google.maps.Marker(options.point);
                    point[id] = new google.maps.Circle(options.point);
                    var coords = fields['coordinates'].split(',');
                    var ll = new google.maps.LatLng(coords[0],coords[1]);
                    point[id].setCenter(ll);
                    google.maps.event.addListener(point[id], 'click', function(event){
                        infoWindow.setPosition(ll);
                        infoWindow.setContent('<h2>'+fields['type_flag']+' '+fields['name']+'</h2>');
                        infoWindow.open(map);
                    });
                    point[id].setMap(map);
                /*var mapLabel = new MapLabel({
                        text: fields['type_flag']+' '+fields['name'],
                        position: ll,
                        map: map,
                        fontSize: 20,
                        align: 'center'
                    });*/                    
                }
            }
            
            this.setData = function(data, params){
                line = new Array();
                $.each(data['multi'], function(key, value) {  
                    var keyValues = new Array();
                    for(var k in params.keyField) {
                        keyValues[k] = value[params.keyField[k]];
                    }
                    var coords = value[params.listField[0]];
                    var name = value[params.listField[1]];
                    addLine(keyValues.join(';'),name,coords);
                });                
            }
            
            this.showValue = function(value) {
                //console.log('Zaznaczam linię '+value);
                for (var i in line) {
                    if (line[i]) {
                        if (i==value) {
                            line[value].setOptions(options.currentLine);
                        }
                        else
                            line[i].setOptions(options.line);
                    }
                }
            }
            
            addLatLng = function (event) {
                var path = line[valuer.val()].getPath();
                // Because path is an MVCArray, we can simply append a new coordinate
                // and it will automatically appear
                path.push(event.latLng);
            }
    
            google.maps.event.addListener(map, 'click', function(event) {
                if (coorder.val()=='') {
                    coorder.val(event.latLng.lat()+','+event.latLng.lng());
                } else {
                    coorder.val(coorder.val()+';'+event.latLng.lat()+','+event.latLng.lng());
                }
                addLatLng(event);
            });            
            
            return this;
        }
    });
})(jQuery);