/**
 * Widget linkEdit
 */

linkEdit = function(id, options) {
    var self = this;
    this.id = id;
    this.widget = $('#'+this.id);
    this.widgetButton = $("#"+this.id+"-button");
    var defaults = {
        protocol: ''
    }
    
    options =  $.extend(defaults, options);
    
    this.init = function() {
        this.widgetButton
        .bind("mouseover", function(e, ui){
            $(this).addClass("ui-state-hover");
        })
        .bind("mouseout", function(e, ui){
            $(this).removeClass("ui-state-hover");
        });
        this.widget
        .bind("change", function(e, ui){
            var value = $(this).val();
            if (value)
                self.widgetButton.attr("href", options.protocol+value);
            else
                self.widgetButton.attr("href", "#");
        });
    }
    
    this.disable = function() {
        this.widget.attr('disabled','disabled');
        this.widget.parent().addClass('ui-state-disabled');        
    }

    this.enable = function() {
        this.widget.removeAttr('disabled');
        this.widget.parent().removeClass('ui-state-disabled');
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
