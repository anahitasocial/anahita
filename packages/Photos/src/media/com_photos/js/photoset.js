var PhotoSet = new Class({
	
	Implements :[Options],
	
	options : {
		sets	: 'sets-wrapper', 
		setForm : 'set-form'
	},
	
	initialize : function(options) 
    {
		this.setOptions(options);
		this.slide 		= new Fx.Slide(this.options.sets);
		this.oid 		= document.id(this.options.sets).get('oid');
		this.photo_id	= document.id(this.options.sets).get('photo_id');
		this.baseURL 	= 'index.php?option=com_photos';
    },
	
	show : function(){
		this.browse('selector');
	},
	
	hide : function(){
		this.browse('module');
	},
	
	browse : function(layout){
		
		this.slide.slideOut();
		
		var req = new Request.HTML({
			method  : 'get',
			url		: this.baseURL + '&view=sets&layout=' + layout + '&oid=' + this.oid + '&photo_id=' + this.photo_id,
			update	: this.options.sets,
			onComplete: function(el){
				this.slide.slideIn();
			}.bind(this)
		}).send();
	},
	
	add : function(){
		
		this.form = document.id(this.options.setForm);
		
		if(!this.form.get('validator').validate())
			return;
		
		this.form.ajaxRequest({
			method : 'post',
			url : this.form.get('action') + '?layout=selector_list&reset=1',
			data : this.form,
			inject : {
				element : document.getElement('#' + this.options.sets + ' .an-entities'),
				where   : 'top'
			},
			onSuccess : function(form){
				var element = document.getElement('#' + this.options.sets + ' .an-entities').getElement('.an-entity');
				this.form.reset();
			}.bind(this)
		}).send();
	},
	
	addPhoto : function(el){
		
		var entity = el;
		
		entity.ajaxRequest({
			method	: 'post',
			url		: this.baseURL + '&view=set&id=' + el.get('set_id').toInt() + '&photo_id=' + this.photo_id.toInt(),
			data	: 'action=addphoto',
			onComplete : function()
			{
				entity.addClass('an-highlight');
				entity.set('data-trigger', 'RemovePhoto');
			}
		}).send();
	},
	
	removePhoto : function(el){
		
		var entity = el;
		
		el.ajaxRequest({
			method	: 'post',
			url		: this.baseURL + '&view=set&id=' + el.get('set_id').toInt() + '&photo_id=' + this.photo_id.toInt(),
			data	: 'action=removephoto',
			onSuccess : function()
			{
				if(this.status == 204)
					entity.destroy();
				else
				{
					entity.removeClass('an-highlight');
					entity.set('data-trigger', 'AddPhoto');
				}	
			}
		}).send();
	}
});

var photoSet = new PhotoSet();

Delegator.register('click', {
	
	'SetSelector' : function(event, el, api) {
		event.stop();
		photoSet.show();
	},
	
	'CloseSelector' : function(event, el, api) {
		event.stop();
		photoSet.hide();
	},
	
	'RemovePhoto' : function(event, el, api){
		event.stop();
		photoSet.removePhoto(el);
	},
	
	'AddPhoto' : function(event, el, api){
		event.stop();
		photoSet.addPhoto(el);
	},
	
	'Add' : function(event, el, api){
		event.stop();
		photoSet.add();
	}
});