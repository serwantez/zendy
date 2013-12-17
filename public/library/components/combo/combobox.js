/**
 * Widget ComboBox
 */

comboBox = function(id, options) {
    var self = this;
    this.id = id;
    this.widget = $('#'+this.id);
    //this.widgetUl = $("#"+this.id+"-list");
   
    this.setData = function(data, params) {
        this.widget.empty();
        if (params.emptyValue) {
            this.widget.append($("<option></option>"));
        }            
        $.each(data['rows'], function(key, value) {  
                
            var keyValues = new Array();
            for(var k in params.keyField) {
                keyValues[k] = value[params.keyField[k]];
            }
                
            var listValues = new Array();
            for(var i in params.listField) {
                listValues[i] = value[params.listField[i]];
            }
            self.widget
            .append($("<option></option>")
                .attr("value",keyValues.join(';'))
                .text(listValues.join(params.columnSpace))); 
        });
    }

    this.showValue = function(value) {
    
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
            this.widget.attr('disabled','disabled');
        } else {
            this.widget.removeAttr('disabled');
        }        
    }
    
    
}

