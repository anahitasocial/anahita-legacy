/**
 * @depends min/mootools.js
 * @depends min/bootstrap.js
 * @depends anahita.js
*/

/**
 * Handling displaying ajax message notifications
 */
Class.refactor(Request.HTML, 
{	
	//check the header
	onSuccess: function() {
		var message 	= this.xhr.getResponseHeader('Redirect-Message');
		var messageType = this.xhr.getResponseHeader('Redirect-Message-Type') || 'success';
		if  ( message ) {
			message.alert(messageType);
		}
		return this.previous.apply(this, arguments);
	},
	onFailure: function() {
		var message 	= this.xhr.getResponseHeader('Redirect-Message');
		var messageType = this.xhr.getResponseHeader('Redirect-Message-Type') || 'error';
		if  ( message ) {
			message.alert(messageType);
		}
		return this.previous.apply(this, arguments);
	}
});

/**
 * String Alert using Purr
 */
String.implement({
	prompt : function(options) {
		var options = {					
				body    : '<h3>' + this.translate() + '</h3>',
				buttons : [
				   {name: 'Action.cancel'.translate(), dismiss:true},
				   {name: 'Action.yes'.translate(), dismiss:true, click:options.onConfirm, type: 'btn-danger'}
				]
		};
		return new Bootstrap.Popup.from(options).show();	
	},
	alert  : function(type) {
		var div = new Element('div',{html:this});
		div.set('data-alert-type', type);
		window.behavior.applyFilter(div, Behavior.getFilter('Alert'));
	}
});

(function(){
	Class.refactor(Bootstrap.Popup, {	
		_animationEnd: function(){
			if (Browser.Features.getCSSTransition()) this.element.removeEventListener(Browser.Features.getCSSTransition(), this.bound.animationEnd);
			this.animating = false;
			if (this.visible){
				this.fireEvent('show', this.element);
			} else {
				this.fireEvent('hide', this.element);
				if (!this.options.persist){
					this.destroy();
				} else {
					this.element.addClass('hide');
					this._mask.dispose();
				}
			}
		},
	});	
	Bootstrap.Popup.from = function(data) 
	{
		Object.set(data, {buttons:[], header:''});
		var html = '';
		if ( data.header )
			html += '<div class="modal-header">' + 
//						'<a href="#" class="close dismiss stopEvent">x</a>' + 
						'<h3>'+data.header+'</h3>' +
					'</div>';
					
		html +=	'<div class="modal-body"><p>' + data.body  + '</p>' + 
					'</div>' +
					'<div class="modal-footer">' +
					'</div>';			
		element = new Element('div', {'html':html,'class':'modal fade'});
		
		data.buttons = data.buttons.map(function(button) {
			Object.set(button, {
				click 	: Function.from(),
				type	: ''
			});
			var btn  = new Element('button', {
				html	: button.name, 
				'class' : 'btn'
			});
			
			btn.addClass(button.type);
			
			btn.addEvent('click', button.click.bind(this));
			
			if ( button.dismiss ) {
				btn.addClass('dismiss stopEvent');
			} 
			
			return btn;
		});
		 
		element.getElement('.modal-footer').adopt(data.buttons);
		element.inject(document.body, 'bottom');
		
		return new Bootstrap.Popup(element, data.options || {});	
	}
})();

Behavior.addGlobalFilter('Alert', {
	defaults : {
		mode 		: 'bottom',
		position	: 'right',
		highlight   : false,
		hide 		: true,
		alert		: {
			
		}
	},
	returns	: Purr,
	setup 	: function(el, api) 
	{
		el.dispose();
		var options = api._getOptions();
		if ( api.getAs(Boolean, 'hide') === false) {			
			options.alert['hideAfter'] = false;
		}
		if ( !this._purr ) {
			this._purr = new Purr(options);
		}
		var wrapper = new Element('div',{'class':'alert alert-'+api.get('type')}).set('html', el.get('html'));		
		this._purr.alert(wrapper, api._getOptions() || {});
		return this._purr;
	}
});

Class.refactor(Bootstrap.Popover, {
        
   initialize : function(el, options)
   {             
       return this.previous(el, options);       
   },
   _makeTip: function() 
   {
	  if ( !this.tip ) 
	  {
		 this.previous();
		 if ( this.options.tipclass )
			 this.tip.addClass(this.options.tipclass);
   	  }
   	  return this.tip;
   }, 
   _attach: function(method) 
   {
       this.parent(method);
       this.bound.event = this._handleEvent.bind(this);
       method = method || 'addEvents';
       if (this.options.trigger == 'click') 
       {		
       		[document,this.element].invoke(method,{
       			 click: this.bound.event
       		});
       }
       else if (this.options.trigger == 'hover')
       {
           this.options.delayOut = Math.max(50, this.options.delayOut);
           
           if ( this.tip )
           {
               this.tip[method]({
                   mouseover  : this.bound.enter,
                   mouseleave : this.bound.leave
               });               
           }
       }
   },
   _complete: function() 
   {
       if ( this.visible )
       {
           if ( this.options.trigger == 'hover' )
               this.tip['addEvents']({
                   mouseover  : this.bound.enter,
                   mouseleave : this.bound.leave
               }); 
       }
       return this.parent();       
   },
   _handleEvent : function(event)
   {
		var el = event.target;
		var contains = el == this.element || this.element.contains(el) || (this.tip && this.tip.contains(el));
		if ( !contains ) {
           this.bound.leave();
           clearTimeout(this.repositioner);
           this.repositioner = null;
		}
        else {
           this.bound.enter();
           if ( !this.repositioner ) {
           		this.repositioner = (function(){
           			this._reposition();
           		}).periodical(10, this);
           }
		}
   },
   _reposition : function()
   {
   		if ( !this.tip || !this.visible )
   			return;
		var pos, edge, offset = {x: 0, y: 0};
		switch(this.options.location){
			case 'below': case 'bottom':
				pos = 'centerBottom';
				edge = 'centerTop';
				offset.y = this.options.offset;
				break;
			case 'left':
				pos = 'centerLeft';
				edge = 'centerRight';
				offset.x = this.options.offset;
				break;
			case 'right':
				pos = 'centerRight';
				edge = 'centerLeft';
				offset.x = this.options.offset;
				break;
			default: //top
				pos = 'centerTop';
				edge = 'centerBottom';
				offset.y = this.options.offset;
		}
		if (typeOf(this.options.offset) == "object") offset = this.options.offset;
		this.tip.position({			
			relativeTo: this.element,
			position: pos,
			edge: edge,
			offset: offset
		});
   }
   
});

Behavior.addGlobalPlugin('BS.Popover','Popover', {
    setup : function(el, api, instance)
    {
    	instance.options.tipclass = api.getAs(String,'tipclass');    	
    /*
        var getContent   = instance.options.getContent;
        instance.options = Object.merge(instance.options,{
           getContent : function() {
               var content = getContent();
               //check if it's a selector
               if ( element = el.getElement(content) ) {
                   element.dispose();
                   return element.get('html');
               }
               return content;
           }
        });
        */
        if ( instance.options.trigger == 'click')
            instance._leave();
    }

});

Behavior.addGlobalFilter('RemotePopover', {
    defaults : {
        title   : '.popover-title',
        content : '.popover-content',       
        delay   : 0
    },
    setup : function(el, api) 
    {
        el.addEvent('click', function(e){e.stop()});
        var getData = function(popover) 
        {
            var req = new Request.HTML({
                method : 'get',
                async  : true,
                url    : url,
                onSuccess : function() {
					var html    = req.response.text.parseHTML();
            		var title   = html.getElement(api.get('title'));
           			var content = html.getElement(api.get('content'));
            		if ( content )
                		content = content.get('html');
            		if ( title )
                		title   = title.get('html');
                	if ( popover.tip )
                	{
                		if ( title )
				            popover.tip.getElement('.popover-title').set('html',   title);
			            popover.tip.getElement('.popover-content').set('html', content);
                	}
		        }
			}).send();
        }
        var clone = Object.clone(Bootstrap.Popover.prototype);
        Class.refactor(Bootstrap.Popover, {
            _leave : function()
            {
                (function()
                {
                    if ( !this.visible ) {
                        this.data = null;
                        if ( this.tip )
                            this.tip.dispose();
                        this.tip = null;                        
                    }
                }).delay(100,this);
                this.previous();
            },
            _enter : function()
            {
                if ( !this.data ) {
                	getData(this);
                	data  = {
                		title   : this.element.get(this.options.title)   || 'Prompt.loading'.translate(),
                		content : this.element.get(this.options.content) || '<p class="uiActivityIndicator">&nbsp;</p>'
                	}
                    this.data = data;
                }
                if ( !this.data.content )
                    this._leave();
                else
                {
                    this.options.getContent = Function.from(this.data.content);
                    this.options.getTitle   = Function.from(this.data.title);
                    this.previous();
                }
            }
        });
        
        window.behavior.applyFilter(el, Behavior.getFilter('BS.Popover'));
        var instance = el.getBehaviorResult('BS.Popover'),
            url      = api.getAs(String, 'url');
        
        Bootstrap.Popover.prototype = clone;
    }
});   

/**
 * Editable Behavior
 */
Behavior.addGlobalFilter('Editable',{
	defaults : {
		prompt 		: 'Prompt.inlineEdit'.translate(),
		inputType	: 'textfield'
	},
	setup : function(el, api)
	{
		var prompt 	   = api.getAs(String, 'prompt'),
			inputType  = api.getAs(String, 'inputType')
			url	   	   = api.getAs(String, 'url')
			inputName  = api.getAs(String, 'name')
			;
			
		el.store('prompt', '<span class="an-ui-inline-form-prompt">'+ prompt +'</span>');
		
		if ( !el.get('text').test(/\S/) ) {
			el.set('html', el.retrieve('prompt'));
		}
		
		el.addEvent('click', function(el, inputType, url,inputName) 
		{
			var prompt = el.retrieve('prompt');
			if ( el.retrieve('state:edit') ) {
				return;
			}
			el.store('state:edit', true);
			el.hide();
			var form 	   = new Element('form', {method:'post', 'action':url,'class':'inline-edit'});			
			var cancelBtn  = new Element('button', {text:'Action.cancel'.translate(),'class':'btn'});
			var saveBtn    = new Element('button', {text:'Action.save'.translate(),  'class':'btn btn-primary'});
			var value	   = el.getElement('span') ? '' : el.get('text');
			var inputText  = new Element('input',{type:'text'});
			if ( inputType == 'textarea' ) {
				inputText = new Element('textarea');
			}
			inputText.set({name:inputName, value:value.trim(), 'class':'input-xxlarge', 'cols':'5', 'rows':'5'});
			form.show();
			form.adopt(new Element('div', {'class':'control-group'}).adopt(new Element('div', {'class':'controls'}).adopt(inputText)));
			form.adopt(new Element('div', {'class':'form-actions'}).adopt(cancelBtn).appendText(' ').adopt(saveBtn));
			
			cancelBtn.addEvent('click', function(e){
				e.stop();
				el.store('state:edit', false);
				el.show();
				form.destroy();
			});
			
			saveBtn.addEvent('click', function(e){
				e.stop();
				el.store('state:edit', false);
				form.ajaxRequest({
					onSuccess : function() {
						el.set('html', inputText.get('value') || prompt);
						el.show();
						form.hide();					
					}
				}).send();
			});
			
			el.getParent().adopt(form);
		}.bind(null,[el,inputType, url,inputName]));
	}
});

/**
 * Embeding Video
 */
Behavior.addGlobalFilter('EmbeddedVideo', {
	setup : function(el, api) 
	{
		//el.spin();
		var img	 = el.getElement('img');
		el.store('thumbnail', img);
		img.addEvent('load', function() {
			el.get('spinner').element.destroy();
			var ratio  = img.width / img.height;
			var width  = Math.min(img.getStyle('max-width').toInt(), img.width);
			var height = width / ratio;
			var styles = {width:width,height:height};
			el.unspin();			
			var span = new Element('span');
			span.setStyles(styles);
			el.setStyles(styles);
			span.inject(el, 'top');
		});
		el.addEvent('click:once', function() {			
			var thumb	= el.retrieve('thumbnail');
			var options = api._getOptions();
			var size 	= el.getParent().getSize();
			var width	    = el.getParent().get('embed_width') || options.width;
			var ratio		= width / options.width;
			options.height  = ratio * options.height;
			options.width   = width;
			if ( Browser.Engine.trident )
				options.wMode   = '';
			var object = new Swiff(options['url']+'&autoplay=1', {
					width  : thumb.width,
					height : thumb.height,
					params : options
			});
			thumb.set('tween',{
				duration 	: 'short',
				onComplete	: function() {
					el.empty().adopt(object);
				}
			});
			thumb.fade(0.7);			
		});
	}		
});

/**
 * Delegates
 */
Delegator.register('click', {
	'ViewSource' : function(event, el, api) {
		event.stop();
		var element = api.getAs(String, 'element');		
		element = el.getElement(element);
		yWindow = window.open('','','resizable=no,scrollbars=yes,width=800,height=500');
		var codes = [];
		element.getElements('pre').each(function(line){
			codes.push(line.get('text').escapeHTML());
		});
		yWindow.document.body.innerHTML = '<pre>' + codes.join("\n") + '</pre>';		
	},
	'Remove' : function(event, handle, api) {
		event.stop();		
		var options = {
			'confirmMsg'	  : api.get('confirm-message') || 'Prompt.confirmDelete'.translate(),
			'confirm'		  : true,
			'parent'          : api.get('parent') || '!.an-removable',
			'form'			  : api.get('form')
		};
		var parent  = handle.getElement(options.parent);		
		var submit  = function(options) 
		{
			if ( !options.form )
				var data    = handle.get('href').toURI().getData();
				var url 	= handle.get('href');
			
			if ( parent ) 
			{
				parent.ajaxRequest({url:url, data:data,onSuccess:function(){parent.destroy()}}).post();
			} 
			else 
			{
				var form = (options.form || 
					Element.Form({
						method  : 'post',
						url 	: url,
						data	: data
					}));
				if ( instanceOf(options.form, String) )
				{
					form = handle.getElement(options.form);
				}
				form.submit();
			}
			if ( handle.retrieve('modal') ) {
				handle.retrieve('modal').destroy();
			}
		}.pass(options);
		
		if ( options.confirm )
		{
			options = {
					body    : '<h3>' + options.confirmMsg + '</h3>',
					buttons : [
					   {name: 'Action.cancel'.translate(), dismiss:true},
					   {name: 'Action.delete'.translate(), dismiss:true, click:function(){submit()}, type: 'btn-danger'}					   
					]
			};
			if ( !handle.retrieve('modal') ) {
				handle.store('modal', Bootstrap.Popup.from(options));
			}
			
			handle.retrieve('modal').show();								
		}
		else submit();		
	},
	'Submit' : function(event, el, api) {
		event.stop();
		if ( el.hasClass('disabled') )
		{
		    return false;
		}
		data = el.get('href').toURI().getData();
		var form = Element.Form({action:el.get('href'), data:data});
		if ( el.get('target') ) {
			form.set('target', el.get('target'));
		}
		var submit = function(){
			el.spin();
			form.inject(document.body, 'bottom');
			form.submit();			
		}
		if ( api.get('promptMsg') ) {
			api.get('promptMsg').prompt({onConfirm:submit});
		}		
		else {			
			submit();
		}
	},
	'VoteLink' : function(event, el, api) {
		event.stop();
		el.ajaxRequest({
			method    : 'post',
			onSuccess : function() {
				el.getParent().hide();
				document.id(api.get('toggle')).getParent().show();
				var box = document.id('vote-count-wrapper-' + api.get('object')) ||
				          el.getElement('!.an-actions ~ .story-comments  .vote-count-wrapper ')
				if ( box ) 
				{
					box.set('html', this.response.html);
					if ( this.response.html.match(/an-hide/) )
						box.hide();
					else
						box.show();
				}
			}
		}).send();		
	}
});

(function(){
	Delegator.register('click', 'BS.showPopup', {
		handler: function(event, link, api) {
			var target, url;	
			event.preventDefault();
			if ( api.get('target') ) {
				target = link.getElement(api.get('target'));
			} 
			if ( api.get('url') ) {			
				url	   = api.get('url');
			}
			if ( !url && !target ) {
				api.fail('Need either a url to the content or can\'t find the target element');
			}
						
			if ( target )								
				target.getBehaviorResult('BS.Popup').show();
			else {
				var popup = Bootstrap.Popup.from({
					header : 'Prompt.loading'.translate(),
					body   : '<div class="uiActivityIndicator">&nbsp;</div>',
					buttons : [{name: 'Action.close'.translate(), dismiss:true}]
				});
				popup.show();			
				var req = new Request.HTML({
					url : url,
					onSuccess : function(nodes, tree, html) { 
					    var title = html.parseHTML().getElement('.popup-header');
					    var body  = html.parseHTML().getElement('.popup-body');
					    if ( title ) {
					    	popup.element.getElement('.modal-header').empty().adopt(title);
					    }
					    if ( body ) {
					    	popup.element.getElement('.modal-body').empty().adopt(body);
					    }
					}
				}).get();
			}
		}

	}, true);

})();

Request.Options = {};

/**
 * Paginations
 */
(function()
{        
    /*
    var StreamPagination = new Class({
        initialize : function(url)
        {
            this.url = url.toURI();
            new Request.HTML({
                url : this.url
            }).get();
        },
    });*/
    var Pagination = new Class(
    {
        __cache            : {},
        __internal_pointer : 0,
        getPage  : function(url, options)
        {
            var urlKey = this._urlToKey(url);
            options = options || {}
            Object.set(options,{
               onSuccess    : Function.from(),
            });
            if ( !this.__cache[urlKey] )
            {
                Object.append(options, {             
                    url : url
                });
                this.__cache[urlKey] = new Request(options).get();
            } else 
            {
                var request = this.__cache[urlKey];
                var callSuccess = function() {                   
                    request.__event_registered = true;
                    if ( options.spinner )
                        options.spinner.unspin();
                    options.onSuccess.call(request);
                }
                if ( request.isRunning() )                     
                     request.addEvent('success', callSuccess);
                else {
                    callSuccess.call();
                }
            }
        },
        cache    : function(url, times)
        {
            times = times || 2;
            url   = url.toURI();
            (times).times(function(i) {
               i += this.__internal_pointer;
               var start   = parseInt(url.getData('start')) || 0,
               limit       = parseInt(url.getData('limit'));
               var nextURL = Object.clone(url).setData({limit:limit,start:start + limit * i}, true);
               this.getPage(nextURL.toString());
            }.bind(this));
            this.__internal_pointer += (Math.max(0,times-1));
        },
        _urlToKey : function(url)
        {
            url =  url.toURI();
            var key =  url.getData('option') + url.getData('view') + url.getData('start') + url.getData('limit');
            return key;            
        }        
    });
    
    Behavior.addGlobalFilter('ScrollPagination',{
        defaults : {
            set         : '!div !+ .an-entities',
            record      : '.an-entity',
            cacheCount  : 2 
        },
        setup  : function(el, api) 
        {	      
            var currentSet = el.getElement(api.get('set'));
            if ( !currentSet )
            	return;
            var pagination = currentSet.retrieve('paginatinCache:instance');
            if ( !pagination )
                pagination = new Pagination();
            currentSet.store('paginatinCache:instance', pagination);
            pagination.cache(el.get('href'), api.getAs(Number, 'cacheCount'));
            Object.append(el,{
                paginate : function() 
                {
                    if ( this.paginating ||  !currentSet.isVisible() )
                        return;
                    this.paginating = true;
                    var self        = this;
                    el.spin();
                    pagination.getPage(this.get('href'), {
                        spinner   : el,
                        onSuccess : function() 
                        {
                            el.get('spinner').hide(true);
                            var html          = this.response.text.parseHTML();                            
                            var records       = html.getElements(api.get('record'));
                            if (records.length == 0) {                            	
                                html.getElement('.alert').replaces(self.getParent());
                            } else 
                            {
                                var mEl = html.getElement('.an-get-more-records')
                                if ( mEl) 
                                {
                                    mEl.replaces(self);
                                    window.behavior.apply(mEl.getParent());
                                } 
                                else self.dispose();
                                currentSet.adopt(records);
                                window.behavior.apply(currentSet);
                            }
                        }
                    });
                }.bind(el)
            });
                        
            el.addEvent('click:once', function(event) {
                event.stop();
                el.paginate();
            });
            
            var scroller = new ScrollLoader({
                area      : 400,
                onScroll  : function() {
                    el.paginate();
                }
            });
        }
    });
})()

Behavior.addGlobalFilter('Pagination', {
	setup : function(el, api) {
		var links = el.getElements('a');
		links.addEvent('click', function(e){
			e.stop();
			if ( this.getParent().hasClass('active') || this.getParent().hasClass('disabled') )
				return;
			var uri   	= this.get('href').toURI();
			var current	= new URI(document.location).getData();				
			//only add the queries to hash that are different 
			//from the current
			var hash = {};
			Object.each(uri.getData(), function(value, key) {
				//if not value skip
				if ( !value )
					return;				
				//if the value is either option,layout,view skip
				if ( ['layout','option','view'].contains(key) ) {
					return;
				}
				//no duplicate value
				if ( current[key] != value ) {
					hash[key] = value;
				}
 			});
			document.location.hash = Object.toQueryString(hash);
			this.ajaxRequest({			
				method 	  :  'get'  ,
				onSuccess : function() {					
					var html = this.response.html.parseHTML();
					html.getElements('.pagination').replaces(document.getElements('.pagination'));
					html.getElement('.an-entities')
					.replaces(document.getElement('.an-entities'));
				}
			}).send();
		})
	}
});


window.addEvent('domready',
(function(){
	var uri = document.location.toString().toURI();
	if ( uri.getData('start', 'fragment') ) {
		uri.setData(uri.getData(null, 'fragment'), true);
		uri.set('fragment','');
		uri.go();
	}
	else if ( uri.getData('permalink', 'fragment') ) {
		uri.setData({permalink:uri.getData('permalink', 'fragment')}, true);
		uri.set('fragment','');
		uri.go();
	} else if ( uri.getData('scroll', 'fragment') ) {
		window.addEvent('domready', function() {
			var selector = uri.getData('scroll', 'fragment');
			var element  = document.getElement('[scroll-handle="'+selector+'"]') || document.getElement(selector);
			if ( element )
				new Fx.Scroll(window).toElement(element).chain(element.highlight.bind(element));
		});
	}	
}));

Behavior.addGlobalFilter('PlaceHolder', {
    defaults : {
        element  : '.placeholder'
    },
    setup : function(element, api) 
    {
        var placeholder = element.getElement(api.getAs(String, 'element'));        
        element.store('placeholder:element', placeholder);
        Object.append(element,  {
            setContent      : function(content) 
            {
                element.store('placeholder:content', content);
                element.adopt(content);
                element.showContent();                
            },
            toggleContent   : function(event) 
            {
                event = event || 'click';
                element.addEvent(event,  function(e) {
                    e.eventHandled = true;                    
                    element.showContent();
                });
                var area = element.getElement(api.getAs(String,'area')) || element;
                area.onOutside(event, function(e){
                    if ( !e.eventHandled )
                        element.hideContent();
                });
            },
            showContent     : function() 
            {
                var content = element.retrieve('placeholder:content'), 
                placeholder = element.retrieve('placeholder:element'); 
                placeholder.hide();
                content.fade('show').show();
            },
            hideContent : function() 
            {
                var content = element.retrieve('placeholder:content'), 
                placeholder = element.retrieve('placeholder:element');
                content.get('tween').chain(function(){
                    content.hide();
                    placeholder.show();
                });
                content.fade('out');                
            }
        });
    }
});

/**
 * Fixes Bootrap Drop down
 */

Class.refactor(Bootstrap.Dropdown, {
			
    _handle: function(e){
        var el = e.target;
        var open = el.getParent('.open');
        if (!el.match(this.options.ignore) || !open) this.hideAll();
        if (this.element.contains(el)) {
            var parent = el.match('.dropdown-toggle') ? el.getParent() : el.getParent('.dropdown-toggle');
            if (parent) {
                e.preventDefault();
                if (!open) this.show(el.getParent('.dropdown') || parent);
            }
        }
    }
});

Delegator.register(['click'],'Comment', {
	handler  : function(event, el, api) {
		event.stop();
		var textarea = el.form.getElement('textarea');
		if ( textarea.setContentFromEditor )
			textarea.setContentFromEditor();
		if ( Form.Validator.getValidator('required').test(el.form.getElement('textarea')) )
			window.delegator.trigger('Request',el,'click');
	}
});

var ScrollLoader = new Class({

    Implements: [Options, Events],

    options: {
        /*onScroll: fn,*/
        area: 50,
        mode: 'vertical',
        container  : null,
        scrollable : window
    },
    initialize: function(options) 
    {
        this.setOptions(options);
        this.scrollable = document.id(this.options.scrollable) || window;
        this.element    = document.id(this.options.container)  || this.scrollable;
        this.bounds     = {
            scroll : this.scroll.bind(this)
        }
        this.attach();
    },
    attach: function() 
    {
        this.scrollable.addEvent('scroll', this.bounds.scroll);
        return this;
    },
    detach: function()
    {
        this.scrollable.removeEvent('scroll', this.bounds.scroll);
        return this;
    },
    scroll: function() 
    {
        var z = (this.options.mode == 'vertical') ? 'y' : 'x';

        var element = this.element,
            size = element.getSize()[z],
            scroll = element.getScroll()[z],
            scrollSize = element.getScrollSize()[z];

        if (scroll + size < scrollSize - this.options.area) return;
        
        this.fireEvent('scroll');
    }
});

var EditEntityOptions = function() {
	return {
		replace : this.getParent('form'),
		url		: function() {
			return this.form.get('action') + '&layout=list&reset=1';
		}
	}
}


var EntityHelper = new Class({
	
	initialize: function(){
		this.form = document.id('entity-form');
	},
	
	resetForm : function(){
		this.form.title.value = '';
		this.form.description.value = '';
	},
	
	add : function(){
		
		if(this.form.title.value.clean().length < 3)
			return false;
		
		this.form.ajaxRequest({
			method : 'post',
			url : this.form.get('action') + '&layout=list&reset=1',
			data : this.form,
			inject : {
				element : document.getElement('.an-entities'),
				where   : 'top'
			},
			onSuccess : function(form){
				var element = document.getElement('.an-entities').getElement('.an-entity');
				this.resetForm();
			}.bind(this)
		}).send();
	}
});

Behavior.addGlobalFilter('Scrollable',{
	defaults : {
	
	},
	returns : Scrollable,
    setup   : function(el, api)
    {
    	var container = el;
    	if ( api.getAs(String,'container') ) {
    		container = el.getElement(api.getAs(String,'container'));
    	}
		return new Scrollable(container);    
    }
})