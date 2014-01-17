/**
 * Widget ListBox
 */

listBox = function(id, options) {
    var self = this;
    this.id = id;
    this.widget = $('#'+this.id);
    this.widgetUl = $("#"+this.id+"-list");
   
    this.showValue = function(value) {
        $("#"+self.id+"-list li").removeClass("ui-state-active");
        $("#"+self.id+"-list li[key='"+value+"']").addClass("ui-state-active");
        $("#"+self.id+"-list li[key='"+value+"'] a").focus();
    }
    
    this.widget.bind("change", function(e, ui){
        self.showValue($(this).val());
    });
    
    this.refresh = function() {
        $("#"+this.id+"-list li a").bind("click", function(e, ui){
            self.widget.val($(this).parent().attr("key"));
            self.widget.trigger("change");
        });
    
        $("#"+this.id+"-list li")
        .bind("mouseover", function(e, ui){
            $(this).addClass("ui-state-hover");
        })
        .bind("mouseout", function(e, ui){
            $(this).removeClass("ui-state-hover");
        }); 
        
        this.widgetUl.find("a").bind("keydown", function(e) {
            //up
            if (e.keyCode == 38) { 
                var prev = $(e.currentTarget).closest("li").prev().find('a');
                if (prev) {
                    prev.focus();
                    prev.trigger("click");
                }
                return false;
            }
            //down
            if (e.keyCode == 40) { 
                var next = $(e.currentTarget).closest("li").next().find('a');
                if (next) {
                    next.focus();
                    next.trigger("click");
                }
                return false;
            }
        });
        
    }
    
    this.setData = function(data, params) {
        this.widget.empty();
        this.widgetUl.empty();
        if (params.emptyValue) {
            this.widgetUl.append($("<li><a href='#'></a></li>"));
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
            
            var li = $("<li></li>")
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
                    
        });
        this.refresh();
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
    
    
    this.refresh();
    
}

