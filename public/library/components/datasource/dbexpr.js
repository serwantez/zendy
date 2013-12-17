/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

dbExpr = function(id, expr) {
    this.id = id;
    this.expr = expr;
    var self = this;
    
    this.setData = function(value) {
        if (this.expr == 'offset') {
            value++;
        }
        $('#'+this.id).val(value);
    }
    
    this.getValue = function() {
        var val = $('#'+this.id).val();
        if (this.expr == 'offset') {
            val--;
        }
        return val;
    }
   
    this.disable = function() {
        $('#'+this.id).attr('disabled','disabled');
    }

    this.enable = function() {
        $('#'+this.id).removeAttr('disabled');
    }
    
    this.setEvents = function(actionFunction) {
        if (this.expr == 'offset') {
            $('#'+this.id).keypress({
                dataAction: 'seekAction',
                actionType: 'standard'
            }, function(event) {
                if(event.keyCode==13){
                    event.data.offset = self.getValue();
                    actionFunction(event);
                }
            });
        }
        if (this.expr == 'page') {
            $('#'+this.id).keypress({
                dataAction: 'seekPageAction',
                actionType: 'standard'
            }, function(event) {
                if(event.keyCode==13){
                    event.data.page = self.getValue();
                    actionFunction(event);
                }
            });
        }
        
    }
}