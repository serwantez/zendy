/**
 * Widget radiobutton
 */

radiobutton = function(id, options) {
    var self = this;
    this.id = id;
    this.widget = $('input[name="'+this.id+'"]');
    var defaults = {
    }
    
    options =  $.extend(defaults, options);
    
    this.init = function() {
        /*this.widget.button("option", "click", function(e) {
            e.preventDefault();
            return false;
        });*/
    }
    
    this.disable = function() {
        $('#'+this.id+'-container').buttonset({
            disabled: true
        });
        $('input[name="'+this.id+'"]').attr('disabled','disabled');
    }

    this.enable = function() {
        $('#'+this.id+'-container').buttonset({
            disabled: false
        });
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
