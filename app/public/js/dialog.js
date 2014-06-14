define(['jquery', 'bootstrap'], function($){
	
    var view = Backbone.View.extend({
    	el: $('#dialog'),
    	defaults:{
    			title: '在线考试系统', 
    			msg: '',
    			callback:null,
    			style:'',
    			ok:'确定',
    			close:'关闭'
    	},
    	op:null,
    	attrs:{
      		  keyboard: false,
    		  backdrop: 'static'
        },
        
        show: function(options){
        	clearWindow();
        	this.op = $.extend({},this.defaults, options),
        	$(".title", this.el).html(this.op.title);
        	$(".msg", this.el).html(this.op.msg);
        	$(".btn-close", this.el).html(this.op.close);
        	$(".btn-ok", this.el).html(this.op.ok);
        	var view = this;
        	$(".ok", this.el).unbind().on('click', function(){view.callback()});

        	if($("#dialog").is(":hidden")){
        		$(this.el).attr('style', this.op.style);
            	$(this.el).modal(this.attrs);
        	}else{
        		$(this.el).attr('style', this.op.style+';display:block');
        	}
        },
        
        hide: function(){
        	$(this.el).modal('hide');
        },
        
        callback: function(){
        	if($.isFunction(this.op.callback)){
        		this.op.callback();
        	}else{
        		this.hide();
        	}
        }
    });
        
    return new view;
});
