/**
 * Widget SortableListBox
 */

sortableListBox = function(id, options) {
    var self = this;
    this.id = id;
    this.widget = $('#'+this.id);
    this.widgetUl = $("#"+this.id+"-list");
    this.widgetSerializer = $("#"+this.id+"-serializer");
    this.oldPosition = null;
    this.newPosition = null;
    
    this.widgetUl
    .sortable(options)
    .on("sortstart", function(event, ui){
        if (ui !== undefined) {
            self.oldPosition = ui.item.index();
        //console.log('sortstart '+ self.oldPosition);
        }
    })
    .on("sortupdate", function(event, ui){
        if (ui !== undefined) {
            self.newPosition = ui.item.index();
        //console.log('sortupdate '+ self.newPosition);
        }
        self.widgetSerializer.val($(this).sortable("toArray",{
            attribute: 'value'
        }));
    });
    
    this.showValue = function(value) {
        $("#"+self.id+"-list li").removeClass("ui-state-highlight");
        $("#"+self.id+"-list li[key='"+value+"']").addClass("ui-state-highlight");
    }
    
    this.widget.bind("change", function(e, ui){
        self.showValue($(this).val());
    });
    
    this.refresh = function() {
        this.widgetUl.trigger("sortupdate");
        $("#"+this.id+"-list li a").bind("click", function(e, ui){
            self.widget.val($(this).parent().attr("key"));
            self.widget.trigger("change");
            $(this).focus();
        });
    
        $("#"+this.id+"-list li").bind("mouseover", function(e, ui){
            $(this).addClass("ui-state-hover");
        });
        
        $("#"+this.id+"-list li").bind("mouseout", function(e, ui){
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
            self.widgetUl
            .append($("<li></li>")
                .attr('key', keyValues.join(';'))
                //.addClass('ui-state-default')
                .append($("<a href='#'></a>")
                    .text(listValues.join(params.columnSpace))));
        });
        this.refresh();
    }
    
    this.refresh();    
    
}

