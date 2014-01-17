/**
 * Skrypt do ajaksowej walidacji formularza
 */

addErrorTooltip = function(id, errors) {
    $("#"+id+"-container").tooltip({
        items: "#"+id+"-container",
        tooltipClass: "ui-state-error",
        content: function() {
            var errorsjoined = "";
            for (var j in errors) {
                errorsjoined += errors[j]+"<br />";
            }
            return errorsjoined;  
        },
        position: {
            my: 'left+10 center', 
            at: 'right center'
        }
    });
}

disableTooltip = function(id) {
    if ($("#"+id+"-container").tooltip())
        $("#"+id+"-container").tooltip('disable');
}

validate = function (formClass, id, url) {
    var data = {
        form: formClass.join('\\')
    };
    var tags = "input[type='text'], input[type='password'], input[type='radio']:checked, input[type='hidden'], input[type='checkbox']:checked, textarea";
    $(tags).each(function(i){
        if ($(this).attr('name')) {
            if ($(this).attr('name').indexOf('[]')>-1) {
                if (!data[$(this).attr('name')]) data[$(this).attr('name')] = [];
                data[$(this).attr('name')][i] = $(this).val();
            } else {
                data[$(this).attr('name')] = $(this).val();
            }
        }
    });
    
    $("select option:selected").each(function(i){
        if ($(this).parent().attr('name')) {
            if ($(this).parent().attr('name').indexOf('[]')>-1) {
                if (!data[$(this).parent().attr('name')]) data[$(this).parent().attr('name')] = [];
                data[$(this).parent().attr('name')][i] = $(this).val();
            }
            else {
                data[$(this).parent().attr('name')] = $(this).val();
            }
        }
    });    

    var ret = new Array();
    //wysłanie asynchronicznego zapytania ajax'em
    $.ajaxSetup({
        async: true
    });
    $.post(url,data,function(resp){
        ret = resp;
        if (id) {
            if (id.indexOf('[]')>-1) {
                id = id.substring(0,id.indexOf('[]'))
            }
            if (resp[id] && Object.keys(resp[id]).length>0) {
                $("#"+id+"-container").addClass('ui-state-error');
                addErrorTooltip(id, resp[id]);
            } else {
                $("#"+id+"-container").removeClass('ui-state-error');
                disableTooltip(id);
            }
        }
    },'json');
    return ret;
}
