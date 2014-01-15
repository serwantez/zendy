/**
 * Widget Grid
 */

grid = function(id, options) {
    var self = this;
    this.id = id;
    this.widget = $('#'+this.id);
    var container = "#"+this.id+"-container";
    this.widgetGrid = $("#"+this.id+"-container");
    var sorting = false;
    var defaults = {
        icon: {
            asc: 'ui-icon-triangle-1-n',
            desc: 'ui-icon-triangle-1-s',
            selected: 'ui-icon-triangle-1-e'
        },
        firstColWidth: 19,
        dblClickRow: function() {}
    };
    options =  $.extend(defaults, options);
   
    this.showValue = function(value) {
        $(container+" .ui-grid-body tr").removeClass("ui-state-highlight");
        $(container+" .ui-grid-body tr td:first-child")
        .html("");
        $(container+" .ui-grid-body tr[key='"+value+"']")
        .addClass("ui-state-highlight");
        $(container+" .ui-grid-body tr[key='"+value+"'] td:first-child")
        .append($("<span></span>")
            .addClass('ui-icon '+options.icon.selected)
            );
    }
    
    this.getFieldValue = function(fieldIndex) {
        var value = this.widget.val();
        var fieldValue = $(container+" .ui-grid-body tr[key='"+value+"']")
        .children('td')
        .eq(fieldIndex)
        .html();
        return fieldValue;
    }
    
    this.widget.bind("change", function(e, ui){
        self.showValue($(this).val());
    });
    
    this.setData = function(data, params) {
        this.widget.empty();
        var widgetGridBody = $(container+" .ui-grid-body table tbody");
        widgetGridBody.empty();
        
        var trf = $("<tr></tr>")
        .addClass('ui-widget-content')
        .addClass('ui-grid-firstrow')
        .append($("<td></td>")
            .css('height','0px')
            .css('width',options.firstColWidth+'px')
            )
        ;
        for(var i in params.listField) {
            var width = $(container+" .ui-grid-header colgroup").find('col').eq(i).attr('width');
            trf.append($("<td></td>")
                .css('height','0px')
                .css('width',width)
                .css('min-width',width)
                );
        }
        widgetGridBody.append(trf);
        $.each(data['rows'], function(key, value) {
            var keyValues = new Array();
            for(var k in params.keyField) {
                keyValues[k] = value[params.keyField[k]];
            }
            /*var an = $("<a></a>")
                .attr('href','#')
                .attr('key',key);*/
            var tr = $("<tr></tr>")
            .attr('key', keyValues.join(';'))
            .addClass('ui-widget-content')
            ;
            
            tr.append($("<td></td>")
                .addClass('ui-state-default')
                );
                    
            //formatowanie warunkowe
            if (value['_format']) {
                tr.addClass(value['_format']);
            }
            for(var i in params.listField) {
                var cellValue = (value[params.listField[i]] ? value[params.listField[i]] : '');
                tr.append($("<td></td>")
                    .addClass(params.columnsOptions[params.listField[i]].align)
                    .html(cellValue)
                    );
            } 
            widgetGridBody.append(tr);
        });
        if (sorting) {
            $(container+" .ui-grid-header th div .ui-grid-header-sorticon").empty();
            $(container+" .ui-grid-header th").attr('sort', 'clear');
            $.each(data['sort'], function(key, value) {
                $("#"+self.id+"_"+value.field).attr('sort', value.direction);
                var ths = $("#"+self.id+"_"+value.field+" div .ui-grid-header-sorticon");
                ths.append($("<span></span>")
                    .addClass('ui-icon '+options.icon[value.direction])
                    );
            });
        }
        this.refresh();        
    }
    
    this.onRefresh = function() {
    };

    this.refresh = function() {
        //wiersze        
        $(container+" .ui-grid-body tr").bind("click", function(e, ui){
            if ($(this).attr("key") != self.widget.val()) {
                self.widget.val($(this).attr("key"));
                self.widget.trigger("change");
            }
        });
        
        $(container+" .ui-grid-body tr").bind("dblclick", function(e, ui){
            options.dblClickRow();
        });
        
        $(container+" .ui-grid-body tr").bind("mouseover", function(e, ui){
            $(this).addClass("ui-state-hover");
        });
        
        $(container+" .ui-grid-body tr").bind("mouseout", function(e, ui){
            $(this).removeClass("ui-state-hover");
        });
        
        this.onRefresh();
       
    }
    
    getNextSortDirection = function(direction) {
        switch (direction)
        {
            case 'asc':
                return 'desc';
            case 'desc':
                return 'clear';
            default:
                return 'asc';
        }
    }
    
    this.setSorting = function(columns, ds, form) {
        for (var i in columns) {
            var col = columns[i];
            sorting = true;
            //nazwy kolumn w nagłówku
            var th = $("#"+this.id+"_"+col);

            th.bind("mouseover", function(e, ui){
                $(this).addClass("ui-state-hover");
            });
        
            th.bind("mouseout", function(e, ui){
                $(this).removeClass("ui-state-hover");
            });
            
            th.bind("click", {
                dataAction: 'sortAction',
                actionType: 'standard',
                field: col
            }, function(e, ui){
                e.data.direction = getNextSortDirection($(this).attr('sort'));
                //console.log(e.data.field+': '+$(this).attr('sort')+', next: '+e.data.direction);
                df[form].getDataSource(ds).executeAction(e);
            });

            var thd = $("#"+this.id+"_"+columns[i]+" div");
        
            thd.addClass('ui-grid-sortable');
        
            var icons = $("<span></span>")
            .addClass('ui-grid-header-sorticon')
            .append($("<span></span>")
                //.addClass('ui-icon ui-icon-triangle-1-n')
                );
            thd.append(icons);
        }
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

