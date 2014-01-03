/**
 * Widget PointMap, LineMap
 */


(function($) {
    $.fn.extend({
        pointMap: function(options) {
            var defaults = {
                map: {
                    zoom: 12
                },
                marker: {
                    title: ''
                }
            }
            options =  $.extend(defaults, options);
            
            var valuer = $('#'+options.id);
            var map = new google.maps.Map(this[0],options.map);
            var marker = new google.maps.Marker(options.marker);
                
            this.refresh = function() {
                var center = map.getCenter();        
                google.maps.event.trigger(map, "resize");        
                map.setCenter(center);
            }
            
            this.disable = function() {
                $('#'+options.id).attr('disabled','disabled');
                $('#'+options.id).parent().addClass('ui-state-disabled');        
            }

            this.enable = function() {
                $('#'+options.id).removeAttr('disabled');
                $('#'+options.id).parent().removeClass('ui-state-disabled');
            }
    
            this.readonly = function(ro) {
                if (ro) {
                    $('#'+options.id).attr('readonly','readonly');
                } else {
                    $('#'+options.id).removeAttr('readonly');
                }        
            }
            

            valuer.change(function() {
                if($('#'+options.id).val()) {
                    var c = $('#'+options.id).val().split(',');
                    var p = new google.maps.LatLng(c[0],c[1]);
                    map.setCenter(p);
                    marker.setPosition(p);
                    //marker.setTitle();
                    marker.setMap(map);
                } else marker.setMap(null);
            });

            google.maps.event.addListener(map, 'click', function(event) {
                var attrRO = $('#'+options.id).attr('readonly');
                if ($('#'+options.id).is(':disabled') == false && (typeof attrRO == 'undefined' || attrRO == false)) {
                    $('#'+options.id).val(event.latLng.lat()+','+event.latLng.lng());
                    map.setCenter(event.latLng);
                    marker.setPosition(event.latLng);
                    marker.setMap(map);
                }
            });
            
            return this;
        },
        lineMap: function(options) {
            var defaults = {
                map: {
                    zoom: 5
                },
                line: {
                    strokeColor: "#000000",
                    strokeOpacity: 1.0,
                    strokeWeight: 2
                }
            }
            options =  $.extend(defaults, options);
            
            var valuer = $('#'+options.id);
            var map = new google.maps.Map(this[0],options.map);
            var line = new google.maps.Polyline(options.line);
            
            this.refresh = function() {
                var center = map.getCenter();        
                google.maps.event.trigger(map, "resize");        
                map.setCenter(center);
            }
            
            this.disable = function() {
                $('#'+options.id).attr('disabled','disabled');
                $('#'+options.id).parent().addClass('ui-state-disabled');        
            }

            this.enable = function() {
                $('#'+options.id).removeAttr('disabled');
                $('#'+options.id).parent().removeClass('ui-state-disabled');
            }
    
            this.readonly = function(ro) {
                if (ro) {
                    $('#'+options.id).attr('readonly','readonly');
                } else {
                    $('#'+options.id).removeAttr('readonly');
                }        
            }            
            
            valuer.change(function() {
                if(valuer.val()) {
                    var lp = valuer.val().split(';');
                    var points = new Array();
                    for(var i in lp) {
                        var c = lp[i].split(',');
                        points[i] = new google.maps.LatLng(c[0],c[1]);
                    }
                    line.setPath(points);
                    //map.setCenter(points[lp.length-1]);
                    line.setMap(map);
                } else line.setMap(null);
       
            });
            
            addLatLng = function (event) {
                var path = line.getPath();
                // Because path is an MVCArray, we can simply append a new coordinate
                // and it will automatically appear
                path.push(event.latLng);
            }
    
            google.maps.event.addListener(map, 'click', function(event) {
                if (valuer.val()=='') {
                    valuer.val(event.latLng.lat()+','+event.latLng.lng());
                } else {
                    valuer.val(valuer.val()+';'+event.latLng.lat()+','+event.latLng.lng());
                }
                addLatLng(event);
            });
            
            return this;   
        },
        lineListMap: function(options) {
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
                }
            }
            options =  $.extend(defaults, options);
            var valuer = $('#'+options.id);
            var coorder = $('#'+options.id+'_coords');
            var map = new google.maps.Map(this[0],options.map);
            var line = new Array();
            
            this.refresh = function() {
                var center = map.getCenter();        
                google.maps.event.trigger(map, "resize");        
                map.setCenter(center);
            }
            
            this.disable = function() {
                $('#'+options.id).attr('disabled','disabled');
                $('#'+options.id).parent().addClass('ui-state-disabled');        
            }

            this.enable = function() {
                $('#'+options.id).removeAttr('disabled');
                $('#'+options.id).parent().removeClass('ui-state-disabled');
            }
    
            this.readonly = function(ro) {
                if (ro) {
                    $('#'+options.id).attr('readonly','readonly');
                } else {
                    $('#'+options.id).removeAttr('readonly');
                }        
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
            
            this.setData = function(data, params){
                line = new Array();
                $.each(data['rows'], function(key, value) {  
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