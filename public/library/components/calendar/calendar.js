/**
 * Widget Calendar
 */

calendar = function(id, options) {
    var self = this;
    this.id = id;
    this.widget = $('#'+this.id);
    _calendarBody = $('#'+this.id+'-container .ui-calendar-body table tbody');
    _calendarNavi = $('#'+this.id+'-container .ui-calendar-navi .ui-calendar-range span');
    defaults = {
        range: 'month'
    };
    options.currentDate = new Date(options.currentDate);
    options = $.extend(defaults, options);
    this.curDay = options.currentDate;
    
    this.setData = function(data, params) {
        //console.log(data);
        var day;
        var holiday;
        //this.widget.empty();
        if (data['list']=='standard') {
            $('.ui-calendar-feasts').empty();
            $.each(data['rows'], function(key, value) {
                day = $('#'+value[params.dateField]);
                holiday = value[params.holidayField];
                if (holiday == 1) {
                    day.addClass('ui-calendar-holiday');
                }
                if (day) {
                    var feast = $('<div></div>')
                    .addClass('ui-calendar-feast')
                    .html(value[params.listField[0]]);

                    //formatowanie warunkowe
                    if (value['_format']) {
                        feast.addClass(value['_format']);
                    }
                
                    day
                    .find('.ui-calendar-feasts')
                    .append(feast);
                }
            });
        } else if (data['list']=='event') {
            $('.ui-calendar-events').empty();
            $.each(data['rows'], function(key, value) {
                day = $('#'+value[params.eventField['date']]);
                if (day) {
                    var eventTime = $('<span></span>')
                    .addClass('ui-calendar-event-time')
                    .html(value[params.eventField['time']]);
                    
                    var eventType = $('<span></span>')
                    .addClass('ui-calendar-event-type')
                    .html(value[params.eventField['type']]);
                    
                    var eventDescription = $('<span></span>')
                    .addClass('ui-calendar-event-description')
                    .html(value[params.eventField['description']]);
                    
                    var event = $('<div></div>')
                    .addClass('ui-calendar-event')
                    .append(eventTime)
                    .append(eventType)
                    .append('<br />')
                    .append(eventDescription);
                    
                    day
                    .find('.ui-calendar-events')
                    .append($('<a></a>')
                        .attr('key', value[params.eventKeyField[0]])
                        .append(event))
                ;
                }
            });
        }
        this.refresh(params);
    }
    
    this.refresh = function(params) {
        var ce = $(".ui-calendar-event");
        if (ce) {
            $(".ui-calendar-event").bind("click", {
                dataAction: 'searchAction',
                actionType: 'standard'
            }, function(e, ui){
                if (params.eventDialog) {
                    $("#"+params.eventDialog).dialog("open");
                }
                var value = $(this).parent().attr("key");
                value = value.split(';');
                e.data.searchValues = {};
                for(var k in params.eventKeyField) {
                    e.data.searchValues[params.eventKeyField[k]] = value[k];
                }
                params.actionFunction(e);
            });
        }
    }

    this.showValue = function(value) {
    
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
    
    this.setNavigating = function(ds, form) {
        $("#"+self.id+"-button-prev")
        .bind("click", {
            dataAction: 'refreshAction',
            actionType: 'standard'
        }, function(e, ui){
            e.data.currentDate = self.curDay.yyyymmdd();
            e.data.range = options.range;
            df[form].multiaction(ds, e);
        });
        
        $("#"+self.id+"-button-next")
        .bind("click", {
            dataAction: 'refreshAction',
            actionType: 'standard'
        }, function(e, ui){
            e.data.currentDate = self.curDay.yyyymmdd();
            e.data.range = options.range;
            df[form].multiaction(ds, e);
        });
        
        $("input[name="+self.id+"-button-range]")
        .bind("click", {
            dataAction: 'refreshAction',
            actionType: 'standard'
        }, function(e, ui){
            e.data.currentDate = self.curDay.yyyymmdd();
            e.data.range = $(this).val();
            df[form].multiaction(ds, e);
        });
        
    }
    
    _init = function() {
        _buildCalendar();

        $("#"+self.id+"-button-prev")
        .bind("click", function(e, ui) {
            if (options.range == 'month') {
                self.curDay.setDate(1);
                self.curDay.setMonth(self.curDay.getMonth()-1);
            }
            else if (options.range == 'week') {
                self.curDay.setDate(self.curDay.getDate()-7);
            }
            _buildCalendar();
        });
        
        $("#"+self.id+"-button-next")
        .bind("click", function(e, ui) {
            if (options.range == 'month') {
                self.curDay.setDate(1);
                self.curDay.setMonth(self.curDay.getMonth()+1);
            }
            else if (options.range == 'week') {
                self.curDay.setDate(self.curDay.getDate()+7);
            }
            _buildCalendar();
        });

        $("input[name="+self.id+"-button-range]")
        .bind("click", function(e, ui) {
            options.range = $(this).val();
            _buildCalendar();
        });

    }
    
    _buildCalendar = function() {
        var today = new Date();
        var tr;
        var td;
        var dayNumber;
        var monthName;
        var feasts;
        var events;
        var iDay = new Date(self.curDay);
        var weekday;
        _calendarBody.empty();
        
        if (options.range == 'month') {
            iDay.setDate(1);
            weekday = iDay.getDay();
            var daysNumber = daysInMonth(self.curDay.getMonth()+1, self.curDay.getFullYear());
            var rows = Math.ceil((daysNumber + weekday)/7);
            iDay.setDate(iDay.getDate() - weekday);
            var h = (100/rows)+'%';

            _calendarNavi.html(options.monthNames[self.curDay.getMonth()+1]+' '+self.curDay.getFullYear());
            for (var i=0; i<rows; i++) {
                tr = $('<tr></tr>').addClass("ui-calendar-row").css('height', h);
                for (var j=0; j<7; j++) {
                    dayNumber = $('<span></span>')
                    .addClass("ui-calendar-day")
                    .html(iDay.getDate());
                    monthName = $('<span></span>')
                    .addClass("ui-calendar-month")
                    .html(options.monthNames[iDay.getMonth()+1]);
                    feasts = $('<span></span>').addClass("ui-calendar-feasts");
                    events = $('<span></span>').addClass("ui-calendar-events");
                    td = $('<td></td>')
                    .attr('id', iDay.yyyymmdd())
                    .addClass("ui-widget-content")
                    .append($('<div></div>')
                        .addClass("ui-widget-content ui-calendar-day-header")
                        .append(dayNumber)
                        .append(monthName))                    
                    .append(feasts)
                    .append(events)
                    ;
                    //dzień dzisiejszy
                    if (iDay.getDate() == today.getDate() && iDay.getMonth() == today.getMonth() && iDay.getFullYear() == today.getFullYear()) {
                        td.addClass("ui-calendar-today");
                    }
                    //dni spoza bieżącego miesiąca
                    if (iDay.getMonth() != self.curDay.getMonth() || iDay.getFullYear() != self.curDay.getFullYear()) {
                        td.addClass("ui-state-disabled");
                    }
                    tr.append(td);
                    iDay.setDate(iDay.getDate()+1);
                }
                _calendarBody.append(tr);
            }
        }
        else if (options.range == 'week') {
            weekday = iDay.getDay();
            iDay.setDate(iDay.getDate()-weekday);
            var iDay2 = new Date();
            iDay2.setDate(iDay.getDate()+6);
            tr = $('<tr></tr>').addClass("ui-calendar-row").css('height', '100%');
            
            _calendarNavi.html(iDay.yyyymmdd()+" - "+iDay2.yyyymmdd());
            for (var j=0; j<7; j++) {
                dayNumber = $('<span></span>')
                .addClass("ui-calendar-day")
                .html(iDay.getDate());
                monthName = $('<span></span>')
                .addClass("ui-calendar-month")
                .html(options.monthNames[iDay.getMonth()+1]);
                feasts = $('<span></span>').addClass("ui-calendar-feasts");
                events = $('<span></span>').addClass("ui-calendar-events");
                td = $('<td></td>')
                .attr('id', iDay.yyyymmdd())
                .addClass("ui-widget-content")
                .append($('<div></div>')
                    .addClass("ui-widget-content ui-calendar-day-header")
                    .append(dayNumber)
                    .append(monthName))                    
                .append(feasts)
                .append(events);
                //dzień dzisiejszy
                if (iDay.getDate() == today.getDate() && iDay.getMonth() == today.getMonth() && iDay.getFullYear() == today.getFullYear()) {
                    td.addClass("ui-calendar-today");
                }
                tr.append(td);
                iDay.setDate(iDay.getDate()+1);
            }
            _calendarBody.append(tr);
        }
    }
    
    _init();
    
}

daysInMonth = function(month, year) {
    return new Date(year, month, 0).getDate();
}

Date.prototype.yyyymmdd = function() {         
                                
    var yyyy = this.getFullYear().toString();                                    
    var mm = (this.getMonth()+1).toString(); // getMonth() is zero-based         
    var dd  = this.getDate().toString();             
                            
    return yyyy + '-' + (mm[1]?mm:"0"+mm[0]) + '-' + (dd[1]?dd:"0"+dd[0]);
};
