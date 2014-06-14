require.config({
    paths:{
        jquery		: 'jquery.min',
        backbone	: 'backbone-min',
        underscore	: 'underscore-min',
        bootstrap	: 'bootstrap.min'
    }, 
	shim: {  
		bootstrap: {  
	        deps: ["jquery"]
	    }  
	}  
});

require(['config','jquery', 'underscore', 'backbone', 'bootstrap'],function(config, $, _, Backbone){
    
    var view = Backbone.View.extend({
        el:$("#leftnavContent"),
        
        events:{
        	'click li':'change_nav'
        },
        
        initialize:function(){
        },
        
        change_nav:function(e){
        	var $this = $(e.currentTarget);
        	$(this.el).children('li').removeClass('active');
        	$this.addClass('active');
        }
        
    });
    
    var app = Backbone.Router.extend({
        routes:{
        	''					: 'info',			//用户中心
        	'infos'				: 'info',			//用户中心

        	'examing'			: 'user_examing',	//参加考试
            'examing/:page'		: 'user_examing',	//参加考试
            'examed'			: 'user_examed',	//参加过的考试
            'examed/p:page'		: 'user_examed',	//参加过的考试
            'wrong'				: 'user_wrong',		//错题分析
            'wrong/p:page'		: 'user_wrong',		//错题分析
            'info'				: 'user_info',		//

            'question'			: 'question_list',	//所有试题
            'question/p:page'	: 'question_list',	//所有试题
            'question/add'		: 'question_add',	//添加试题
            'question/edit:id'	: 'question_edit',	//编辑试题

            'exam'				: 'exam_list',		//试卷列表	
            'exam/p:page'		: 'exam_list',		//试卷列表	
            'exam/edit:id'		: 'exam_edit',		//编辑试卷
            'exam/add'			: 'exam_add',		//添加试卷
            'verifying'			: 'exam_verifying',	//批阅试卷
            'verifying/p:page'	: 'exam_verifying',	//批阅试卷

            'users'				: 'users',	//用户管理
            'users/p:page'		: 'users',	//用户管理

            'results'			: 'results',	//成绩查询
            'results/p:page'	: 'results',	//成绩查询

        },
        
        initialize:function(){
          new view;
        },
        
        //成绩查询
        results:function(page){
        	require(['user'], function(user){
        		user.examed_all(page);
        	});
        	
        },
        
        //用户管理
        users:function(page){
        	require(['user'], function(user){
        		user.list(page);
        	});
        },        
        exam_verifying:function(page){
        	require(['exam'], function(exam){
        		exam.verifying_list(page);
        	});
        },
        
        user_examing:function(page){
            loadWindow();
        	require(['exam'], function(exam){
        		exam.user_exam_list(page);
        	});
        },
        
        info:function(){
        	require(['user'], function(user){
        		user.info();
        	});
        },
        user_examed:function(page){
            loadWindow();
        	require(['user'], function(user){
        		user.examed(page);
        	});
        },
        user_wrong:function(page){
        	require(['user'], function(user){
        		user.wrong(page);
        	});
        },
        question_list:function(page){
            loadWindow();
        	require(['question'], function(question){
        		question.list(page);
        	});
        },
        question_add:function(){
        	require(['question'], function(question){
        		question.add();
        	});
        },
        question_edit:function(id){
        	require(['question'], function(question){
        		question.edit(id);
        	});
        },
        
        exam_list:function(page){
            loadWindow();
        	require(['exam'], function(exam){
        		exam.list(page);
        	});
        },
        exam_edit:function(id){
        	require(['exam'], function(exam){
        		exam.edit(id);
        	});
        },
        exam_add:function(id){
        	require(['exam'], function(exam){
        		exam.add();
        	});
        }
        

    });
    
    new app;
    Backbone.history.start();
});

/**
 * 加载层
 * @param msg
 */
function loadWindow(msg){
	if($("#loading").length==0){
		if(typeof msg == 'undefined'){
			msg = "加载中，请稍后...";
		}
		var html = '<div class="modal-backdrop fade in" id="loading" style="z-index:2000px;"><div class="loading">'+msg+'</div></div>';
		$("body").append(html);
	}
}
/**
 * 取消加载层
 */
function clearWindow(){
	$("#loading").remove()	
}

function trim_img(html){
	if(html){
		return html.replace(/<img.*?>/, "[图片]");
	}else{
		return "";
	}
}

//去掉所有的html标记
function trim_tag(str){
	return str.replace(/<[^>]+>/g,"");
}