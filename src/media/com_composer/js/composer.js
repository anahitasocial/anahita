Behavior.addGlobalFilter('ComposerForm', {
    defaults : {
      trigger : 'button'  
    },
    setup : function(el,api)
    {        
        if ( el.getElement('input[type="file"]') )
        {
            (function(el){
                var submitted = false;
                new Element('input',{type:'hidden','name':'composed','value':'true'}).inject(el);
                new Element('input',{type:'hidden','name':'format','value':'raw'}).inject(el);
                var iframe = new IFrame().hide().inject(el);
                el.set('target', iframe.get('name'));
                iframe.addEvent('load', function(){
                    el.unspin();
                    if ( submitted ) {
                        var story   = window.frames[this.get('name')].document.body.innerHTML;
                        story = story.parseHTML().getElement('*');
                        story.inject(document.id('an-stories'), 'top');                        
                        el.reset();
				        window.delegator.attach(document);
				        window.behavior.apply(document.body);
                    }
                });
                //create an iframe 
                el.addEvent('submit', function(e){
                    submitted = true;
                    if ( el.retrieve('validator').validate() )
                        el.spin();
                });
                
            })(el);
        }
        else 
        {
            var trigger = el.getElement(api.getAs(String, 'trigger'));
            trigger.addEvent('click', function(event){            
                event.stop();
                el.ajaxRequest({
                    data            : el.toQueryString() + '&composed=true',
                    inject          : 'an-stories',
                    onSuccess       : function() {
                        this.reset();
                    }.bind(el)
                }).send();            
            });
        }
    }
});

(function(){
    var composerTabs;    
    Behavior.addGlobalFilter('ComposerTabs', {    
        defaults : {
            focus : 'input'
        },
        setup : function(el,api)
        {
            var focusInput = function(section) {
              //element.getElement(api.getAs(String,'focus')).focus();
                var element;
                if ( element = section.getElement('input[type="text"]') ) {
                    element.focus();
                } else if ( element = section.getElement('textarea') ) {
                    element.focus();
                }
            }
            window.behavior.applyFilter(el.getParent(), Behavior.getFilter('BS.Dropdown'));
            window.behavior.applyFilter(el, Behavior.getFilter('BS.Tabs'));
            var nagivation  = document.getElement('.sidelinks');
            composerTabs    = el.getBehaviorResult('BS.Tabs');
            //if there's a navigation
            if ( nagivation )
            {
                window.behavior.applyFilter(nagivation, Behavior.getFilter('BS.Tabs'));                                  
                var profileTabs = nagivation.getBehaviorResult('BS.Tabs')
                if ( profileTabs ) 
                {
                    profileTabs.addEvent('active', function(idx, nav, section){
                        if ( idx == 0 ) {
                            el.getParent().show();
                        } else {
                            el.getParent().hide();
                        }                
                    });
                }                
            }
          
            composerTabs.addEvent('active', function(idx,section,tab) {
				el.getElement('.composer-button-title').set('text', tab.get('text'));
                if ( section.get('data-content-url') )
                {
                    if ( !section.retrieve('request'))
                    {
                        var request = section.ajaxRequest({
                            url         : section.get('data-content-url'),
                            onSuccess   : function() {
                                var element = this.response.text.parseHTML().getElement('*');
                                section.setContent(element);
                                element.get('tween').chain(function(){
                                    section.toggleContent();
                                    focusInput(section);
                                });
                                element.fade('hide').fade('in');
                            }
                        }).get();
                        section.store('request', request);
                    }
                 }
                               
             });
            composerTabs.addEvent('background', function(idx,section,tab){
                if ( section.getElement('form') && section.getElement('form').get('validator') ) {
                    section.getElement('form').get('validator').reset();
                }
            });     
                        
            if ( composerTabs.tabs.length == 1 ) {
                composerTabs.tabs[0].retrieve('section').replaces(el);
            }
                        
        }
    
    });
    Delegator.register('click', 'LoadComposerTab', function(event, el, api) {        
    	var index = api.getAs(Number, 'index');        
    	if ( composerTabs && composerTabs.now == index )
        {
            composerTabs.now = -1;
            composerTabs.show(index);                
        }
    });    
})();