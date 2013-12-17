/**
 * Widget DatePicker2
 */

datepicker2 = function(id) {
    var self = this;
    this.id = id;
    this.widget = $('#'+this.id);
    this.widgetButton = $("#"+this.id+"-button");
    
   
    this.init = function() {
        this.widgetButton
        .bind("mouseover", function(e, ui){
            $(this).addClass("ui-state-hover");
        })
        .bind("mouseout", function(e, ui){
            $(this).removeClass("ui-state-hover");
        })
        .bind("mousedown", function(e, ui){
            e.preventDefault();
            opened = self.widget.datepicker("widget").is(":visible");
            self.widget.focus();
        })
        .bind("click", function(e, ui){
            if ( opened ) {
                self.widget.datepicker("hide");
            } else {
                self.widget.datepicker("show");
            }
        });
        this.widget.datepicker("option", "beforeShow", function(i) {
            if ($(i).attr('readonly')) {
                return false;
            }
        }
        );
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

