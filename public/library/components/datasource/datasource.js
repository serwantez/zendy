/**
 * skrypt komponentu ZendY\Db\DataSource 
 * wywołujący wykonanie zdarzeń na zbiorach danych
 * oraz przekazywanie danych wynikowych do komponentów wizualnych
 * @author Piotr Zając
 */

dataSource = function(id, url, formClass, formId, dialog) {
    
    var self = this;

    this.id = id;
        
    //tablica edycyjnych kontrolek bazodanowych
    this.de = new Array();
    
    //tablica nawigacyjnych kontrolek bazodanowych
    this.dn = new Array();

    //tablica przycisków akcji
    this.da = new Array();

    //tablica kontrolek - wyrażeń
    this.dex = new Array();

    //tablica kontrolek filtrujących
    this.df = new Array();
    
    this.active = false;
    
    this.url = url;
    
    this.formClass = formClass.join('\\');
    
    this.formId = formId;
    
    /**
     * zwraca wartości pól ze zbioru danych dla bieżącego rekordu
     */
    this.getFieldsValues = function() {
        var i;
        var ret = {};
        for(i in this.de) {
            if (!this.de[i].presentation) {
                ret[this.de[i].dataField] = this.de[i].getValue();
            }
        }
        return ret;
    }

    /**
     * zwraca wartości kontrolek bazodanowych
     */
    this.getElementsValues = function() {
        var i;
        var ret = {};
        for(i in this.de) {
            if (!this.de[i].presentation)
                ret[this.de[i].id] = this.de[i].getValue();
        }
        return ret;
    }

    /**
     * zwraca wartości z kontrolek fitrujących
     */
    this.getFilters = function() {
        var ret = {};
        for(var f in this.df) {
            ret[this.df[f].dataField] = this.df[f].getFilter();
        }
        return ret;
    }

    this.disableBtns = function() {
        for(var b in this.da) {
            this.da[b].disable();
        }
    }

    this.enableBtns = function() {
        for(var b in this.da) {
            this.da[b].enable();
        }
    }

    this.disableEdits = function() {
        for(var i in this.de) {
            this.de[i].disable();
        }
    }

    this.enableEdits = function() {
        for(var i in this.de) {
            this.de[i].enable();
        }
    }

    this.disableNavEdits = function() {
        for(var i in this.dn) {
            this.dn[i].disable();
        }
    }

    this.enableNavEdits = function() {
        for(var i in this.dn) {
            this.dn[i].enable();
        }
    }

    this.disableExpr = function() {
        for(var i in this.dex) {
            this.dex[i].disable();
        }
    }

    this.enableExpr = function() {
        for(var i in this.dex) {
            this.dex[i].enable();
        }
    }
    
    openDialog = function() {
        $('#'+self.id+'_dialog').dialog('open');
    }
    
    closeDialog = function() {
        $('#'+self.id+'_dialog').dialog('close');
    }
    
    beforeAction = function() {
        //na czas wykonywania akcji wyłączamy możliwość wywołania innej akcji
        if(dialog){
            openDialog();
        }
    }
    
    afterAction = function() {
        //włączenie kontrolek
        if(dialog){
            closeDialog();
        }
    }
    
    /**
     * wykonanie akcji na zbiorze danych i pobranie bieżącego rekordu (strony rekordów)
     */
    this.executeAction = function(event) {
  
        var params = event.data;
        var action = params.dataAction;
        var actionType = params.actionType;
        params.id = self.id;
        params.formId = self.formId;
        params.form = self.formClass;
        //console.log(params);
            
        if (actionType == 'save') {
            params.fieldsValues = self.getFieldsValues();
            params.elementsValues = self.getElementsValues(); 
        }
        
        if (actionType == 'filter') {
            params.filter = {
                user: self.getFilters()
            };
        }
        
        if (actionType == 'generateFile' || actionType == 'report') {
            beforeAction();
            var url = self.url;
            for (key in params) {
                if(url.indexOf("?") > -1)
                    url += '&' + key + '=' + params[key];
                else
                    url += '?' + key + '=' + params[key];
            }
            if (actionType == 'generateFile')
                window.location.href = url;
            else 
                window.open(url, 'Report'); 
            afterAction();
        } else {
            if (actionType == 'confirm') {
                if (confirm("Czy na pewno wykonać tą akcję? Operacja jest nieodwracalna.") == false) {
                    action = 'cancelAction';
                    params.dataAction = action;                    
                }
            }
            beforeAction();
            //wysłanie asynchronicznego zapytania ajax'em
            /*$.ajaxSetup({
                async: false
            });*/
            $.post(self.url, params, function(data) {
                //parsowanie odpowiedzi
                data = $.parseJSON(data);
                df[self.formId].refreshControls(data, action);
                afterAction();
            });
        }
    }
    
    /**
     * metoda otwierająca 
     */
    this.open = function() {
        //console.log('Otwieram zbiór '+this.id);
        var event = {
            data: {
                dataAction: 'openAction',
                actionType: 'standard'
            }            
        };
        this.executeAction(event);

        var controls = new Array(this.dn, this.de, this.da, this.dex, this.df, this.dtv, this.dg);
        for(var i in controls) {
            for(var j in controls[i]) {
                controls[i][j].setEvents(this.executeAction);
            }
        }
        this.active = true;
    }
    
    this.addEdit = function(id, params) {
        this.de[id] = new dbEdit(id, params);
        this.de[id].setEvents(this.executeAction);
    }

    this.addNavi = function(id, params) {
        if(params['staticRender']){}
        else {
            this.dn[id] = new dbNavi(id, params);
            this.dn[id].setEvents(this.executeAction);
        }
    }

    this.addAction = function(id, params) {
        this.da[id] = new dbAction(id, params);
        this.da[id].setEvents(this.executeAction);
    }

    this.addExpr = function(id, expr) {
        this.dex[id] = new dbExpr(id, expr);
        this.dex[id].setEvents(this.executeAction);
    }

    this.addFilter = function(id, params) {
        this.df[id] = new dbFilter(id, params);
        this.df[id].setEvents(this.executeAction);
    }

    this.addGrid = function(id) {
        this.dg[id] = new dbGrid(id);
        this.dg[id].setEvents(this.executeAction);
    }
    
}
