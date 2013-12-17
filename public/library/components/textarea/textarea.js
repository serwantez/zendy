/**
 * Widget textarea
 */

textarea = function(id, options) {
    var self = this;
    this.id = id;
    this.widget = $('#'+this.id);
    var defaults = {
    }
    
    options =  $.extend(defaults, options);
    
    this.init = function() {
    }
    
    this.disable = function() {
        $('#'+this.id).attr('disabled','disabled');
        $('#'+this.id).parent().addClass('ui-state-disabled');        
    }

    this.enable = function() {
        $('#'+this.id).removeAttr('disabled');
        $('#'+this.id).parent().removeClass('ui-state-disabled');
    }
    
    this.readonly = function(ro) {
        if (ro) {
            $('#'+this.id).attr('readonly','readonly');
        } else {
            $('#'+this.id).removeAttr('readonly');
        }        
    }
    
    this.init();
}
