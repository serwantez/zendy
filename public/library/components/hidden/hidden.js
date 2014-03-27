/**
 * Widget hidden
 */

hidden = function(id, options) {
    var self = this;
    this.id = id;
    this.widget = $('#'+this.id);
    var defaults = {
    }
    
    options =  $.extend(defaults, options);
    
    this.init = function() {
    }
    
    this.disable = function() {
        this.widget.attr('disabled','disabled');
    }

    this.enable = function() {
        this.widget.removeAttr('disabled');
    }
    
    this.readonly = function(ro) {
        if (ro) {
            this.widget.attr('readonly','readonly');
        } else {
            this.widget.removeAttr('readonly');
        }        
    }
    
    this.init();
}
