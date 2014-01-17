/* 
 * Automatyczne wyśrodkowywanie 
 */

$(document).ready(function(){
						   
    $(window).resize(function(){
        $('.ui-align-center').each(function(index){
            $(this).css({
                position: 'absolute',
                left: ($(this).parent().width() 
                    - $(this).outerWidth())/2,
                top: ($(this).parent().height() 
                    - $(this).outerHeight())/2
            });
        });
        $('.ui-align-vcenter').each(function(index){
            $(this).css({
                position: 'absolute',
                top: ($(this).parent().height() 
                    - $(this).outerHeight())/2
            });
        });
        $('.ui-align-hcenter').each(function(index){
            $(this).css({
                position: 'absolute',
                left: ($(this).parent().width() 
                    - $(this).outerWidth())/2
            });
        });        
        
    });
    
    $(window).resize();

});
