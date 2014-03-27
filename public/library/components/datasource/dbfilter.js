/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

dbFilter = function(id, params) {
    this.id = id;
    this.dataField = params.dataField;
    this.operator = params.operator;
    this.type = params.type;
    var self = this;
    this.setData = function(value) {
        //console.log(this.id+' v '+value);
        $('#'+this.id).val(value);
    }
    
    this.clear = function() {
        $('#'+this.id).val('');
    }
    
    this.getValue = function() {
        var val = $('#'+this.id).val();
        if(val==null)
            val='';
        return val;
    }
    
    this.disable = function() {
        $('#'+this.id).attr('disabled','disabled');
    }

    this.enable = function() {
        $('#'+this.id).removeAttr('disabled');
    }
    
    this.setEvents = function(actionFunction) {
        //filtry listy
        if (this.type == 'ls') {
        /*$('#'+this.id).change({
                dataAction: 'filterSeekAction',
                actionType: 'filter',
                offset: 0
            }, function(event) {
                actionFunction(event);
            });*/   
        }

        else {
        //filtry tekstowe
        /*$('#'+this.id).keypress({
                dataAction: 'filterSeekAction',
                actionType: 'filter',
                offset: 0
            }, function(event) {
                if(event.keyCode==13){
                    actionFunction(event);
                    return false;
                }
            });*/
        /*
            $('#'+this.id).focusout({
                dbaction: 'filterSeekAction',
                offset: 0
            }, function(event) {
                action(event);
            });
             */
        }
        
    //dodatkowo dla przycisków - ikon
    /*if ($('#'+this.id+'-button')) {
            $('#'+this.id+'-button').click({
                dataAction: 'filterSeekAction',
                actionType: 'filter',
                offset: 0
            }, function(event) {
                actionFunction(event);
            });            
        }*/
    }
    
    this.getFilter = function() {
        var f = {};
        f[this.dataField] = {
            value: this.getValue(), 
            operator: this.operator
            
        }
        if (this.type == 'ls' && f.value == '') {
            f[this.dataField].operator = 'begin';
        }
        return f;
    }
}