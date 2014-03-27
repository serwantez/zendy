/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

dbNavi = function(id, params) {
    this.id = id;
    var self = this;
    this.keyField = params.keyField;
    

    this.setValue = function(value) {
        if ($('#'+this.id)[0].nodeName=='INPUT' && ($('#'+this.id).attr('type')=='radio' || $('#'+this.id).attr('type')=='checkbox')) {
            $('input:'+$('#'+this.id).attr('type')+'[name='+this.id+']').val([value]);
        } else {
            $('#'+this.id).val(value);
            if (dc[params.type][this.id]) {
                dc[params.type][this.id].showValue(value);
            }
        }    
    }
    
    this.getValue = function() {
        //zwraca wartość dla poszczególnych typów kontrolek
        if ($('#'+this.id)[0].nodeName=='INPUT' && $('#'+this.id).attr('type')=='radio') {
            val = $('input:'+$('#'+this.id).attr('type')+'[name='+this.id+']:checked').val();
        } else if ($('#'+this.id)[0].nodeName=='INPUT' && $('#'+this.id).attr('type')=='checkbox') {
            val = $('input:'+$('#'+this.id).attr('type')+'[name='+this.id+']:checked').val();
            //wartość dla niezaznaczonego checkboxa pobierana jest z ukrytego pola
            if (!val) val = $('input:hidden'+'[name='+this.id+']').val();
        } else {
            val = $('#'+this.id).val();
        }
        return val;
    }
    
    this.getIndex = function() {
        var index;
        if ($('#'+this.id)[0].nodeName=='INPUT' && ($('#'+this.id).attr('type')=='radio' || $('#'+this.id).attr('type')=='checkbox')) {
            var input = $('input:'+$('#'+this.id).attr('type')+'[name='+this.id+']');
            index = input.index(input.filter(':checked'));
        } else {
            index = $('#'+this.id)[0].selectedIndex;
        }
        return index;
    }
    
    this.refresh = function(data) {
        //odświeża listy danych
        //console.log('refresh '+this.id+' of '+params.type);
        if (dc[params.type][this.id]) {
            dc[params.type][this.id].setData(data, params);
        }
    }
    
    this.disable = function() {
        if (dc[params.type][this.id]) {
            dc[params.type][this.id].disable();
        }
    }
    
    this.enable = function() {
        //console.log(params.type);
        if (dc[params.type][this.id]) {
            dc[params.type][this.id].enable();
        }
    }
    
    this.setEvents = function(actionFunction) {
        params.actionFunction = actionFunction;
        //if(this.changeclick!=false)
        $('#'+this.id).bind("change", {
            dataAction: 'searchAction',
            actionType: 'standard'
        }, function(event) {            
            var value = self.getValue();
            if (value) {
                value = value.split(';');
                event.data.searchValues = {};
                for(var k in params.keyField) {
                    event.data.searchValues[params.keyField[k]] = value[k];
                }                
                actionFunction(event);
            }
        });
        if (params.type=='sl') {
            dc["sl"][this.id].widgetUl.on("sortstop", function(event, ui){
                if (ui !== undefined) {
                    event.data = {
                        dataAction: 'moveToAction',
                        actionType: 'standard',
                        oldPosition: dc["sl"][self.id].oldPosition,
                        newPosition: dc["sl"][self.id].newPosition
                    }
                    actionFunction(event);
                }
            });
        }
    }
}
