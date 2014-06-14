define(['config','jquery', 'underscore', 'backbone', 'dialog'], function(config, $, _, Backbone, dialog){

	var model = Backbone.Model.extend({
		url:'index.php?m=exam',
		page: function(num, view){
        	this.fetch({url:this.url+"&p="+num,success:function(c,resp){
        		view.render();
        	}});
		},
		verify: function(num, view){
        	this.fetch({url:this.url+"&a=verify&p="+num,success:function(c,resp){
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
        template: _.template($("#exam_list").html()),
        model:null,
        selected_ids:[],//编辑试卷已选择试题
        events:{
        	'click .exam #save_edit':'save_confirm',
        	'click .exam .examing':'examing', //预览试卷
        	'click .exam .del':'del', //删除试卷
        	'click .exam .add_big':'add_big', //编辑试卷-添加大题
        	'click .exam .add_question':'add_question', //编辑试卷-添加试题
         	'click .exam .tools .del':'del_question', //编辑试卷-删除试题
         	'click .exam .tools .edit':'edit_big', //编辑试卷-编辑大题
         	'click .verifying_list .verifying':'verifying',//阅卷
        },
        
        //添加大题到试卷
        add_big:function(){
        	var html = $("#add_big_dialog").html();
        	var view = this;
        	dialog.show({title:'添加大题', msg:html, callback:function(){
        		view.add_big_callback();
        	}});
        },
        
        //编辑大题
        edit_big:function(e){
        	var $this = $(e.currentTarget);
        	var $li = $this.parents('li');
        	var text = $li.data('title');
        	$('.title', $li).html("<input type='text' value='"+text+"' id='edit_big_input'> ");
        	$("#edit_big_input").focus();
        	$('#edit_big_input', $li).blur(function(){
            	$('.title', $li).html($(this).val());
            	$li.data('title', $(this).val());
        	});
        },
        
        //添加大题回调
        add_big_callback:function(){
        	var title = $("#big_question_title").val();
        	var after = $("#big_question_title_after").val()-0;
        	this.render_add_big(title, after);
        	dialog.hide();
        },
        
        //大题渲染
        render_add_big:function(title,after){
        	var html = _.template($("#add_big_question_item").html(),{title:title});
        	if(after == 0){
        		$(".exam_questions").append(html);
        	}else{
        		$(".exam_questions li").eq(after-1).after(html);
        	}
        },
        
        //添加试题到试卷
        add_question: function(page){
        	if(typeof page == 'undefined' || typeof page == 'object'){
        		page = 1;
        	}
        	loadWindow();
        	var template = _.template($("#add_question_dialog").html());
        	var view = this;
        	$.ajax({url:"index.php?m=questions&p="+page, success:function(data){
        		dialog.show({title:'在线考试系统-添加试题',msg:template({list:data.data,page:data.page, selected_ids:view.selected_ids}), style:'width:900px;margin-left: -450px;', callback:function(){
        			$("#dialog input[type='checkbox'][name='id']:checked").each(function(){
        				$this = $(this);
        				view.render_add_question($this.data('id'), $this.data('sort'), $this.data('score'), $this.data('text'));
        			});
        			view.render_exam_info();
        			dialog.hide();
        		}});
        		
        		$(".add_question_dialog .page").on('click', function(){
        			var page = $(this).data('page');
        			view.add_question(page);
        		});
        		
        	},dataType:"json"});
        },
        
        //渲染试卷信息
        render_exam_info:function(){
        	/**
        	 * 1:单 2：多 3：填空 4：判断 5：简答
        	 */
        	var num1=num2=num3=num4=num5=0;
        	var score1=score2=score3=score4=score5=0;
        	var view = this;
        	view.selected_ids=[];//重置已选择id
        	$(".exam .exam_questions .question").each(function(){
        		$this = $(this);
        		var id = $this.data('id');
        		var sort = $this.data('sort');
        		var score = parseFloat($this.data('score'));
        		
        		view.selected_ids.push(id);
        		
        		switch(sort){
        		case 1:{
        			num1++;
        			score1+=score;
        			break;
        		}
        		case 2:{
        			num2++;
        			score2+=score;
        			break;
        		}
        		case 3:{
        			num3++;
        			score3+=score;
        			break;
        		}
        		case 4:{
        			num4++;
        			score4+=score;
        			break;
        		}
        		case 5:{
        			num5++;
        			score5+=score;
        			break;
        		}
        		}
        	});
        	var total_num = num1+num2+num3+num4+num5;
        	var total_score = score1+score2+score3+score4+score5;
        	$(".exam .single .num").html(num1);
        	$(".exam .multi .num").html(num2);
        	$(".exam .blank .num").html(num3);
        	$(".exam .judge .num").html(num4);
        	$(".exam .brief .num").html(num5);
        	$(".exam .total .num").html(total_num);

        	$(".exam .single .score").html(score1);
        	$(".exam .multi .score").html(score2);
        	$(".exam .blank .score").html(score3);
        	$(".exam .judge .score").html(score4);
        	$(".exam .brief .score").html(score5);
        	$(".exam .total .score").html(total_score);
        	$(".exam #n_score").val(total_score);	
        },
        
        //编辑试卷-删除试题
        del_question:function(e){
        	var $this = $(e.currentTarget);
        	var view = this;
        	dialog.show({msg:'确定将该试题从该卷移除？',callback:function(){
        		$this.parents('li').remove();
        		view.render_exam_info();
				dialog.hide();
        	}});
        },

        //编辑试卷-添加试题渲染
        render_add_question:function(id,sort, score, title){
			var t = _.template($("#add_question_item").html(), {title:title, id:id, sort:sort, score:score});
			$(".exam .exam_questions").append(t);
        },
        
        initialize:function(){
        	this.model = new model;
        },
        
        //所有试题
        list:function(page){
        	this.template =  _.template($("#exam_list").html());
        	if(page == null){
        		page = 1;
        	}
        	this.model.page(page,this);
        },
        //用户中心-参加考试，所有试题列表
        user_exam_list:function(page){
        	this.template = _.template($("#user_examing").html());
        	if(page == null){
        		page = 1;
        	}
        	this.model.page(page,this);
        },
        //所有待批阅试卷列表
        verifying_list:function(page){
        	this.template = _.template($("#exam_verifying").html());
        	if(page == null){
        		page = 1;
        	}
        	this.model.verify(page,this);
        },
        
        //阅卷
        verifying:function(e){
        	var $this = $(e.currentTarget);
        	var id = $this.data('id');
        	window.open("index.php?m=exam&a=verifying&id="+id, "_blank"); 
        },
                
        add:function(){
        	this.model.clear();
        	var data = $.extend([], {s_name:'',n_score:0, n_spend:120}, {addr:'添加试卷'});
        	$(this.el).html(_.template($("#exam_edit").html(),data));
        	require(['jquery.dragsort-0.4.min','jquery-validate.min'], function(){
        		$(".exam_questions").dragsort({ dragSelector: "li .drag", dragEnd: function() { }, dragBetween: false, placeHolderTemplate: "<li></li>" });
        	});
        },
        
        edit:function(id){
        	this.model.clear();
        	this.model.edit(id, this);
        },
        //保存确认
        save_confirm:function(){
        	var s_name = $("#s_name", this.el).val();
        	var n_score = $("#n_score", this.el).val();
        	var n_spend = $("#n_spend", this.el).val();
        	if(s_name.length<5){
        		dialog.show({msg:"请输入试卷名称！"});
        		return;
        	}
        	if(n_score==0){
        		dialog.show({msg:"请添加试题！"});
        		return;
        	}
        	var msg = _.template($("#exam_save_confirm").html(), {name:s_name, score:n_score, spend: n_spend});
        	var view = this;
        	dialog.show({msg:msg, title:"确认保存？", callback:function(){
        		view.save();
        	}});
        },
        
        //保存试卷
        save:function(){
        	this.model.unset('error');
        	this.model.unset('msg');
        	var s_name = $("#s_name", this.el).val();
        	var n_score = $("#n_score", this.el).val();
        	var n_spend = $("#n_spend", this.el).val()*60;

        	var data = [];
        	var $li = $(".exam .exam_questions li");
        	var len = $li.length;
        	for(var n=0;n<len;n++){
        	    var $this = $li.eq(n);
    	         if($this.hasClass('big')){
    	        	 //修正第一项数据为null
    	        	 if(n!=0){
    	        		 data.push(tmp);
    	        	 }
    	             tmp = {};
    	             tmp['title'] = $this.data('title');
    	             tmp['data'] = [];
    	         }else{
    	        	    if(n==0){
    	        	        var tmp = {};
    	        	        tmp['title']=$this.data('title');
    	        	        tmp['data'] = [];
    	        	    }
    	        	    tmp['data'].push({id:$this.data('id')});
    	         }

		        if(n==len-1){
		           data.push(tmp);
		        }
        	}        	
        		
        	var data = {s_name: s_name, n_score: n_score, n_spend: n_spend, s_content: JSON.stringify(data)};
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
            					location.hash='#exam';
            					dialog.hide();
            			}
            		});
        		}
            }},
            {error:function(err){
            }});
        },

        del:function(e){
        	var $this = $(e.currentTarget);
        	dialog.show({msg:'删除试卷将导致所有已参考考生不可查阅试卷！！！', title:"确定删除试卷？", callback:function(){
        		var id= $this.data('id');
        		var m = new model({id:id});
        		m.url = m.url+"&id="+id
        		m.destroy({success:function(m, r){
        			if(r.error){
        				dialog.show({msg:r.msg});
        			}else{
        				dialog.hide();
    					Backbone.history.loadUrl();
        			}
        		}});
        	}});
        },
        
        examing:function(e){
        	var $this = $(e.currentTarget);
        	var id = $this.data('id');
        	view = this;

        	loadWindow();
        	$.ajax({url:"index.php?m=exam&a=is_examed&id="+id, success:function(data){
        		if(data.code == 1){
        			dialog.show({msg:"该试卷已经考过，是否重新考试？",ok:'重新考试', close:'不了', callback:function(){
        				dialog.hide();
        				view.examing_paper(id);
        			}});
        		}else{
        			clearWindow();
        			view.examing_paper(id);	
        		}
        		
        	},dataType:"json"});

        },
        
        //打开考试页面
        examing_paper:function(id){
    		var sheight = screen.height-70;
    		var swidth = screen.width-10;
    		var winoption = "dialogHeight:"+sheight+"px;dialogWidth:"+ swidth +"px;status:no;location:no;scroll:yes;resizable:no;center:yes";
    		window.showModalDialog("index.php?m=exam&a=examing&id="+id, window,winoption);
        },
        
        //渲染试卷列表
        render:function(){
        	data = this.model.toJSON();
        	list = data.data;
        	page = data.page;
        	$(this.el).html(this.template({list:list, page:page}));
            clearWindow();
        },
        
        //渲染编辑试卷
        render_edit:function(){
        	require(['jquery.dragsort-0.4.min'], function(){
        		$(".exam_questions").dragsort({ dragSelector: "li .drag", dragEnd: function() { }, dragBetween: false, placeHolderTemplate: "<li></li>" });
        	});
        	var infos = JSON.parse(this.model.get('infos'));
        	this.model.unset('infos');
        	var data = $.extend({}, this.model.toJSON(), {addr:'编辑试卷'});
        	data['n_spend'] = parseInt(data['n_spend']/60);
        	$(this.el).html(_.template($("#exam_edit").html(),data));
        	var list = JSON.parse(data['s_content']);
        	var view = this;
        		
        	//编辑试题区域填充
        	$.each(list,function(){
        		if(this){
	        		if(this.title){
	        			view.render_add_big(this.title, 0);
	        		}
	        		if(this.data){
		    			$.each(this.data, function(){
		    				var title = "";
		    				if(infos[this.id]){
		    					title = infos[this.id].s_title;
		    				}else{
		    					title = "试题已删除";
		    				}
		    				view.render_add_question(this.id, infos[this.id].n_sort, infos[this.id].f_score, title);
		    			});
	        		}
        		}
        	});
        	
        	this.render_exam_info();//渲染试卷信息
        },
        render_show: function(){
        	var template = _.template($("#question_show").html(), this.model.toJSON());
        	dialog.show({msg:template});
        }
    });
        
    return new view;
});
