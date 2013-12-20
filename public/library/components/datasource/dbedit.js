/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

dbEdit = function(id, params) {
    this.id = id;
    this.dataField = params.dataField;
    this.presentation = params.presentation;
    this.lastcheck=null;
    self=this;
    
    this.setData = function(value) {
        //console.log(this.id+' '+value);
        if($('#'+this.id).attr('texttype')=='textView') {
            $('#'+this.id).val(value);
            $('#'+this.id).trigger('change');
        } else if (params.type == 'ra' || params.type == 'rb') {
            $('input:'+$('#'+this.id).attr('type')+'[name='+this.id+']').val([value]);
            //wywołanie zdarzenia onchange dla odświeżenia kontrolek jquery
            $('input:'+$('#'+this.id).attr('type')+'[name='+this.id+']').trigger('change');
        } else {
            if (params.type == 'im') {
                if (!value) {
                    $('#'+this.id+'-img').attr('src',params.attribs.src + '/value/empty');
                } else {
                    var d = new Date();
                    $('#'+this.id+'-img').attr('src',params.attribs.src + '?' + d.getTime());
                }
            } else {
                if (params.type == 'tx') {
                    $('#'+this.id).html(value);
                } else {
                    if (params.type == 'tfv') {
                        $('#'+this.id).html(value);
                        $('#'+this.id+'RowNumbers').empty();
                        var rn = $('<pre></pre>');
                        var rowCount = value.split('<br />').length;
                        var s = '';
                        for (var i = 0; i<rowCount; i++) {
                            s = s + (i+1) + '<br />';
                            }
                        rn.html(s);
                        $('#'+this.id+'RowNumbers').append(rn);
                    } else {
                        $('#'+this.id).val(value);
                        $('#'+this.id).trigger('change');
                    }
                }
            }
        }
        //usuwa błędy walidacji poprzedniego rekordu
        $("#"+this.id).parent().find(".ui-state-error").remove();
    }
    
    this.getValue = function() {
        //zwraca wartość dla poszczególnych typów kontrolek
        if ($('#'+this.id)[0].nodeName=='INPUT' && $('#'+this.id).attr('type')=='radio') {
            //console.log(value);
            val = $('input:'+$('#'+this.id).attr('type')+'[name='+this.id+']:checked').val();
        } else if ($('#'+this.id)[0].nodeName=='INPUT' && $('#'+this.id).attr('type')=='checkbox') {
            val = $('input:'+$('#'+this.id).attr('type')+'[name='+this.id+']:checked').val();
            //wartość dla niezaznaczonego checkboxa pobierana jest z ukrytego pola
            if (!val) val = $('input:hidden'+'[name='+this.id+']').val();
        } else if (params.type == 'im') {
            val = $('#'+this.id+'-img').attr('src');
        } else if (params.type == 'se'){
            val = $('#'+this.id).val();
            if(val){
                val=val.replace(/[^0-9\-\.]/g,'');
            //console.log("wartosc val"+JSON.stringify(val));
            }            
        } else if(params.type == 'tx') {
            val = $('#'+this.id).html();
        } else {
            val = $('#'+this.id).val();
        }
        return val;    
    }

    this.disable = function() {
        if (params.type !== 'tx' && dc[params.type][this.id]) {
            dc[params.type][this.id].disable();
        }
    }

    this.enable = function() {
        if (params.type !== 'tx' && dc[params.type][this.id]) {
            dc[params.type][this.id].enable();
        }
    }
    
    this.readonly = function(ro) {
        if (params.type !== 'tx' && dc[params.type][this.id]) {
            dc[params.type][this.id].readonly(ro);
        }
    }
    
    this.setEvents = function(actionFunction) {
    }  
}