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
    
    this.refresh = function() {
    }
    
    this.setData = function(data, params) {
        //this.widget.empty();
        if (params.emptyValue) {
            //this.widgetUl.append($("<li><a href='#'></a></li>"));
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
            
            /*var li = $("<li></li>")
            .attr('key', keyValues.join(';'))
            //.addClass('ui-state-default')
            .append($("<a href='#'></a>")
                .text(listValues.join(params.columnSpace)));

            //formatowanie warunkowe
            if (value['_format']) {
                li.addClass(value['_format']);
            }
            
            self.widgetUl
            .append(li);
              */      
        });
        this.refresh();
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
    
    this.refresh();
}
