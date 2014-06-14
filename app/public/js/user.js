define(['config','jquery', 'underscore', 'backbone', 'dialog'], function(config, $, _, Backbone, dialog){
	var model = Backbone.Model.extend({
		url:'index.php?m=user',
		examed: function(num, view){
        	this.fetch({url:this.url+"&a=examed&p="+num,success:function(c,resp){
        		view.render_examed();
        	}});
		},
		examed_all: function(num, view){
        	this.fetch({url:this.url+"&a=result&p="+num,success:function(c,resp){
        		view.render_examed_all();
        	}});
		},
		wrong: function(num, view){
        	this.fetch({url:this.url+"&a=wrong&p="+num,success:function(c,resp){
        		view.render_wrong();
        	}});
		},
		wrong_show: function(id, view){
        	this.fetch({url:this.url+"&a=wrong&id="+id,success:function(c,resp){
        		view.render_wrong_show();
        	}});
		},
		info: function(view){
        	this.fetch({url:this.url+"&a=info",success:function(c,resp){
        		view.render_info();
        	}});
		},
		
		page: function(num, view){
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
        template: _.template($("#user_list").html()),
        model:null,
        events:{
        	'click .wrong .show':'wrong_show',	//查看错题
        	'click .examed .show': 'examed_show', //查阅已考试卷
        	'click .user_list .edit':'edit',
        	'click .user_list .add_student':'add_student',
        	'click .user_list .add_teacher':'add_teacher',
        	'click .user_list .del':'del',

        },
        list:function(page){
        	if(page == null){
        		page = 1;
        	}
        	loadWindow();
        	this.model.page(page,this);
        },
        
        add_student:function(){
        	this.add(1);
        },
        add_teacher:function(){
        	this.add(2);
        },
        
        add:function(role){
        	var tem = _.template($("#user_edit").html());
        	var view = this;
        	dialog.show({msg:tem, callback:function(){
            	var account = $(".user_edit #account").val();
            	var name = $(".user_edit #name").val();
            	var email = $(".user_edit #email").val();
            	var password = $(".user_edit #password").val();
            	var data= {s_account:account, s_nickname:name, s_email:email, s_password:password, n_role: role};
            	view.save(data);
        	}});

        },
        
        edit:function(e){
        	$this = $(e.currentTarget);
        	var tem = _.template($("#user_edit").html());
        	var id = $this.data('id');
        	var view = this;
        	dialog.show({msg:tem, callback:function(){
            	var account = $(".user_edit #account").val();
            	var name = $(".user_edit #name").val();
            	var email = $(".user_edit #email").val();
            	var password = $(".user_edit #password").val();
            	var data= {id:id, s_account:account, s_nickname:name, s_email:email, s_password:password};
            	view.save(data);
        	}});
        	$(".user_edit #account").val($this.data('account'));
        	$(".user_edit #name").val($this.data('name'));
        	$(".user_edit #email").val($this.data('email'));
        },
        
        save:function(data){
        	this.model.clear();
        	this.model.set(data);
        	loadWindow("正在保存，请稍后...");

        	this.model.save(null, {success:function(model,resp){  
        		if(resp.error){
            		dialog.show({
            			msg: resp.msg
            		});
        		}else{
        			Backbone.history.loadUrl();
        			dialog.hide();
        		}
            }},
            {error:function(err){
                aler("err");  
            }});
        },

        del:function(e){
        	var $this = $(e.currentTarget);
        	dialog.show({msg:'确定删除该学生？', callback:function(){
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
        
        render:function(){
        	data = this.model.toJSON();
        	list = data.data;
        	page = data.page;
        	$(this.el).html(this.template({list:list, page:page}));
        	clearWindow();
        },

        
        info:function(){
        	this.model.clear();
        	this.model.info(this);
        },
                
        initialize:function(){
        	this.model = new model;
        },
        
        examed:function(page){
        	if(page == null){
        		page = 1;
        	}
        	this.model.examed(page,this);
        },

        examed_all:function(page){
        	if(page == null){
        		page = 1;
        	}
        	loadWindow();
        	this.model.examed_all(page,this);
        },

        wrong:function(page){
        	if(page == null){
        		page = 1;
        	}
        	loadWindow();
        	this.model.wrong(page,this);
        },
        
        wrong_show:function(e){
        	this.model.clear();
        	var $this = $(e.currentTarget);
        	var id = $this.data('id');
        	this.model.wrong_show(id,this);
        },
        
        examed_show:function(e){
        	var $this = $(e.currentTarget);
        	var id = $this.data('id');
        	window.open("index.php?m=exam&a=examed&id="+id, "_blank"); 
        },

        render_examed:function(){
        	data = this.model.toJSON();
        	list = data.data;
        	page = data.page;
        	$(this.el).html(_.template($("#user_examed").html(), {list:list, page:page}));
        	clearWindow();
        },
        
        render_examed_all:function(){
        	data = this.model.toJSON();
        	list = data.data;
        	page = data.page;
        	$(this.el).html(_.template($("#user_examed_all").html(), {list:list, page:page}));
        	clearWindow();
        },
        
        render_wrong:function(){
        	data = this.model.toJSON();
        	list = data.data;
        	page = data.page;
        	$(this.el).html(_.template($("#user_wrong").html(), {list:list, page:page}));
        	clearWindow();
        },
        render_wrong_show: function(){
        	var data =  this.model.toJSON();
        	var template = _.template($("#user_wrong_show").html(), data);
        	dialog.show({msg:template});
        },
        render_info: function(){
        	var data =  this.model.toJSON();
        	$(this.el).html(_.template($("#user_info").html(),data));
        }

    });
        
    return new view;
});
