/**
 * Widget IconComboBox
 */

iconComboBox = function(id, options) {
    var self = this;
    this.options = {
        position: {
            my: "left top",
            at: "left bottom",
            collision: "none"
        }
    };    
    this.id = id;
    this.cancelBlur = false;
    this.widget = $('#'+this.id);
    this.widgetUl = $("#"+this.id+"-list");
    this.widgetText = $("#"+this.id+"-text");
    this.container = this.widgetText.parent();
    this.widgetButton = $("#"+this.id+"-button");
    
   
    this.showValue = function(value) {
        $("#"+self.id+"-list li a").removeClass("ui-state-active");
        $("#"+self.id+"-list li[key='"+value+"'] a").addClass("ui-state-active");
        this.widgetText.val($("#"+self.id+"-list li[key='"+value+"']").text());
    }
    
    this._previous = function(event) {
        if (this.widgetUl.is(":visible")) {
            event.preventDefault();
            var focused = $("#"+this.id+"-list li a.ui-state-focus");
            this.widgetUl.find("li a.ui-state-focus").removeClass("ui-state-focus");            
            if (focused.length>0) { 
                focused.trigger("mouseout");
                var el = focused.parent().prev().find('a');
            } else {
                return;
            }
            if (el.length>0) {
                this.cancelBlur = true;
                el.focus();
                el.trigger("mouseover");
                this.widgetText.focus();
            }
        }
        return;
    };

    this._next = function(event) {
        if (this.widgetUl.is(":visible")) {
            event.preventDefault();
            var focused = $("#"+this.id+"-list li a.ui-state-focus");
            if (focused.length>0) {
                focused.trigger("mouseout");
                var el = focused.parent().next().find('a');
            } else {
                var el = $("#"+this.id+"-list li").first().find('a');
            }
            if (el.length>0) {
                this.cancelBlur = true;
                el.focus();
                el.trigger("mouseover");
                this.widgetText.focus();
            }
        } else this._show(event);
        return;
    };
    
    this._show = function() {
        this.widgetUl.show();
        //set position
        this.widgetUl.position($.extend({
            of: this.container
        }, this.options.position));    
    };
    
    this._select = function(event) {
        if (this.widgetUl.is(":visible")) {
            event.preventDefault();
            var v = $("#"+this.id+"-list li a.ui-state-focus").parent().attr('key');
            $('#'+this.id).val(v);
            $('#'+this.id).trigger('change');
            this.widgetUl.hide();
        }
    }
    
    this.init = function() {
        $('#'+this.id)
        .bind("change", function(e, ui){
            self.showValue($(this).val());
        });

        this.widgetButton
        .bind("mouseover", function(e, ui){
            if (!$(this).hasClass("ui-state-disabled"))
                $(this).addClass("ui-state-focus");
        })
        .bind("mouseout", function(e, ui){
            if (!$(this).hasClass("ui-state-disabled"))
                $(this).removeClass("ui-state-focus");
        })
        .bind("mousedown", function(e, ui){
            if (!$(this).hasClass("ui-state-disabled")) {
                e.preventDefault();
                opened = self.widgetUl.is(":visible");
                self.widgetText.focus();
            }
        })
        .bind("click", function(e, ui) {
            var attrRO = $('#'+self.id).attr('readonly');
            //console.log(attrRO);
            if (!$(this).hasClass("ui-state-disabled") && (typeof attrRO == 'undefined' || attrRO == false)) {
                if ( opened ) {
                    self.widgetUl.hide();
                } else {
                    self._show(e);
                }
                self.cancelBlur = false;
            }
        });
    
        this.widgetText
        .bind("blur", function(e, ui){
            if (self.cancelBlur) {
                self.cancelBlur = false;
                return; 
            }
            if (self.widgetUl.is(":visible")) {
                self.widgetUl.hide();
            }
        })
        .bind("keydown", function(e, ui){
            var keyCode = $.ui.keyCode;
            switch(e.keyCode) {
                case keyCode.UP:
                    self._previous(e);
                    break;
                case keyCode.DOWN:
                    self._next(e);
                    break;
                case keyCode.ENTER:
                case keyCode.NUMPAD_ENTER:
                    self._select(e);
                    break;
                case keyCode.ESCAPE:
                    if (self.widgetUl.is(":visible")) {
                        self.widgetUl.hide();
                        e.preventDefault();
                    }
                    break;            
            }
        });
    
        this.widgetUl
        .bind("mousedown", function(e, ui){
            self.widgetText.focus();
        });
        
        this.refresh();
    }
        
    this.refresh = function() {
        $("#"+this.id+"-list li a")
        .bind("mousedown", function(e, ui){
            e.preventDefault();
            self.cancelBlur = true;
            self.widget.val($(this).parent().attr("key"));
            self.widget.trigger("change");
            self.widgetUl.hide();
            self.widgetText.focus();
        })
        .bind("mouseover", function(e, ui){
            $("#"+self.id+"-list li a.ui-state-focus").removeClass("ui-state-focus");
            $(this).addClass("ui-state-focus");
        })
    ; 
        
    }
    
    this.setData = function(data, params) {
        $('#'+this.id).empty();
        this.widgetText.empty();
        this.widgetUl.empty();
        if (params.emptyValue) {
            this.widgetUl.append($("<li><a href='#'></a></li>"));
        }            
        $.each(data['rows'], function(key, value) {  
            //console.log(value);
                
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
            .addClass('ui-menu-item')
            .append($("<a href='#'></a>")
                .addClass('ui-corner-all')
                .text(listValues.join(params.columnSpace))
                .append($('<span></span>')
                    .addClass('ui-icon '+keyValues.join(';'))
                    )
                );

            //formatowanie warunkowe
            if (value['_format']) {
                li.addClass(value['_format']);
            }
            
            self.widgetUl
            .append(li);
            
        });
        this.refresh();
    };

    this.disable = function() {
        $('#'+this.id).attr('disabled', 'disabled');
        this.widgetText
        .attr('disabled', 'disabled')
        .parent()
        .addClass('ui-state-disabled');
        this.widgetButton.addClass('ui-state-disabled');
    };
    
    this.enable = function() {
        $('#'+this.id).removeAttr('disabled');
        this.widgetText
        .removeAttr('disabled')
        .parent()
        .removeClass('ui-state-disabled');
        this.widgetButton.removeClass('ui-state-disabled');
    };
    
    this.readonly = function(ro) {
        if (ro) {
            $('#'+this.id).attr('readonly','readonly');
            $('#'+this.id+"-text").attr('readonly','readonly');
        } else {
            $('#'+this.id).removeAttr('readonly');
            $('#'+this.id+"-text").removeAttr('readonly');
        }        
    }
    
    
    this.init();
    
}

