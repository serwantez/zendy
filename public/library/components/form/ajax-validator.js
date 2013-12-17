/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function validate(formClass, id, url) {
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
    /*console.log(data);*/
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
            //console.log(resp);
            $("#"+id).parent().parent().find(".ui-state-error").remove();
            $("#"+id).parent().parent().append(getErrorHtml(resp[id],id));
            
            /*var widget = $("#"+id).parent().parent();
            widget.find(".ui-state-error").removeClass('ui-state-error');
            if (widget.tooltip())
                widget.tooltip("destroy");*/
        /*if (resp[id] && Object.keys(resp[id]).length>0) {
                $("#"+id).parent().addClass('ui-state-error');
                widget.tooltip({
                    tooltipClass: "ui-tooltip ui-state-error",
                    items: "#"+id,
                    content: function() {
                        return getErrorHtml(resp[id],id);
                    }
                });
            }*/
        }
    },'json');
    return ret;
}
    
function getErrorHtml(formErrors, id) {
    var o = '';
    if (formErrors && Object.keys(formErrors).length>0) {
        // style="list-style: none;"
        o = '<ul id="errors-'+id+'" class="ui-state-error ui-corner-all">';
        for(errorKey in formErrors) {
            o += '<li>' + formErrors[errorKey]+ '</li>';
        }
        o += '</ul>';
    }
    return o;
}
