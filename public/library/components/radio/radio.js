/**
 * Widget radio
 */

radio = function(id, options) {
    var self = this;
    this.id = id;
    this.widget = $('input[name="'+this.id+'"]');
    var defaults = {
    }
    
    options =  $.extend(defaults, options);
    
    this.init = function() {
    }
    
    this.disable = function() {
        $('input[name="'+this.id+'"]').attr('disabled','disabled');
    }

    this.enable = function() {
        $('input[name="'+this.id+'"]').removeAttr('disabled');
    }
    
    this.readonly = function(ro) {
        if (ro) {
            $('input[name="'+this.id+'"]').attr('readonly','readonly');
            this.disable();
        } else {
            $('input[name="'+this.id+'"]').removeAttr('readonly');
            this.enable();
        }        
    }
    
    this.init();
}
