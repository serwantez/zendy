/**
 * Widget ImageView
 */

imageview = function(id, options) {
    var self = this;
    this.id = id;
    this.widget = $("#"+this.id);
    this.widgetUploader = $("#"+this.id+"-uploader");
    this.widgetImage = $("#"+this.id+"-img");
    this.widgetLink = $("#"+this.id+"-img-a");
    var defaults = {
        uploadDirectory: '',
        nullPath: ''
    }
    
    options =  $.extend(defaults, options);
    
    this.init = function() {
        this.widgetUploader.fileupload({
            dataType: 'json',
            dropZone: this.widgetImage,
            done: function (e, data) {
                $.each(data.result, function (index, file) {
                    self.setData(options.uploadDirectory+file.name);
                });
            }
        });
        
        this.widgetLink.dblclick(function(){
            self.load();
        });
        
        this.widgetLink.keyup(function(e) {
            if (e.keyCode == 46) {
                self.widget.val('');
                self.widgetImage.attr("src", options.nullPath);
            }
        });
               
    }
    
    this.load = function() {
        var attrRO = $("#"+this.id).attr('readonly');
        if ($("#"+this.id).is(":disabled") == false && (typeof attrRO == 'undefined' || attrRO == false)) {
            $("#"+this.id+"-uploader").click();
        }
    }
    
    this.disable = function() {
        $("#"+this.id).attr('disabled','disabled');
        $("#"+this.id).parent().addClass('ui-state-disabled');        
    }

    this.enable = function() {
        $("#"+this.id).removeAttr('disabled');
        $("#"+this.id).parent().removeClass('ui-state-disabled');
    }
    
    this.readonly = function(ro) {
        if (ro) {
            //this.disable();
            $("#"+this.id).attr('readonly','readonly');
        } else {
            //this.enable();
            $("#"+this.id).removeAttr('readonly');
        }        
    }    
    
    this.setData = function(value) {
        if (!value) {
            $('#'+this.id).val('');
            $('#'+this.id+'-img').attr('src',options.nullPath);
        } else {
            $('#'+this.id).val(value);
            $('#'+this.id+'-img').attr('src',value);
        }
    }
    
    this.init();
}
