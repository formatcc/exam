(function($){
    var id = 'topmsg';
    var $topmsg = $("#"+id);
    var frame = "<div id='"+id+"' class='navbar navbar-inverse navbar-fixed-top'></div>";
    $.topmsg = function(content){
        alert(content);
        init();
        setContent(content);
        return $topmsg;
    };
    
    function setContent(content){
        $topmsg.html(content);
        return $topmsg;
    }
    
    function init(){
        if($("#"+id).length==0){
            $("body").append(frame);
            $topmsg = $("#"+id);
        }
    }
    
})(jQuery);