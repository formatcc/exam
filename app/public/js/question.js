define(['config','jquery', 'underscore', 'backbone', 'dialog'], function(config, $, _, Backbone, dialog){

	var model = Backbone.Model.extend({
		url:'index.php?m=questions',
		page: function(num, data, view){
        	this.fetch({url:this.url+"&p="+num,success:function(c,resp){
        		view.render();
        	}});
		},
		edit:function(id, view){
        	this.fetch({url:this.url+"&id="+id,success:function(c,resp){
        		view.render_edit();
        	}});
		},
		show: function(id, view){
        	this.fetch({url:this.url+"&id="+id,success:function(c,resp){
        		view.render_show();
        	}});
		}
	});
	
    var view = Backbone.View.extend({
        el:$("#main"),
        template: _.template($("#question_list").html()),
        model:null,
        events:{
        	'click .question #save_edit':'save',
        	'click  .question .del':'del',
        	'click  .question .show_question':'show_question',
        	'change .question #question_sort':'render_answer', //改变试题类型
            'change .question #options_num':'render_answer',//改变候选项数目
            'click  .question .search':'search',//搜索
        },
                
        initialize:function(){
        	this.model = new model;
        },
        //搜索
        search:function(){
            window.location.hash='#question';
        },

        //列表
        list:function(page){

        	if(page == null){
        		page = 1;
        	}
            var subject = $(".subject", this.el).val();
            var sort = $(".question_sort", this.el).val();
            var data = {subject:subject, sort:sort};
        	this.model.page(page, data, this);
        },
        //添加试题        
        add:function(){
        	this.model.clear();
        	$(this.el).html(_.template($("#add_question").html(),{addr:'添加试题'}));
        	CKEDITOR.replace('title'); //题干
        	CKEDITOR.replace('options');//选项
        	CKEDITOR.replace('analyse');//解析
        	CKEDITOR.replace('short_answer');//简答题答案
        	this.render_answer();
        },
        
        edit:function(id){
        	loadWindow();
        	this.model.clear();
        	this.model.edit(id, this);
        },
        save:function(){
        	this.model.unset('error');
        	this.model.unset('msg');
        	var subject = $("#subject", this.el).val();
        	var sort = $("#question_sort", this.el).val();
        	var score = $("#score", this.el).val();
        	var title = CKEDITOR.instances.title.getData();
        	var options = CKEDITOR.instances.options.getData();
        	var analyse = CKEDITOR.instances.analyse.getData();
        	var options_num = $("#options_num").val();
        	
        	if(sort == 1 || sort == 4){
        		//单选 判断
        		var answer = $(".answerbox:not(:hidden)").find('input:checked').val();
        	}else if(sort == 2){
        		//多选
        		var checkbox = $(".answerbox:not(:hidden)").find('input:checked');
        		var answer = ''; checkbox.each(function(){answer += $(this).val()});
        	}else if(sort == 3){
        		//填空
            	var blank = $(".answerbox:not(:hidden)").find('input');
            	var answer = {}; var option=65; blank.each(function(){answer[String.fromCharCode(option++)] =$(this).val();});
            	answer = JSON.stringify(answer);
        	}else if(sort == 5){
        		//简答
        		var answer = CKEDITOR.instances.short_answer.getData(); 
        	}else{
        		var answer = '';
        	}
        	
        	var data = {n_subject_id: subject, n_sort: sort, s_title: title, s_options:options, n_options_num:options_num, s_analyse:analyse, f_score:score, s_answer:answer};
        	this.model.set(data);
        	loadWindow("正在保存，请稍后...");

        	this.model.save(null, {success:function(model,resp){  
        		if(resp.error){
            		dialog.show({
            			msg: resp.msg
            		});
        		}else{
            		dialog.show({
            			msg: resp.msg, 
            			callback:function(){
            					location.hash='#question';
            					dialog.hide();
            			}
            		});
        		}
            }},
            {error:function(err){
                aler("err");  
            }});
        },

        del:function(e){
        	var $this = $(e.currentTarget);
        	dialog.show({msg:'确定删除该试题？', callback:function(){
        		var id= $this.data('id');
        		var m = new model({id:id});
        		m.url = m.url+"&id="+id
        		m.destroy({success:function(m, r){
        			if(r.error){
        				dialog.show({msg:r.msg});
        			}else{
    					Backbone.history.loadUrl();
    					dialog.hide();
        			}
        		}});
        	}});
        },
        
        show_question:function(e){
        	this.model.clear();
        	var $this = $(e.currentTarget);
        	var id = $this.data('id');
        	this.model.show(id,this);
        },

        render:function(){
        	data = this.model.toJSON();
        	list = data.data;
        	page = data.page;
        	$(this.el).html(this.template({list:list, page:page}));
        	clearWindow();
        },
        
        render_edit:function(){
        	$(this.el).html(_.template($("#add_question").html(),{addr:'编辑试题'}));
        	var title = CKEDITOR.replace('title'); //题干
        	var options = CKEDITOR.replace('options');//选项
        	var analyse = CKEDITOR.replace('analyse');//解析
        	CKEDITOR.replace('short_answer');//简答题答案
        	
        	data = this.model.toJSON();
        	$("#subject", this.el).val(data.n_subject_id);
        	$("#question_sort", this.el).val(data.n_sort);
        	$("#score", this.el).val(data.f_score);
        	$("#options_num", this.el).val(data.n_options_num);
        	title.setData(data.s_title);
        	options.setData(data.s_options);
        	analyse.setData(data.s_analyse);
        	this.render_answer(data.s_answer);
        	
        },
        
        //渲染候选项数目，参考答案
        render_answer:function(answer){
        	var type = $("#question_sort", this.el).val();//试题类型
        	var num = $("#options_num", this.el).val();//候选项数目
        	var $cur_box = $("#answerbox_"+type);//当前box
        	$(".answerbox").hide();
        	$cur_box.show();

        	//简单题,判断题不需要候选项数量
        	if(type == 5 || type == 4){
        		$("#selectnumber", this.el).hide();
        	}else{
        		$("#selectnumber", this.el).show();
        		var template = _.template($("#temp_answerbox_"+type).html());
        		var $ori = $("label", $cur_box);
        		var ori_len = $ori.length;

        		if(ori_len > num){
            		for(var n=num;n<ori_len;n++){
            			$ori.eq(n).remove();	
            		}
        		}else{
        			
        			for(var n=ori_len;n<num;n++){
        				var code = String.fromCharCode(65+n);
        				$cur_box.append(template({answer:code}));
        			}	
        		}
        	}
        	
        	//设置值
        	if(answer){
        		if(typeof answer == 'object'){
        			return;
        		}
        		if(type == 1 || type == 2 || type == 4){
        			//单选，多选，判断
        			var len = answer.length;
        			var sel = [];
        			for(var n=0;n<len;n++){
        				sel.push('[value='+answer[n]+']');
        			}
        			
        			var m = 'input'+sel.join(',');
        			$(".answerbox:not(:hidden)").find(m).attr("checked", "checked");        			
        		}else if(type == 3){
        			//填空
        			try{
        				var data = JSON.parse(answer);
        				var m=0;
        				for(n in data){
        					 $(".answerbox:not(:hidden)").find('input').eq(m++).val(data[n]);
        				}
        			}catch(e){
        			}
        		}else if(type == 5){
        			CKEDITOR.instances.short_answer.setData(answer); 
        		}
        	}
        	clearWindow();
        	
        },
        
        render_show: function(){
        	var template = _.template($("#question_show").html(), this.model.toJSON());
        	dialog.show({msg:template});
        }
    });
        
    return new view;
});
