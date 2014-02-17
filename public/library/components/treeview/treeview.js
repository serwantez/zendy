/**
 * Widget Treeview
 */

treeview = function(id, options) {
    var self = this;
    this.id = id;
    this.widget = $('#'+this.id);
    this.widgetUl = $("#"+this.id+"-list");
    var expandedNode = new Array();
    
    defaults = {
        icons: {
            handleExpanded: "ui-icon-triangle-1-se",
            handleCollapsed: "ui-icon-triangle-1-e"
        },
        hide: "slideUp",
        show: "slideDown"
    };
    this.options =  $.extend(defaults, options);
   
    this.showValue = function(value) {
        $("#"+this.id+"-list li a").removeClass("ui-state-active");
        $("#"+this.id+"-list li a[key='"+value+"']").addClass("ui-state-active");
    }
    
    this.widget.bind("change", function(e, ui){
        self.showValue($(this).val());
    });
    
    this.refresh = function() {
        $("#"+this.id+"-list li a")
        .bind("click", function(e, ui){
            self.widget.val($(this).attr("key"));
            self.widget.trigger("change");
            $(this).focus();
        })
        .bind("contextmenu", function(e, ui){
            self.widget.val($(this).attr("key"));
            self.widget.trigger("change");
            $(this).focus();
        })
        .bind("mouseover", function(e, ui){
            $(this).addClass("ui-state-hover");
        })
        .bind("mouseout", function(e, ui){
            $(this).removeClass("ui-state-hover");
        });

        this.widgetUl.find("span.ui-tree-node-handle").bind("click", function(e) {
            self.toggleNode( $(e.currentTarget).closest("li") );
        });
        
        this.widgetUl.find("a").bind("keydown", function(e) {
            //space
            if (e.keyCode == 32) { 
                self.toggleNode( $(e.currentTarget).closest("li") );
            }
            //left
            if (e.keyCode == 37) { 
            //to do
            }
            //right
            if (e.keyCode == 39) { 
            //to do
            }
            //up
            if (e.keyCode == 38) { 
                var prev = $(e.currentTarget).closest("li").prev().children('a');
                if (prev) {
                    prev.focus();
                    prev.trigger("click");
                }
                return false;
            }
            //down
            if (e.keyCode == 40) { 
                var next = $(e.currentTarget).closest("li").next().children('a');
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
        var depth = 0;
        var node = this.widgetUl;
        var hasChildren;
        var icon;
        var j = 0;
        //console.log('set data for control '+this.id);
        //console.log(data);
        if (data['rows']) {
            $.each(data['rows'], function(key, value) {
                if (j==0) depth = value[params.depthField];
                if ((value[params.rightField]-value[params.leftField])>1) 
                    hasChildren = true; 
                else 
                    hasChildren = false;
                if (value[params.depthField]>depth) {
                    node = $("<ul></ul>").appendTo(node.children().last());
                } else {
                    if ((value[params.depthField]<depth)) {
                        for(var i = value[params.depthField]; i<depth; i++) {
                            node = node.parent().parent();
                        }
                    }
                }
                
                depth = value[params.depthField];
                var keyValues = new Array();
                for(var k in params.keyField) {
                    keyValues[k] = value[params.keyField[k]];
                }
                var key = keyValues.join(';');

                var listValues = new Array();
                for(var i in params.listField) {
                    listValues[i] = value[params.listField[i]];
                }
                
                var anchor = $("<a href='#'></a>")
                .addClass('ui-corner-all')
                .addClass('ui-tree-node-icon')
                .attr('key', key)
                .text(listValues.join(params.columnSpace));
                if (value[params.iconField]) {
                    icon = value[params.iconField];
                }
                else {
                    if (hasChildren) 
                        icon = params.icons['nodeExpanded'];
                    else
                        icon = params.icons['leaf'];
                }
                anchor.append($("<span></span>")
                    .addClass('ui-icon '+icon));
                var listItem = $("<li></li>")
                .addClass('ui-tree-node')
                .append(anchor);
                if (hasChildren) {
                    var handleIcon;
                    //expanded[key] != 'undefined' && 
                    if (expandedNode[key] == false) {
                        listItem.attr('aria-expanded','false');
                        handleIcon = params.icons['handleCollapsed'];
                    //console.log(key+" powinien być schowany");
                    }
                    else {
                        listItem.attr('aria-expanded','true');
                        handleIcon = params.icons['handleExpanded'];
                    }                
                    listItem
                    .append($("<span></span>")
                        .addClass('ui-tree-node-handle ui-icon '+handleIcon)); 
                }
                else
                    listItem.addClass('ui-tree-leaf');
                node.append(listItem);
                if (node.parent().attr("aria-expanded") == "false") {
                    node.hide();
                } 
                j++;
            }); 
        }
        this.refresh();
    }
    
    this.toggleNode = function(node) {
        var expanded = !node.children("span.ui-tree-node-handle." + self.options.icons.handleExpanded).length;
        var groupContainer = node
        .children("span.ui-tree-node-handle").toggleClass(self.options.icons.handleCollapsed + " " + self.options.icons.handleExpanded).end()
        .children("ul");
        node.attr('aria-expanded',expanded);                
        if (groupContainer.children("li").length) groupContainer[expanded ? "show" : "hide"]();
        var key = node.find("a").attr("key");
        if (key) {
            expandedNode[key] = expanded;
        }
    }
    
    this.disable = function() {
        $('#'+this.id).attr('disabled','disabled');
        $('#'+this.id).parent().addClass('ui-state-disabled');        
    }

    this.enable = function() {
        $('#'+this.id).removeAttr('disabled');
        $('#'+this.id).parent().removeClass('ui-state-disabled');
    }
    
    this.readonly = function(ro) {
        if (ro) {
            $('#'+this.id).attr('readonly','readonly');
        } else {
            $('#'+this.id).removeAttr('readonly');
        }        
    }
    
    
    this.refresh();    
    
}

