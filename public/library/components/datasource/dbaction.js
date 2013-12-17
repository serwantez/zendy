/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

dbAction = function(id, params) {
    this.id = id;
    this.params = params;
    var self = this;
    
    this.disable = function() {
        $('#'+this.id).attr('disabled','disabled');
        if (params.type == 'bt') {
            $('#'+this.id).button({
                disabled: true
            });
        } else {
            if (params.type == 'mi') {
                $('#'+this.id).parent().addClass('ui-state-disabled');
            }
        }
    }
    
    this.enable = function() {
        if (params.type == 'bt') {
            $('#'+this.id).button({
                disabled: false
            });
        } else {
            if (params.type == 'mi') {
                $('#'+this.id).parent().removeClass('ui-state-disabled');
            }
        }
        $('#'+this.id).removeAttr('disabled');
    }
    
    this.setEvents = function(actionFunction) {
        if (this.params.dataAction) {
            $('#'+this.id).click(
                this.params, function(event) {
                    if (params.type=='bt' || params.type=='mi' && !$('#'+self.id).parent().hasClass('ui-state-disabled')) {
                        actionFunction(event);
                    }
                });
        }
    
    }
}
