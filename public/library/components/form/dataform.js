/**
 * Skrypt klasy ZendY\Db\Form 
 * @author Piotr Zając
 */

/**
 * tablica globalna formularzy
 */
var df = new Array();

/**
 * tablica globalna kontrolek
 */
var dc = new Array();
//calendar
dc["ca"] = new Array();
//combobox
dc["cb"] = new Array();
//checkbox
dc["ch"] = new Array();
//datepicker
dc["dp"] = new Array();
//edit, iconedit
dc["ed"] = new Array();
//grid
dc["gr"] = new Array();
//hidden
dc["hd"] = new Array();
//iconcombobox
dc["ic"] = new Array();
//image
dc["im"] = new Array();
//imageview
dc["iv"] = new Array();
//linkedit
dc["le"] = new Array();
//listbox
dc["lb"] = new Array();
//map
dc["mp"] = new Array();
//radio
dc["ra"] = new Array();
//radiobutton
dc["rb"] = new Array();
//spinedit
dc["se"] = new Array();
//sortablelistbox
dc["sl"] = new Array();
//textarea
dc["ta"] = new Array();
//textfileview
dc["tfv"] = new Array();
//treeview
dc["tv"] = new Array();

dataForm = function(id, className, url, message) {
    
    var self = this;

    this.id = id;
    
    this.className = className.join('\\');
    
    this.url = url;
    
    var ds = new Array();
    
    this.addDataSource = function(dataSource){
        ds[dataSource.id] = dataSource;
    }
    
    this.getDataSource = function(id) {
        return ds[id];
    }

    this.getDataSources = function() {
        return ds;
    }
    
    this.clearErrors = function(d) {
        var elements = this.getDataSource(d).de;
        for (var i in elements) {
            $("#"+i+"-container").removeClass("ui-state-error");
        }
    }
    
    this.addErrorTooltip = function(id, errors) {
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
    
    this.showErrors = function(d, errors) {
        var elements = this.getDataSource(d).de;
        for (var i in elements) {
            if (errors[i]) {
                $("#"+i+"-container").addClass("ui-state-error");
                this.addErrorTooltip(i, errors[i]);
            }
        }
    }
 
    this.refreshControls = function(data, action) {
        for(var d in data) {
            if (ds[d]) {
                this.clearErrors(d);
                //komunikaty błędów dla zbioru głównego
                if (data[d]['errors']) {
                    this.showErrors(d, data[d]['errors']);
                    alert(message);
                }
                //powiadomienia
                if (data[d]['messages']) {
                    msg = data[d]['messages'];
                    s = '';
                    if (typeof msg === 'object') {
                        for (var i in msg) {
                            if (i>0) {
                                s += ' ';
                            }
                            if (typeof msg[i] === 'object') {
                                for (var j in msg[i]) {
                                    s += msg[i][j]+'. ';
                                }
                            } else {
                                s += msg[i];
                            }
                        }
                    } else {
                        s = msg;
                    }
                    alert(s);
                }                
                //kontrolki nawigacyjne
                var cdata = new Array();
                for(var n in ds[d].dn) {
                    if (data[d]['multi']) {
                        cdata['list'] = data[d]['multi'][n]['list'];
                        cdata['rows'] = data[d]['multi'][n]['data'];
                        cdata['sort'] = data[d]['sort'];
                        ds[d].dn[n].refresh(cdata);
                    }

                    var value = new Array();
                    for(var k in ds[d].dn[n].keyField) {
                        value[k] = data[d]['data'][ds[d].dn[n].keyField[k]];                        
                    }
                    ds[d].dn[n].setValue(value.join(';'));
                    
                    if(data[d].expr['state']>0) {
                        ds[d].dn[n].enable();
                    }
                }
            }
        }
        for(var d in data) {
            if (ds[d]) {
                //kontrolki edycyjne
                for(var j in ds[d].de) { 
                    if (!(action == 'saveAction' && data[d]['errors'])) {
                        if (ds[d].de[j].id in data[d]['data']) {
                            ds[d].de[j].setData(data[d]['data'][ds[d].de[j].id]);
                        }
                        else {
                            ds[d].de[j].setData('');
                        }
                        if(data[d].expr['state']==0) {
                            ds[d].de[j].disable();
                        } else {
                            ds[d].de[j].enable();
                            if(data[d].expr['state']==2 || data[d].expr['state']==3 && data[d].expr['count']>0) {
                                ds[d].de[j].readonly(false);
                            } else {
                                ds[d].de[j].readonly(true);
                            }
                        }
                    }
                }

                //wyrażenia
                for(var x in ds[d].dex) {
                    ds[d].dex[x].setData(data[d]['expr'][ds[d].dex[x].expr]);
                    if(data[d].expr['state']==0) {
                        ds[d].dex[x].disable();
                    } else {
                        ds[d].dex[x].enable();
                    }
                }
            
                //przyciski nawigacyjne
                for(var b in ds[d].da) {
                    if (data[d]['navigator'][ds[d].da[b].params.dataAction] && data[d].expr['state']>0) {
                        ds[d].da[b].enable();
                    }
                    else {
                        ds[d].da[b].disable();
                    }
                }
                
                //filtry
                for(var f in ds[d].df) {
                    if (data[d]['filter'][ds[d].df[f].id]) {
                        ds[d].df[f].setData(data[d]['filter'][ds[d].df[f].id][ds[d].df[f].dataField]['value']);
                    } else ds[d].df[f].clear();
                    if(data[d].expr['state']>0) {
                        ds[d].df[f].enable();
                    }                    
                } 

            } else {
                console.log('Brak źródła danych '+d);
            }
        }        
    }
 
    openDialog = function() {
        $('#'+self.id+'_dialog').dialog('open');
    }
    
    closeDialog = function() {
        $('#'+self.id+'_dialog').dialog('close');
    }
 
    this.open = function() {
        if (Object.keys(ds).length > 0) {
            var params = {
                dataAction: 'init',
                formId: this.id,
                form: this.className
            };
            openDialog();
            $.post(self.url, params, function(data) {
                //parsowanie odpowiedzi
                data = $.parseJSON(data);
                self.refreshControls(data, params.dataAction);
                closeDialog();
            });
        }        
    }
    
    this.multiaction = function(sources, event) {
        if (Object.keys(ds).length > 0) {
            var params = event.data;
            params.id = sources;
            params.formId = this.id;
            params.form = this.className;
            openDialog();
            $.post(self.url, params, function(data) {
                try {
                    //parsowanie odpowiedzi
                    data = $.parseJSON(data);
                    self.refreshControls(data, params.dataAction);
                } catch(e) {
                    alert(data);
                }
                closeDialog();
            });
        }        
    }
}
