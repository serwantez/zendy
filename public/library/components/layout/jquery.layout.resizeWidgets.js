/**
 *	UI Layout Callback: resizePaneAccordions
 *
 *	This callback is used when a layout-pane contains 1 or more accordions
 *	- whether the accordion a child of the pane or is nested within other elements
 *	Assign this callback to the pane.onresize event:
 *
 *	SAMPLE:
 *	$("#elem").tabs({ show: $.layout.callbacks.resizePaneAccordions });
 *	$("body").layout({ center__onresize: $.layout.callbacks.resizePaneAccordions });
 *
 *	Version:	1.0 - 2011-07-10
 *	Author:		Kevin Dalman (kevin.dalman@gmail.com)
 */
;
(function ($) {
    var _ = $.layout;

    // make sure the callbacks branch exists
    if (!_.callbacks) _.callbacks = {};

    _.callbacks.resizeWidgets = function (x, ui) {
        // may be called EITHER from layout-pane.onresize OR tabs.show
        var $P = ui.jquery ? ui : $(ui.panel);
        
        // find all VISIBLE accordions inside this pane and resize them
        $P.find(".ui-accordion:visible").each(function(){
            var $E = $(this);
            if ($E.data("accordion"))		// jQuery <= 1.8
                $E.accordion("resize");
            if ($E.data("ui-accordion"))	// jQuery >= 1.9
                $E.accordion("refresh");
        });
        
        if (grid = $('.ui-jqgrid-btable:visible')) {
            grid.each(function(index) {
                gridId = $(this).attr('id');
                gridParentWidth = $('#gbox_' + gridId).parent().width();
                $('#' + gridId).setGridWidth(gridParentWidth);
                
                var gridParentHeight = $("#gbox_"+gridId).parent().height();
                var a = $("#gbox_"+gridId).height();
                var b = $("#gbox_"+gridId+" .ui-jqgrid-bdiv").height();
                var newHeight = gridParentHeight-a+b;
                //alert(gridParentHeight);
                $('#' + gridId).setGridHeight(newHeight);
            });
        };
        
        
        
    };

})( jQuery );