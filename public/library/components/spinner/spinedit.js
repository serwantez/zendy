/**
 * Widget spinedit
 */

spinedit = function(id, options) {
    var self = this;
    this.id = id;
    this.widget = $('#'+this.id);
    var defaults = {
    }
    
    options =  $.extend(defaults, options);
    
    this.init = function() {
        this.widget.spinner("option", "start", function(event, ui) {
            var attrRO = self.widget.attr('readonly');
            if (typeof attrRO !== 'undefined' && attrRO !== false) {
                return false;
            }
        });
    }
    
    this.disable = function() {
        this.widget.spinner("option", "disabled", true);
    }

    this.enable = function() {
        this.widget.spinner("option", "disabled", false);
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
