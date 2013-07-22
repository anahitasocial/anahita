
//This library: http://dev.clientcide.com/depender/build?download=true&version=Clientcide+3.0.8&excludeLibs=Core&require=More-Behaviors%2FDelegator.AddRemoveClass&require=More-Behaviors%2FDelegator.Ajax&require=More-Behaviors%2FDelegator.CheckAllOrNone&require=More-Behaviors%2FDelegator.FxReveal&require=More-Behaviors%2FDelegator.SubmitLink&require=More-Behaviors%2FBehavior.Resizable&require=More-Behaviors%2FBehavior.Sortable&require=More-Behaviors%2FBehavior.FormValidator&require=More-Behaviors%2FBehavior.OverText&require=More-Behaviors%2FBehavior.Accordion&require=More-Behaviors%2FBehavior.HtmlTable&require=Clientcide%2FBehavior.Tabs&excludeLibs=More
//Contents: Behavior:Source/Event.Mock.js, Behavior:Source/Element.Data.js, Behavior:Source/BehaviorAPI.js, Behavior:Source/Behavior.js, Behavior:Source/Delegator.js, More-Behaviors:Source/Delegators/Delegator.CheckAllOrNone.js, More-Behaviors:Source/Delegators/Delegator.SubmitLink.js, More-Behaviors:Source/Delegators/Delegator.FxReveal.js, More-Behaviors:Source/Forms/Behavior.FormValidator.js, More-Behaviors:Source/Delegators/Delegator.AddRemoveClass.js, More-Behaviors:Source/Drag/Behavior.Resizable.js, More-Behaviors:Source/Delegators/Delegator.Ajax.js, More-Behaviors:Source/Forms/Behavior.OverText.js, More-Behaviors:Source/Fx/Behavior.FxAccordion.js, More-Behaviors:Source/Drag/Behavior.Sortable.js, More-Behaviors:Source/Interface/Behavior.HtmlTable.js, Clientcide:Source/Layout/TabSwapper.js, Clientcide:Source/Behaviors/Behavior.Tabs.js

// Begin: Source/Event.Mock.js
/*
---
name: Event.Mock

description: Supplies a Mock Event object for use on fireEvent

license: MIT-style

authors:
- Arieh Glazer

requires: Core/Event

provides: [Event.Mock]

...
*/

(function($,window,undef){

/**
 * creates a Mock event to be used with fire event
 * @param Element target an element to set as the target of the event - not required
 *  @param string type the type of the event to be fired. Will not be used by IE - not required.
 *
 */
Event.Mock = function(target,type){
	type = type || 'click';

	var e = {
		type: type,
		target: target
	};

	if (document.createEvent){
		e = document.createEvent('HTMLEvents');
		e.initEvent(
			type //event type
			, false //bubbles - set to false because the event should like normal fireEvent
			, true //cancelable
		);
	}

	e = new Event(e);

	e.target = target;

	return e;
};

})(document.id,window);

// Begin: Source/Element.Data.js
/*
---
name: Element.Data
description: Stores data in HTML5 data properties
provides: [Element.Data]
requires: [Core/Element, Core/JSON]
script: Element.Data.js

...
*/
(function(){

	JSON.isSecure = function(string){
		//this verifies that the string is parsable JSON and not malicious (borrowed from JSON.js in MooTools, which in turn borrowed it from Crockford)
		//this version is a little more permissive, as it allows single quoted attributes because forcing the use of double quotes
		//is a pain when this stuff is used as HTML properties
		return (/^[,:{}\[\]0-9.\-+Eaeflnr-u \n\r\t]*$/).test(string.replace(/\\./g, '@').replace(/"[^"\\\n\r]*"/g, '').replace(/'[^'\\\n\r]*'/g, ''));
	};

	Element.implement({
		/*
			sets an HTML5 data property.
			arguments:
				name - (string) the data name to store; will be automatically prefixed with 'data-'.
				value - (string, number) the value to store.
		*/
		setData: function(name, value){
			return this.set('data-' + name.hyphenate(), value);
		},

		getData: function(name, defaultValue){
			var value = this.get('data-' + name.hyphenate());
			if (value != undefined){
				return value;
			} else if (defaultValue != undefined){
				this.setData(name, defaultValue);
				return defaultValue;
			}
		},

		/* 
			arguments:
				name - (string) the data name to store; will be automatically prefixed with 'data-'
				value - (string, array, or object) if an object or array the object will be JSON encoded; otherwise stored as provided.
		*/
		setJSONData: function(name, value){
			return this.setData(name, JSON.encode(value));
		},

		/*
			retrieves a property from HTML5 data property you specify
		
			arguments:
				name - (retrieve) the data name to store; will be automatically prefixed with 'data-'
				strict - (boolean) if true, will set the JSON.decode's secure flag to true; otherwise the value is still tested but allows single quoted attributes.
				defaultValue - (string, array, or object) the value to set if no value is found (see storeData above)
		*/
		getJSONData: function(name, strict, defaultValue){
			var value = this.get('data-' + name);
			if (value != undefined){
				if (value && JSON.isSecure(value)) {
					return JSON.decode(value, strict);
				} else {
					return value;
				}
			} else if (defaultValue != undefined){
				this.setJSONData(name, defaultValue);
				return defaultValue;
			}
		}

	});

})();

// Begin: Source/BehaviorAPI.js
/*
---
name: BehaviorAPI
description: HTML getters for Behavior's API model.
requires: [Core/Class, /Element.Data]
provides: [BehaviorAPI]
...
*/


(function(){
	//see Docs/BehaviorAPI.md for documentation of public methods.

	var reggy = /[^a-z0-9\-]/gi;

	window.BehaviorAPI = new Class({
		element: null,
		prefix: '',
		defaults: {},

		initialize: function(element, prefix){
			this.element = element;
			this.prefix = prefix.toLowerCase().replace('.', '-', 'g').replace(reggy, '');
		},

		/******************
		 * PUBLIC METHODS
		 ******************/

		get: function(/* name[, name, name, etc] */){
			if (arguments.length > 1) return this._getObj(Array.from(arguments));
			return this._getValue(arguments[0]);
		},

		getAs: function(/*returnType, name, defaultValue OR {name: returnType, name: returnType, etc}*/){
			if (typeOf(arguments[0]) == 'object') return this._getValuesAs.apply(this, arguments);
			return this._getValueAs.apply(this, arguments);
		},

		require: function(/* name[, name, name, etc] */){
			for (var i = 0; i < arguments.length; i++){
				if (this._getValue(arguments[i]) == undefined) throw new Error('Could not retrieve ' + this.prefix + '-' + arguments[i] + ' option from element.');
			}
			return this;
		},

		requireAs: function(returnType, name /* OR {name: returnType, name: returnType, etc}*/){
			var val;
			if (typeOf(arguments[0]) == 'object'){
				for (var objName in arguments[0]){
					val = this._getValueAs(arguments[0][objName], objName);
					if (val === undefined || val === null) throw new Error("Could not retrieve " + this.prefix + '-' + objName + " option from element.");
				}
			} else {
				val = this._getValueAs(returnType, name);
				if (val === undefined || val === null) throw new Error("Could not retrieve " + this.prefix + '-' + name + " option from element.");
			}
			return this;
		},

		setDefault: function(name, value /* OR {name: value, name: value, etc }*/){
			if (typeOf(arguments[0]) == 'object'){
				for (var objName in arguments[0]){
					this.setDefault(objName, arguments[0][objName]);
				}
				return;
			}
			name = name.camelCase();
			this.defaults[name] = value;
			if (this._getValue(name) == null){
				var options = this._getOptions();
				options[name] = value;
			}
			return this;
		},

		refreshAPI: function(){
			delete this.options;
			this.setDefault(this.defaults);
			return;
		},

		/******************
		 * PRIVATE METHODS
		 ******************/

		//given an array of names, returns an object of key/value pairs for each name
		_getObj: function(names){
			var obj = {};
			names.each(function(name){
				var value = this._getValue(name);
				if (value !== undefined) obj[name] = value;
			}, this);
			return obj;
		},
		//gets the data-behaviorname-options object and parses it as JSON
		_getOptions: function(){
			if (!this.options){
				var options = this.element.getData(this.prefix + '-options', '{}');
				if (options && options.substring(0,1) != '{') options = '{' + options + '}';
				var isSecure = JSON.isSecure(options);
				if (!isSecure) throw new Error('warning, options value for element is not parsable, check your JSON format for quotes, etc.');
				this.options = isSecure ? JSON.decode(options) : {};
				for (option in this.options) {
					this.options[option.camelCase()] = this.options[option];
				}
			}
			return this.options;
		},
		//given a name (string) returns the value for it
		_getValue: function(name){
			name = name.camelCase();
			var options = this._getOptions();
			if (!options.hasOwnProperty(name)){
				var inline = this.element.getData(this.prefix + '-' + name.hyphenate());
				if (inline) options[name] = inline;
			}
			return options[name];
		},
		//given a Type and a name (string) returns the value for it coerced to that type if possible
		//else returns the defaultValue or null
		_getValueAs: function(returnType, name, defaultValue){
			var value = this._getValue(name);
			if (value == null || value == undefined) return defaultValue;
			var coerced = this._coerceFromString(returnType, value);
			if (coerced == null) throw new Error("Could not retrieve value '" + name + "' as the specified type. Its value is: " + value);
			return coerced;
		},
		//given an object of name/Type pairs, returns those as an object of name/value (as specified Type) pairs
		_getValuesAs: function(obj){
			var returnObj = {};
			for (var name in obj){
				returnObj[name] = this._getValueAs(obj[name], name);
			}
			return returnObj;
		},
		//attempts to run a value through the JSON parser. If the result is not of that type returns null.
		_coerceFromString: function(toType, value){
			if (typeOf(value) == 'string' && toType != String){
				if (JSON.isSecure(value)) value = JSON.decode(value);
			}
			if (instanceOf(value, toType)) return value;
			return null;
		}
	});

})();

// Begin: Source/Behavior.js
/*
---
name: Behavior
description: Auto-instantiates widgets/classes based on parsed, declarative HTML.
requires: [Core/Class.Extras, Core/Element.Event, Core/Selectors, More/Table, /Element.Data, /BehaviorAPI]
provides: [Behavior]
...
*/

(function(){

	var getLog = function(method){
		return function(){
			if (window.console && console[method]){
				if(console[method].apply) console[method].apply(console, arguments);
				else console[method](Array.from(arguments).join(' '));
			}
		};
	};

	var PassMethods = new Class({
		//pass a method pointer through to a filter
		//by default the methods for add/remove events are passed to the filter
		//pointed to this instance of behavior. you could use this to pass along
		//other methods to your filters. For example, a method to close a popup
		//for filters presented inside popups.
		passMethod: function(method, fn){
			if (this.API.prototype[method]) throw new Error('Cannot overwrite API method ' + method + ' as it already exists');
			this.API.implement(method, fn);
			return this;
		},

		passMethods: function(methods){
			for (method in methods) this.passMethod(method, methods[method]);
			return this;
		}

	});

	var spaceOrCommaRegex = /\s*,\s*|\s+/g;

	BehaviorAPI.implement({
		deprecate: function(deprecated, asJSON){
			var set,
			    values = {};
			Object.each(deprecated, function(prop, key){
				var value = this.element[ asJSON ? 'getJSONData' : 'getData'](prop);
				if (value !== undefined){
					set = true;
					values[key] = value;
				}
			}, this);
			this.setDefault(values);
			return this;
		}
	});

	this.Behavior = new Class({

		Implements: [Options, Events, PassMethods],

		options: {
			//by default, errors thrown by filters are caught; the onError event is fired.
			//set this to *true* to NOT catch these errors to allow them to be handled by the browser.
			// breakOnErrors: false,
			// container: document.body,

			//default error behavior when a filter cannot be applied
			onError: getLog('error'),
			onWarn: getLog('warn'),
			enableDeprecation: true,
			selector: '[data-behavior]'
		},

		initialize: function(options){
			this.setOptions(options);
			this.API = new Class({ Extends: BehaviorAPI });
			this.passMethods({
				addEvent: this.addEvent.bind(this),
				removeEvent: this.removeEvent.bind(this),
				addEvents: this.addEvents.bind(this),
				removeEvents: this.removeEvents.bind(this),
				fireEvent: this.fireEvent.bind(this),
				applyFilters: this.apply.bind(this),
				applyFilter: this.applyFilter.bind(this),
				getContentElement: this.getContentElement.bind(this),
				cleanup: this.cleanup.bind(this),
				getContainerSize: function(){
					return this.getContentElement().measure(function(){
						return this.getSize();
					});
				}.bind(this),
				error: function(){ this.fireEvent('error', arguments); }.bind(this),
				fail: function(){
					var msg = Array.join(arguments, ' ');
					throw new Error(msg);
				},
				warn: function(){
					this.fireEvent('warn', arguments);
				}.bind(this)
			});
		},

		getContentElement: function(){
			return this.options.container || document.body;
		},

		//Applies all the behavior filters for an element.
		//container - (element) an element to apply the filters registered with this Behavior instance to.
		//force - (boolean; optional) passed through to applyFilter (see it for docs)
		apply: function(container, force){
		  this._getElements(container).each(function(element){
				var plugins = [];
				element.getBehaviors().each(function(name){
					var filter = this.getFilter(name);
					if (!filter){
						this.fireEvent('error', ['There is no filter registered with this name: ', name, element]);
					} else {
						var config = filter.config;
						if (config.delay !== undefined){
							this.applyFilter.delay(filter.config.delay, this, [element, filter, force]);
						} else if(config.delayUntil){
							this._delayFilterUntil(element, filter, force);
						} else if(config.initializer){
							this._customInit(element, filter, force);
						} else {
							plugins.append(this.applyFilter(element, filter, force, true));
						}
					}
				}, this);
				plugins.each(function(plugin){ plugin(); });
			}, this);
			return this;
		},

		_getElements: function(container){
			if (typeOf(this.options.selector) == 'function') return this.options.selector(container);
			else return document.id(container).getElements(this.options.selector);
		},

		//delays a filter until the event specified in filter.config.delayUntil is fired on the element
		_delayFilterUntil: function(element, filter, force){
			var events = filter.config.delayUntil.split(','),
			    attached = {},
			    inited = false;
			var clear = function(){
				events.each(function(event){
					element.removeEvent(event, attached[event]);
				});
				clear = function(){};
			};
			events.each(function(event){
				var init = function(e){
					clear();
					if (inited) return;
					inited = true;
					var setup = filter.setup;
					filter.setup = function(element, api, _pluginResult){
						api.event = e;
						return setup.apply(filter, [element, api, _pluginResult]);
					};
					this.applyFilter(element, filter, force);
					filter.setup = setup;
				}.bind(this);
				element.addEvent(event, init);
				attached[event] = init;
			}, this);
		},

		//runs custom initiliazer defined in filter.config.initializer
		_customInit: function(element, filter, force){
			var api = new this.API(element, filter.name);
			api.runSetup = this.applyFilter.pass([element, filter, force], this);
			filter.config.initializer(element, api);
		},

		//Applies a specific behavior to a specific element.
		//element - the element to which to apply the behavior
		//filter - (object) a specific behavior filter, typically one registered with this instance or registered globally.
		//force - (boolean; optional) apply the behavior to each element it matches, even if it was previously applied. Defaults to *false*.
		//_returnPlugins - (boolean; optional; internal) if true, plugins are not rendered but instead returned as an array of functions
		//_pluginTargetResult - (obj; optional internal) if this filter is a plugin for another, this is whatever that target filter returned
		//                      (an instance of a class for example)
		applyFilter: function(element, filter, force, _returnPlugins, _pluginTargetResult){
			var pluginsToReturn = [];
			if (this.options.breakOnErrors){
				pluginsToReturn = this._applyFilter.apply(this, arguments);
			} else {
				try {
					pluginsToReturn = this._applyFilter.apply(this, arguments);
				} catch (e){
					this.fireEvent('error', ['Could not apply the behavior ' + filter.name, e]);
				}
			}
			return _returnPlugins ? pluginsToReturn : this;
		},

		//see argument list above for applyFilter
		_applyFilter: function(element, filter, force, _returnPlugins, _pluginTargetResult){
			var pluginsToReturn = [];
			element = document.id(element);
			//get the filters already applied to this element
			var applied = getApplied(element);
			//if this filter is not yet applied to the element, or we are forcing the filter
			if (!applied[filter.name] || force){
				//if it was previously applied, garbage collect it
				if (applied[filter.name]) applied[filter.name].cleanup(element);
				var api = new this.API(element, filter.name);

				//deprecated
				api.markForCleanup = filter.markForCleanup.bind(filter);
				api.onCleanup = function(fn){
					filter.markForCleanup(element, fn);
				};

				if (filter.config.deprecated && this.options.enableDeprecation) api.deprecate(filter.config.deprecated);
				if (filter.config.deprecateAsJSON && this.options.enableDeprecation) api.deprecate(filter.config.deprecatedAsJSON, true);

				//deal with requirements and defaults
				if (filter.config.requireAs){
					api.requireAs(filter.config.requireAs);
				} else if (filter.config.require){
					api.require.apply(api, Array.from(filter.config.require));
				}

				if (filter.config.defaults) api.setDefault(filter.config.defaults);

				//apply the filter
				var result = filter.setup(element, api, _pluginTargetResult);
				if (filter.config.returns && !instanceOf(result, filter.config.returns)){
					throw new Error("Filter " + filter.name + " did not return a valid instance.");
				}
				element.store('Behavior Filter result:' + filter.name, result);
				//and mark it as having been previously applied
				applied[filter.name] = filter;
				//apply all the plugins for this filter
				var plugins = this.getPlugins(filter.name);
				if (plugins){
					for (var name in plugins){
						if (_returnPlugins){
							pluginsToReturn.push(this.applyFilter.pass([element, plugins[name], force, null, result], this));
						} else {
							this.applyFilter(element, plugins[name], force, null, result);
						}
					}
				}
			}
			return pluginsToReturn;
		},

		//given a name, returns a registered behavior
		getFilter: function(name){
			return this._registered[name] || Behavior.getFilter(name);
		},

		getPlugins: function(name){
			return this._plugins[name] || Behavior._plugins[name];
		},

		//Garbage collects all applied filters for an element and its children.
		//element - (*element*) container to cleanup
		//ignoreChildren - (*boolean*; optional) if *true* only the element will be cleaned, otherwise the element and all the
		//	  children with filters applied will be cleaned. Defaults to *false*.
		cleanup: function(element, ignoreChildren){
			element = document.id(element);
			var applied = getApplied(element);
			for (var filter in applied){
				applied[filter].cleanup(element);
				element.eliminate('Behavior Filter result:' + filter);
				delete applied[filter];
			}
			if (!ignoreChildren) this._getElements(element).each(this.cleanup, this);
			return this;
		}

	});

	//Export these for use elsewhere (notabily: Delegator).
	Behavior.getLog = getLog;
	Behavior.PassMethods = PassMethods;


	//Returns the applied behaviors for an element.
	var getApplied = function(el){
		return el.retrieve('_appliedBehaviors', {});
	};

	//Registers a behavior filter.
	//name - the name of the filter
	//fn - a function that applies the filter to the given element
	//overwrite - (boolean) if true, will overwrite existing filter if one exists; defaults to false.
	var addFilter = function(name, fn, overwrite){
		if (!this._registered[name] || overwrite) this._registered[name] = new Behavior.Filter(name, fn);
		else throw new Error('Could not add the Behavior filter "' + name  +'" as a previous trigger by that same name exists.');
	};

	var addFilters = function(obj, overwrite){
		for (var name in obj){
			addFilter.apply(this, [name, obj[name], overwrite]);
		}
	};

	//Registers a behavior plugin
	//filterName - (*string*) the filter (or plugin) this is a plugin for
	//name - (*string*) the name of this plugin
	//setup - a function that applies the filter to the given element
	var addPlugin = function(filterName, name, setup, overwrite){
		if (!this._plugins[filterName]) this._plugins[filterName] = {};
		if (!this._plugins[filterName][name] || overwrite) this._plugins[filterName][name] = new Behavior.Filter(name, setup);
		else throw new Error('Could not add the Behavior filter plugin "' + name  +'" as a previous trigger by that same name exists.');
	};

	var addPlugins = function(obj, overwrite){
		for (var name in obj){
			addPlugin.apply(this, [obj[name].fitlerName, obj[name].name, obj[name].setup], overwrite);
		}
	};

	var setFilterDefaults = function(name, defaults){
		var filter = this.getFilter(name);
		if (!filter.config.defaults) filter.config.defaults = {};
		Object.append(filter.config.defaults, defaults);
	};

	//Add methods to the Behavior namespace for global registration.
	Object.append(Behavior, {
		_registered: {},
		_plugins: {},
		addGlobalFilter: addFilter,
		addGlobalFilters: addFilters,
		addGlobalPlugin: addPlugin,
		addGlobalPlugins: addPlugins,
		setFilterDefaults: setFilterDefaults,
		getFilter: function(name){
			return this._registered[name];
		}
	});
	//Add methods to the Behavior class for instance registration.
	Behavior.implement({
		_registered: {},
		_plugins: {},
		addFilter: addFilter,
		addFilters: addFilters,
		addPlugin: addPlugin,
		addPlugins: addPlugins,
		setFilterDefaults: setFilterDefaults
	});

	//This class is an actual filter that, given an element, alters it with specific behaviors.
	Behavior.Filter = new Class({

		config: {
			/**
				returns: Foo,
				require: ['req1', 'req2'],
				//or
				requireAs: {
					req1: Boolean,
					req2: Number,
					req3: String
				},
				defaults: {
					opt1: false,
					opt2: 2
				},
				//simple example:
				setup: function(element, API){
					var kids = element.getElements(API.get('selector'));
					//some validation still has to occur here
					if (!kids.length) API.fail('there were no child elements found that match ', API.get('selector'));
					if (kids.length < 2) API.warn("there weren't more than 2 kids that match", API.get('selector'));
					var fooInstance = new Foo(kids, API.get('opt1', 'opt2'));
					API.onCleanup(function(){
						fooInstance.destroy();
					});
					return fooInstance;
				},
				delayUntil: 'mouseover',
				//OR
				delay: 100,
				//OR
				initializer: function(element, API){
					element.addEvent('mouseover', API.runSetup); //same as specifying event
					//or
					API.runSetup.delay(100); //same as specifying delay
					//or something completely esoteric
					var timer = (function(){
						if (element.hasClass('foo')){
							clearInterval(timer);
							API.runSetup();
						}
					}).periodical(100);
					//or
					API.addEvent('someBehaviorEvent', API.runSetup);
				});
				*/
		},

		//Pass in an object with the following properties:
		//name - the name of this filter
		//setup - a function that applies the filter to the given element
		initialize: function(name, setup){
			this.name = name;
			if (typeOf(setup) == "function"){
				this.setup = setup;
			} else {
				Object.append(this.config, setup);
				this.setup = this.config.setup;
			}
			this._cleanupFunctions = new Table();
		},

		//Stores a garbage collection pointer for a specific element.
		//Example: if your filter enhances all the inputs in the container
		//you might have a function that removes that enhancement for garbage collection.
		//You would mark each input matched with its own cleanup function.
		//NOTE: this MUST be the element passed to the filter - the element with this filters
		//      name in its data-behavior property. I.E.:
		//<form data-behavior="FormValidator">
		//  <input type="text" name="email"/>
		//</form>
		//If this filter is FormValidator, you can mark the form for cleanup, but not, for example
		//the input. Only elements that match this filter can be marked.
		markForCleanup: function(element, fn){
			var functions = this._cleanupFunctions.get(element);
			if (!functions) functions = [];
			functions.include(fn);
			this._cleanupFunctions.set(element, functions);
			return this;
		},

		//Garbage collect a specific element.
		//NOTE: this should be an element that has a data-behavior property that matches this filter.
		cleanup: function(element){
			var marks = this._cleanupFunctions.get(element);
			if (marks){
				marks.each(function(fn){ fn(); });
				this._cleanupFunctions.erase(element);
			}
			return this;
		}

	});

	Behavior.elementDataProperty = 'behavior';

	Element.implement({

		addBehavior: function(name){
			return this.setData(Behavior.elementDataProperty, this.getBehaviors().include(name).join(' '));
		},

		removeBehavior: function(name){
			return this.setData(Behavior.elementDataProperty, this.getBehaviors().erase(name).join(' '));
		},

		getBehaviors: function(){
			var filters = this.getData(Behavior.elementDataProperty);
			if (!filters) return [];
			return filters.trim().split(spaceOrCommaRegex);
		},

		hasBehavior: function(name){
			return this.getBehaviors().contains(name);
		},

		getBehaviorResult: function(name){
			return this.retrieve('Behavior Filter result:' + name);
		}

	});


})();


// Begin: Source/Delegator.js
/*
---
name: Delegator
description: Allows for the registration of delegated events on a container.
requires: [Core/Element.Delegation, Core/Options, Core/Events, /Event.Mock, /Behavior]
provides: [Delegator]
...
*/
(function(){

	var spaceOrCommaRegex = /\s*,\s*|\s+/g;

	window.Delegator = new Class({

		Implements: [Options, Events, Behavior.PassMethods],

		options: {
			// breakOnErrors: false,
			getBehavior: function(){},
			onError: Behavior.getLog('error'),
			onWarn: Behavior.getLog('warn')
		},

		initialize: function(options){
			this.setOptions(options);
			this._bound = {
				eventHandler: this._eventHandler.bind(this)
			};
			Delegator._instances.push(this);
			Object.each(Delegator._triggers, function(trigger){
				this._eventTypes.combine(trigger.types);
			}, this);
			this.API = new Class({ Extends: BehaviorAPI });
			this.passMethods({
				addEvent: this.addEvent.bind(this),
				removeEvent: this.removeEvent.bind(this),
				addEvents: this.addEvents.bind(this),
				removeEvents: this.removeEvents.bind(this),
				fireEvent: this.fireEvent.bind(this),
				attach: this.attach.bind(this),
				trigger: this.trigger.bind(this),
				error: function(){ this.fireEvent('error', arguments); }.bind(this),
				fail: function(){
					var msg = Array.join(arguments, ' ');
					throw new Error(msg);
				},
				warn: function(){
					this.fireEvent('warn', arguments);
				}.bind(this),
				getBehavior: function(){
					return this.options.getBehavior();
				}.bind(this)
			});

			this.bindToBehavior(this.options.getBehavior());
		},

		bindToBehavior: function(behavior){
			if (!behavior) return;
			this.unbindFromBehavior();
			this._behavior = behavior;
			if (!this._behaviorEvents){
				var self = this;
				this._behaviorEvents = {
					destroyDom: function(elements){
						Array.from(elements).each(function(element){
							self._behavior.cleanup(element);
							self._behavior.fireEvent('destroyDom', element);
						});
					},
					ammendDom: function(container){
						self._behavior.apply(container);
						self._behavior.fireEvent('ammendDom', container);
					}
				};
			}
			this.addEvents(this._behaviorEvents);
		},

		getBehavior: function(){
			return this._behavior;
		},

		unbindFromBehavior: function(){
			if (this._behaviorEvents && this._behavior){
				this._behavior.removeEvents(this._behaviorEvents);
				delete this._behavior;
			}
		},

		attach: function(target, _method){
			_method = _method || 'addEvent';
			target = document.id(target);
			if ((_method == 'addEvent' && this._attachedTo.contains(target)) ||
			    (_method == 'removeEvent') && !this._attachedTo.contains(target)) return this;
			this._eventTypes.each(function(event){
				target[_method](event + ':relay([data-trigger])', this._bound.eventHandler);
			}, this);
			this._attachedTo.push(target);
			return this;
		},

		detach: function(target){
			if (target){
				this.attach(target, 'removeEvent');
				return this;
			} else {
				this._attachedTo.each(this.detach, this);
			}
		},

		trigger: function(name, element, event){
			if (!event || typeOf(event) == "string") event = new Event.Mock(element, event);
			var trigger = this._getTrigger(name);
			if (trigger && trigger.types.contains(event.type)) {
				if (this.options.breakOnErrors){
					this._trigger(trigger, element, event);
				} else {
					try {
						this._trigger(trigger, element, event);
					} catch(e) {
						this.fireEvent('error', ['Could not apply the trigger', name, e]);
					}
				}
			} else {
				this.fireEvent('error', 'Could not find a trigger with the name ' + name + ' for event: ' + event.type);
			}
			return this;
		},

		/******************
		 * PRIVATE METHODS
		 ******************/

		_getTrigger: function(name){
			return this._triggers[name] || Delegator._triggers[name];
		},

		_trigger: function(trigger, element, event){
			var api = new this.API(element, trigger.name);
			if (trigger.requireAs){
				api.requireAs(trigger.requireAs);
			} else if (trigger.require){
				api.require.apply(api, Array.from(trigger.require));
			} if (trigger.defaults){
				api.setDefault(trigger.defaults);
			}
			trigger.handler.apply(this, [event, element, api]);
			this.fireEvent('trigger', [trigger, element, event]);
		},

		_eventHandler: function(event, target){
			var triggers = target.getTriggers();
			if (triggers.contains('Stop')) event.stop();
			if (triggers.contains('PreventDefault')) event.preventDefault();
			triggers.each(function(trigger){
				if (trigger != "Stop" && trigger != "PreventDefault") this.trigger(trigger, target, event);
			}, this);
		},

		_onRegister: function(eventTypes){
			eventTypes.each(function(eventType){
				if (!this._eventTypes.contains(eventType)){
					this._attachedTo.each(function(element){
						element.addEvent(eventType + ':relay([data-trigger])', this._bound.eventHandler);
					}, this);
				}
				this._eventTypes.include(eventType);
			}, this);
		},

		_attachedTo: [],
		_eventTypes: [],
		_triggers: {}

	});

	Delegator._triggers = {};
	Delegator._instances = [];
	Delegator._onRegister = function(eventType){
		this._instances.each(function(instance){
			instance._onRegister(eventType);
		});
	};

	Delegator.register = function(eventTypes, name, handler, overwrite /** or eventType, obj, overwrite */){
		eventTypes = Array.from(eventTypes);
		if (typeOf(name) == "object"){
			var obj = name;
			for (name in obj){
				this.register.apply(this, [eventTypes, name, obj[name], handler]);
			}
			return this;
		}
		if (!this._triggers[name] || overwrite){
			if (typeOf(handler) == "function"){
				handler = {
					handler: handler
				};
			}
			handler.types = eventTypes;
			handler.name = name;
			this._triggers[name] = handler;
			this._onRegister(eventTypes);
		} else {
			throw new Error('Could add the trigger "' + name  +'" as a previous trigger by that same name exists.');
		}
		return this;
	};

	Delegator.implement('register', Delegator.register);

	Element.implement({

		addTrigger: function(name){
			return this.setData('trigger', this.getTriggers().include(name).join(' '));
		},

		removeTrigger: function(name){
			return this.setData('trigger', this.getTriggers().erase(name).join(' '));
		},

		getTriggers: function(){
			var triggers = this.getData('trigger');
			if (!triggers) return [];
			return triggers.trim().split(spaceOrCommaRegex);
		},

		hasTrigger: function(name){
			return this.getTriggers().contains(name);
		}

	});

})();

// Begin: Source/Delegators/Delegator.CheckAllOrNone.js
/*
---
description: Checks all or none of a group of checkboxes.
provides: [Delegator.CheckAllOrNone]
requires: [Behavior/Delegator]
script: Delegator.CheckAllOrNone.js
name: Delegator.CheckAllOrNone

...
*/

Delegator.register('click', {

	'checkAll': {
		require: ['targets'],
		handler: function(event, link, api){
			var targets = link.getElements(api.get('targets'));
			if (targets.length) targets.set('checked', true);
			else api.warn('There were no inputs found to check.');
		}
	},

	'checkNone': {
		require: ['targets'],
		handler: function(event, link, api){
			var targets = link.getElements(api.get('targets'));
			if (targets.length) targets.set('checked', false);
			else api.warn('There were no inputs found to uncheck.');
		}
	}

});

// Begin: Source/Delegators/Delegator.SubmitLink.js
/*
---
description: When the user clicks a link with this delegator, submit the target form.
provides: [Delegator.SubmitLink]
requires: [Behavior/Delegator]
script: Delegator.SubmitLink.js
name: Delegator.SubmitLink

...
*/

(function(){

	var injectValues = function(form, data){
		var injected = new Elements();
		Object.each(data, function(value, key){
			if (typeOf(value) == 'array'){
				value.each(function(val){
					injected.push(
						new Element('input', {
							type: 'hidden',
							name: key,
							value: val
						}).inject(form)
					);
				});
			} else {
				new Element('input', {
					type: 'hidden',
					name: key,
					value: value
				}).inject(form);
			}
		});
		return injected;
	};

	Delegator.register('click', {

		'submitLink': function(event, el, api){
			var formSelector = api.get('form') || '!form';
			var form = el.getElement(formSelector);
			if (!form) api.fail('Cannot find target form: "' +formSelector+ '" for submitLink delegator.');
			var rq = form.retrieve('form.request');
			var extraData = api.getAs(Object, 'extra-data');
			var injected;
			if (extraData) injected = injectValues(form, extraData);
			if (rq) rq.send();
			else form.submit();
			if (injected) injected.destroy();
		}

	});

})();

// Begin: Source/Delegators/Delegator.FxReveal.js
/*
---
description: Provides methods to reveal, dissolve, nix, and toggle using Fx.Reveal.
provides: [Delegator.FxReveal, Delegator.Reveal, Delegator.ToggleReveal, Delegator.Dissolve, Delegator.Nix]
requires: [Behavior/Delegator, More/Fx.Reveal]
script: Delegator.FxReveal.js
name: Delegator.FxReveal

...
*/
(function(){

	var triggers = {};

	['reveal', 'toggleReveal', 'dissolve', 'nix'].each(function(action){

		triggers[action] = {
			handler: function(event, link, api){
				var target = link;
				if (api.get('target')) {
					target = link.getElement(api.get('target'));
					if (!target) api.fail('could not locate target element to ' + action, link);
				}

				var fxOptions = api.get('fxOptions');
				if (fxOptions) target.set('reveal', fxOptions);
				target.get('reveal');
				if (action == 'toggleReveal') target.get('reveal').toggle();
				else target[action]();
				event.preventDefault();
			}
		};

	});

	Delegator.register('click', triggers);

})();

// Begin: Source/Forms/Behavior.FormValidator.js
/*
---
description: Adds an instance of Form.Validator.Inline to any form with the class .form-validator.
provides: [Behavior.FormValidator]
requires: [Behavior/Behavior, More/Form.Validator.Inline, More/Object.Extras]
script: Behavior.FormValidator.js
name: Behavior.FormValidator
...
*/

Behavior.addGlobalFilter('FormValidator', {
	defaults: {
		useTitles: true,
		scrollToErrorsOnSubmit: true,
		scrollToErrorsOnBlur: false,
		scrollToErrorsOnChange: false,
		ignoreHidden: true,
		ignoreDisabled: true,
		useTitles: false,
		evaluateOnSubmit: true,
		evaluateFieldsOnBlur: true,
		evaluateFieldsOnChange: true,
		serial: true,
		stopOnFailure: true
	},
	setup: function(element, api) {
		//instantiate the form validator
		var validator = element.retrieve('validator');
		if (!validator) {
			validator = new Form.Validator.Inline(element, 
				Object.cleanValues(
					api.getAs({
						useTitles: Boolean,
						scrollToErrorsOnSubmit: Boolean,
						scrollToErrorsOnBlur: Boolean,
						scrollToErrorsOnChange: Boolean,
						ignoreHidden: Boolean,
						ignoreDisabled: Boolean,
						useTitles: Boolean,
						evaluateOnSubmit: Boolean,
						evaluateFieldsOnBlur: Boolean,
						evaluateFieldsOnChange: Boolean,
						serial: Boolean,
						stopOnFailure: Boolean
					})
				)
			);
		}
		//if the api provides a getScroller method, which should return an instance of
		//Fx.Scroll, use it instead
		if (api.getScroller) {
			validator.setOptions({
				onShow: function(input, advice, className) {
					api.getScroller().toElement(input);
				},
				scrollToErrorsOnSubmit: false
			});
		}
		return validator;
	}

});

// Begin: Source/Delegators/Delegator.AddRemoveClass.js
/*
---
description: Provides methods to add/remove/toggle a class on a given target.
provides: [Delegator.ToggleClass, Delegator.AddClass, Delegator.RemoveClass, Delegator.AddRemoveClass]
requires: [Behavior/Delegator, Core/Element]
script: Delegator.AddRemoveClass.js
name: Delegator.AddRemoveClass

...
*/
(function(){

	var triggers = {};

	['add', 'remove', 'toggle'].each(function(action){

		triggers[action + 'Class'] = {
			require: ['class'],
			handler: function(event, link, api){
				var target = link;
				if (api.get('target')) {
					target = link.getElement(api.get('target'));
					if (!target) api.fail('could not locate target element to ' + action + ' its class', link);
				}
				target[action + 'Class'](api.get('class'));
			}
		};

	});

	Delegator.register('click', triggers);

})();

// Begin: Source/Drag/Behavior.Resizable.js
/*
---
description: Creates instances of Drag for resizable elements.
provides: [Behavior.Resizable]
requires: [Behavior/Behavior, More/Drag]
script: Behavior.Resizable.js
name: Behavior.Resizable
...
*/
Behavior.addGlobalFilter('Resizable', {
	//deprecated options
	deprecated: {
		handle: 'resize-handle',
		child: 'resize-child'
	},
	deprecatedAsJSON: {
		modifiers: 'resize-modifiers'
	},
	setup: function(element, api){
		var options = {};
		if (api.get('handle')) options.handle = element.getElement(api.get('handle'));
		if (api.get('modifiers')) options.modifiers = api.getAs(Object, 'modifiers');
		var target = element;
		if (api.get('child')) target = element.getElement(api.get('child'));
		var drag = target.makeResizable(options);
		api.onCleanup(drag.detach.bind(drag));
		return drag;
	}

});


// Begin: Source/Delegators/Delegator.Ajax.js
/*
---
description: Provides functionality for links that load content into a target element via ajax.
provides: [Delegator.Ajax]
requires: [Behavior/Delegator, Core/Request.HTML, More/Spinner]
script: Delegator.Ajax.js
name: Delegator.Ajax
...
*/

(function(){

	Delegator.register('click', 'Ajax', {
		require: ['target'],
		defaults: {
			action: 'injectBottom'
		},
		handler: function(event, link, api){
			var target,
				action = api.get('action'),
				selector = api.get('target');
			if (selector) {
				if (selector == "self") {
					target = element;
				} else {
					target = link.getElement(selector);
				}
			}

			if (!target) api.fail('ajax trigger error: element matching selector %s was not found', selector);

			var requestTarget = new Element('div');

			var spinnerTarget = api.get('spinner-target');
			if (spinnerTarget) spinnerTarget = link.getElement(spinnerTarget);

			event.preventDefault();
			new Request.HTML(
				Object.cleanValues({
					method: 'get',
					evalScripts: api.get('evalScripts'),
					url: api.get('href') || link.get('href'),
					spinnerTarget: spinnerTarget || target,
					useSpinner: api.getAs(Boolean, 'useSpinner'),
					update: requestTarget,
					onSuccess: function(){
						//reverse the elements and inject them
						//reversal is required since it injects each after the target
						//pushing down the previously added element
						var elements = requestTarget.getChildren();
						if (api.get('filter')){
							elements = new Element('div').adopt(elements).getElements(api.get('filter'));
						}
						switch(action){
							case 'replace':
								var container = target.getParent();
								elements.reverse().injectAfter(target);
								api.fireEvent('destroyDom', target);
								target.destroy();
								api.fireEvent('ammendDom', [container, elements]);
								break;
							case 'update':
								api.fireEvent('destroyDom', target.getChildren());
								target.empty();
								elements.inject(target);
								api.fireEvent('ammendDom', [target, elements]);
								break;
							default:
								//injectTop, injectBottom, injectBefore, injectAfter
								if (action == "injectTop" || action == "injectAfter") elements.reverse();
								elements[action](target);
								api.fireEvent('ammendDom', [target, elements]);
						}
					}
				})
			).send();
		}
	});

})();




// Begin: Source/Forms/Behavior.OverText.js
/*
---
description: Sets up an input to have an OverText instance for inline labeling. This is a global filter.
provides: [Behavior.OverText]
requires: [Behavior/Behavior, More/OverText]
script: Behavior.OverText.js
name: Behavior.OverText
...
*/
Behavior.addGlobalFilter('OverText', function(element, api){

	//create the overtext instance
	var ot = new OverText(element);
	if (element.get('class')) {
		element.get('class').split(' ').each(function(cls) {
			if (cls) ot.text.addClass('overText-'+cls);
		});
	}
	element.getBehaviors().each(function(filter){
		if (filter != "OverText") ot.text.addClass('overText-'+filter);
	});

	//this method updates the text position with a slight delay
	var updater = function(){
		ot.reposition.delay(10, ot);
	};

	//update the position whenever the behavior element is shown
	api.addEvent('layout:display', updater);

	api.onCleanup(function(){
		api.removeEvent('layout:display', updater);
		ot.destroy();
	});

	return ot;

});


// Begin: Source/Fx/Behavior.FxAccordion.js
/*
---
description: Creates an Fx.Accordion from any element with Accordion in its data-behavior property.  Uses the .toggle elements within the element as the toggles and the .target elements as the targets. 
provides: [Behavior.Accordion, Behavior.FxAccordion]
requires: [Behavior/Behavior, More/Fx.Accordion, Behavior/Element.Data, More/Object.Extras]
script: Behavior.Accordion.js
name: Behavior.Accordion
...
*/

Behavior.addGlobalFilter('Accordion', {
	deprecated: {
		headers:'toggler-elements',
		sections:'section-elements'
	},
	defaults: {
		// defaults from Fx.Accordion:
		display: 0,
		height: true,
		width: false,
		opacity: true,
		alwaysHide: false,
		trigger: 'click',
		initialDisplayFx: true,
		resetHeight: true,
		headers: '.header',
		sections: '.section'
	},
	setup: function(element, api){
		var options = Object.cleanValues(
			api.getAs({
				fixedHeight: Number,
				fixedWidth: Number,
				display: Number,
				show: Number,
				height: Boolean,
				width: Boolean,
				opacity: Boolean,
				alwaysHide: Boolean,
				trigger: String,
				initialDisplayFx: Boolean,
				resetHeight: Boolean
			})
		);
		var accordion = new Fx.Accordion(element.getElements(api.get('headers')), element.getElements(api.get('sections')), options);
		api.onCleanup(accordion.detach.bind(accordion));
		return accordion;
	}
});

// Begin: Source/Drag/Behavior.Sortable.js
/*
---
description: Creates instances of Sortable for sortable lists, optionally updating an input or element with the sort state.
provides: [Behavior.Sortable]
requires: [Behavior/Behavior, More/Sortables, More/Scroller]
script: Behavior.Sortable.js
name: Behavior.Sortable
...
*/
(function(){

Behavior.addGlobalFilter('Sortable',  {

	defaults: {
		clone: true,
		opacity: 0.6
	},
	deprecated: {
		lists: 'sort-lists',
		state: 'sort-state',
		property: 'sort-property',
		'property-child': 'property-child'
	},
	setup: function(element, api){
		//get the list selector
		var lists = api.get('lists');
		//if present, get the elements
		if (lists) lists = element.getElements(lists);
		//else the target element is the list
		else lists = element;
		//get the state target; this is the element (typically an input) to put the new state value on change
		var target = api.get('state');
		if (target) target = element.getParent().getElement(target);

		//get the property to read from each sorted element
		var property = api.get('property');
		//if the value is on a child element, a selector is specified to find it (see docs)
		var property_child = api.get('property-child');
	
		var scrollParent;
		var sort = new Sortables(lists, {
			clone: api.getAs(Boolean, 'clone'),
			opacity: api.getAs(Number, 'opacity'),
			onStart: function(element, clone){
				clone.addClass('clone');
				var scroller,
				    scrollElement = isScrollable(element) ? element : getScrollParent(element);
				if (scrollElement && scrollElement != scrollParent) {
					scroller = new Scroller(scrollElement);
					scrollElement.store('behavior:scroller', scroller);
					scrollParent = scrollElement;
				} else {
					if (scrollParent) scroller = scrollParent.retrieve('behavior:scroller');
				}
				if (scroller) scroller.attach();
			},
			onComplete: function(){
				if (target) {
					target.set(target.get('tag') == 'input' ? 'value' : 'html', sort.serialize(function(item){
						if (property_child) item = item.getElement(property_child);
						var isInput = ['input', 'textarea', 'select'].contains(item.get('tag'));
						return item.get(property || 'name') || isInput ? item.get('value') : item.get('id');
					}).join(','));
				}
				if (scrollParent) scrollParent.retrieve('behavior:scroller').detach();
			}
		});
		api.onCleanup(sort.detach.bind(sort));
		return sort;
	}

});

var isBody = function(element){
	return (/^(?:body|html)$/i).test(element.tagName);
};

var isScrollable = function(element){
	return ['scroll', 'auto'].contains(element.getStyle('overflow'));
};

var getScrollParent = function(element){
	var scrollParent,
	    parent = element.getParent();
	while (!scrollParent){
		if (isBody(parent) || isScrollable(parent)){
			scrollParent = parent;
		} else {
			parent = parent.getParent();
		}
	}
	return scrollParent;
};

})();

// Begin: Source/Layout/TabSwapper.js
/*
---

name: TabSwapper

description: Handles the scripting for a common UI layout; the tabbed box.

license: MIT-Style License

requires: [Core/Element.Event, Core/Fx.Tween, Core/Fx.Morph, Core/Element.Dimensions, More/Element.Shortcuts, More/Element.Measure]

provides: TabSwapper

...
*/
var TabSwapper = new Class({
	Implements: [Options, Events],
	options: {
		// initPanel: null,
		// smooth: false,
		// smoothSize: false,
		// maxSize: null,
		// onActive: function(){},
		// onActiveAfterFx: function(){},
		// onBackground: function(){}
		// cookieName: null,
		selectedClass: 'tabSelected',
		mouseoverClass: 'tabOver',
		deselectedClass: '',
		rearrangeDOM: true,
		effectOptions: {
			duration: 500
		},
		cookieDays: 999
	},
	tabs: [],
	sections: [],
	clickers: [],
	sectionFx: [],
	initialize: function(options){
		this.setOptions(options);
		var prev = this.setup();
		if (prev) return prev;
		if (this.options.initPanel != null) this.show(this.options.initPanel);
		else if (this.options.cookieName && this.recall()) this.show(this.recall().toInt());
		else this.show(0);

	},
	setup: function(){
		var opt = this.options,
		    sections = $$(opt.sections),
		    tabs = $$(opt.tabs);
		if (tabs[0] && tabs[0].retrieve('tabSwapper')) return tabs[0].retrieve('tabSwapper');
		var clickers = $$(opt.clickers);
		tabs.each(function(tab, index){
			this.addTab(tab, sections[index], clickers[index], index);
		}, this);
	},
	addTab: function(tab, section, clicker, index){
		tab = document.id(tab); clicker = document.id(clicker); section = document.id(section);
		//if the tab is already in the interface, just move it
		if (this.tabs.indexOf(tab) >= 0 && tab.retrieve('tabbered')
			 && this.tabs.indexOf(tab) != index && this.options.rearrangeDOM) {
			this.moveTab(this.tabs.indexOf(tab), index);
			return this;
		}
		//if the index isn't specified, put the tab at the end
		if (index == null) index = this.tabs.length;
		//if this isn't the first item, and there's a tab
		//already in the interface at the index 1 less than this
		//insert this after that one
		if (index > 0 && this.tabs[index-1] && this.options.rearrangeDOM) {
			tab.inject(this.tabs[index-1], 'after');
			section.inject(this.tabs[index-1].retrieve('section'), 'after');
		}
		this.tabs.splice(index, 0, tab);
		clicker = clicker || tab;

		tab.addEvents({
			mouseout: function(){
				tab.removeClass(this.options.mouseoverClass);
			}.bind(this),
			mouseover: function(){
				tab.addClass(this.options.mouseoverClass);
			}.bind(this)
		});

		clicker.addEvent('click', function(e){
			e.preventDefault();
			this.show(index);
		}.bind(this));

		tab.store('tabbered', true);
		tab.store('section', section);
		tab.store('clicker', clicker);
		this.hideSection(index);
		return this;
	},
	removeTab: function(index){
		var now = this.tabs[this.now];
		if (this.now == index){
			if (index > 0) this.show(index - 1);
			else if (index < this.tabs.length) this.show(index + 1);
		}
		this.now = this.tabs.indexOf(now);
		return this;
	},
	moveTab: function(from, to){
		var tab = this.tabs[from];
		var clicker = tab.retrieve('clicker');
		var section = tab.retrieve('section');

		var toTab = this.tabs[to];
		var toClicker = toTab.retrieve('clicker');
		var toSection = toTab.retrieve('section');

		this.tabs.erase(tab).splice(to, 0, tab);

		tab.inject(toTab, 'before');
		clicker.inject(toClicker, 'before');
		section.inject(toSection, 'before');
		return this;
	},
	show: function(i){
		if (this.now == null) {
			this.tabs.each(function(tab, idx){
				if (i != idx)
					this.hideSection(idx);
			}, this);
		}
		this.showSection(i).save(i);
		return this;
	},
	save: function(index){
		if (this.options.cookieName)
			Cookie.write(this.options.cookieName, index, {duration:this.options.cookieDays});
		return this;
	},
	recall: function(){
		return (this.options.cookieName) ? Cookie.read(this.options.cookieName) : false;
	},
	hideSection: function(idx) {
		var tab = this.tabs[idx];
		if (!tab) return this;
		var sect = tab.retrieve('section');
		if (!sect) return this;
		if (sect.getStyle('display') != 'none') {
			this.lastHeight = sect.getSize().y;
			sect.setStyle('display', 'none');
			tab.swapClass(this.options.selectedClass, this.options.deselectedClass);
			this.fireEvent('onBackground', [idx, sect, tab]);
		}
		return this;
	},
	showSection: function(idx) {
		var tab = this.tabs[idx];
		if (!tab) return this;
		var sect = tab.retrieve('section');
		if (!sect) return this;
		var smoothOk = this.options.smooth && !Browser.ie;
		if (this.now != idx) {
			if (!tab.retrieve('tabFx'))
				tab.store('tabFx', new Fx.Morph(sect, this.options.effectOptions));
			var overflow = sect.getStyle('overflow');
			var start = {
				display:'block',
				overflow: 'hidden'
			};
			if (smoothOk) start.opacity = 0;
			var effect = false;
			if (smoothOk) {
				effect = {opacity: 1};
			} else if (sect.getStyle('opacity').toInt() < 1) {
				sect.setStyle('opacity', 1);
				if (!this.options.smoothSize) this.fireEvent('onActiveAfterFx', [idx, sect, tab]);
			}
			if (this.options.smoothSize) {
				var size = sect.getDimensions().height;
				if (this.options.maxSize != null && this.options.maxSize < size)
					size = this.options.maxSize;
				if (!effect) effect = {};
				effect.height = size;
			}
			if (this.now != null) this.hideSection(this.now);
			if (this.options.smoothSize && this.lastHeight) start.height = this.lastHeight;
			sect.setStyles(start);
			var finish = function(){
				this.fireEvent('onActiveAfterFx', [idx, sect, tab]);
				sect.setStyles({
					height: this.options.maxSize == effect.height ? this.options.maxSize : "auto",
					overflow: overflow
				});
				sect.getElements('input, textarea').setStyle('opacity', 1);
			}.bind(this);
			if (effect) {
				tab.retrieve('tabFx').start(effect).chain(finish);
			} else {
				finish();
			}
			this.now = idx;
			this.fireEvent('onActive', [idx, sect, tab]);
		}
		tab.swapClass(this.options.deselectedClass, this.options.selectedClass);
		return this;
	}
});


// Begin: Source/Behaviors/Behavior.Tabs.js
/*
---
name: Behavior.Tabs
description: Adds a tab interface (TabSwapper instance) for elements with .css-tab_ui. Matched with tab elements that are .tabs and sections that are .tab_sections.
provides: [Behavior.Tabs]
requires: [Behavior/Behavior, /TabSwapper, More/String.QueryString]
script: Behavior.Tabs.js

...
*/

Behavior.addGlobalFilters({

	Tabs: {
		defaults: {
			'tabs-selector': '.tabs>li',
			'sections-selector': '.tab_sections>li',
			smooth: true,
			smoothSize: true,
			rearrangeDOM: false
		},
		setup: function(element, api) {
			var tabs = element.getElements(api.get('tabs-selector'));
			var sections = element.getElements(api.get('sections-selector'));
			if (tabs.length != sections.length || tabs.length == 0) {
				api.fail('warning; sections and sections are not of equal number. tabs: %o, sections: %o', tabs, sections);
			}
			var getHash = function(){
				return window.location.hash.substring(1, window.location.hash.length).parseQueryString();
			};

			var ts = new TabSwapper(
				Object.merge(
					{
						tabs: tabs,
						sections: sections,
						initPanel: api.get('hash') ? getHash()[api.get('hash')] : null
					},
					Object.cleanValues(
						api.getAs({
							smooth: Boolean,
							smoothSize: Boolean,
							rearrangeDOM: Boolean,
							selectedClass: String,
							initPanel: Number
						})
					)
				)
			);
			ts.addEvent('active', function(index){
				if (api.get('hash')) {
					var hash = getHash();
					hash[api.get('hash')] = index;
					window.location.hash = Object.cleanValues(Object.toQueryString(hash));
				}
				api.fireEvent('layout:display', sections[0].getParent());
			});
			element.store('TabSwapper', ts);
			return ts;
		}
	}
});

var Observer = new Class({

	Implements: [Options, Events],

	options: {
		periodical: false,
		delay: 1000
	},

	initialize: function(el, onFired, options){
		this.setOptions(options);
		this.addEvent('onFired', onFired);
		this.element = document.id(el) || $$(el);
		/* Clientcide change */
		this.boundChange = this.changed.bind(this);
		this.resume();
	},

	changed: function() {
		var value = this.element.get('value');
		if ($equals(this.value, value)) return;
		this.clear();
		this.value = value;
		this.timeout = this.onFired.delay(this.options.delay, this);
	},

	setValue: function(value) {
		this.value = value;
		this.element.set('value', value);
		return this.clear();
	},

	onFired: function() {
		this.fireEvent('onFired', [this.value, this.element]);
	},

	clear: function() {
		clearTimeout(this.timeout || null);
		return this;
	},
	/* Clientcide change */
	pause: function(){
		clearTimeout(this.timeout);
		clearTimeout(this.timer);
		this.element.removeEvent('keyup', this.boundChange);
		return this;
	},
	resume: function(){
		this.value = this.element.get('value');
		if (this.options.periodical) this.timer = this.changed.periodical(this.options.periodical, this);
		else this.element.addEvent('keyup', this.boundChange);
		return this;
	}

});

var $equals = function(obj1, obj2) {
	return (obj1 == obj2 || JSON.encode(obj1) == JSON.encode(obj2));
};

//Begin: Source/3rdParty/Autocompleter.js
/*
---
name: Autocompleter

description: An auto completer class from <a href=\"http://digitarald.de\">http://digitarald.de</a>.

version: 1.1.1

license: MIT-style license

author: Harald Kirschner <mail [at] digitarald.de>

copyright: Author

requires: [Core/Fx.Tween, More/Element.Shortcuts, More/Element.Forms, More/IframeShim, Observer, Clientcide]

provides: [Autocompleter, Autocompleter.Base]

...
*/
var Autocompleter = {};

var OverlayFix = IframeShim;

Autocompleter.Base = new Class({

	Implements: [Options, Events],

	options: {
		minLength: 1,
		markQuery: true,
		width: 'inherit',
		maxChoices: 10,
//		injectChoice: null,
//		customChoices: null,
		className: 'autocompleter-choices',
		zIndex: 42,
		delay: 400,
		observerOptions: {},
		fxOptions: {},
//		onSelection: function(){},
//		onShow: function(){},
//		onHide: function(){},
//		onBlur: function(){},
//		onFocus: function(){},
//		onChoiceConfirm: function(){},

		autoSubmit: false,
		overflow: false,
		overflowMargin: 25,
		selectFirst: false,
		filter: null,
		filterCase: false,
		filterSubset: false,
		forceSelect: false,
		selectMode: true,
		choicesMatch: null,

		multiple: false,
		separator: ', ',
		autoTrim: true,
		allowDupes: false,

		cache: true,
		relative: false
	},

	initialize: function(element, options) {
		this.element = document.id(element);
		this.setOptions(options);
		this.options.separatorSplit = new RegExp("\s*["+
		  this.options.separator == " " ? " " : this.options.separator.trim()+
		  "]\s*/");
		this.build();
		this.observer = new Observer(this.element, this.prefetch.bind(this), Object.merge({
			'delay': this.options.delay
		}, this.options.observerOptions));
		this.queryValue = null;
		if (this.options.filter) this.filter = this.options.filter.bind(this);
		var mode = this.options.selectMode;
		this.typeAhead = (mode == 'type-ahead');
		this.selectMode = (mode === true) ? 'selection' : mode;
		this.cached = [];
	},

	/**
	 * build - Initialize DOM
	 *
	 * Builds the html structure for choices and appends the events to the element.
	 * Override this function to modify the html generation.
	 */
	build: function() {
		if (document.id(this.options.customChoices)) {
			this.choices = this.options.customChoices;
		} else {
			this.choices = new Element('ul', {
				'class': this.options.className,
				'styles': {
					'zIndex': this.options.zIndex
				}
			}).inject(document.body);
			this.relative = false;
			if (this.options.relative || this.element.getOffsetParent() != document.body) {
				this.choices.inject(this.element, 'after');
				this.relative = this.element.getOffsetParent();
			}
			this.fix = new OverlayFix(this.choices);
		}
		if (!this.options.separator.test(this.options.separatorSplit)) {
			this.options.separatorSplit = this.options.separator;
		}
		this.fx = (!this.options.fxOptions) ? null : new Fx.Tween(this.choices, Object.merge({
			'property': 'opacity',
			'link': 'cancel',
			'duration': 200
		}, this.options.fxOptions)).addEvent('onStart', Chain.prototype.clearChain).set(0);
		this.element.setProperty('autocomplete', 'off')
			.addEvent((Browser.ie || Browser.chrome || Browser.safari) ? 'keydown' : 'keypress', this.onCommand.bind(this))
			.addEvent('click', this.onCommand.bind(this, false))
			.addEvent('focus', function(){
				this.toggleFocus.delay(100, this, [true]);
			}.bind(this));
			//.addEvent('blur', this.toggleFocus.create({bind: this, arguments: false, delay: 100}));
		document.addEvent('click', function(e){
			if (e.target != this.choices) this.toggleFocus(false);
		}.bind(this));
	},

	destroy: function() {
		if (this.fix) this.fix.dispose();
		this.choices = this.selected = this.choices.destroy();
	},

	toggleFocus: function(state) {
		this.focussed = state;
		if (!state) this.hideChoices(true);
		this.fireEvent((state) ? 'onFocus' : 'onBlur', [this.element]);
	},

	onCommand: function(e) {
		if (!e && this.focussed) return this.prefetch();
		if (e && e.key && !e.shift) {
			switch (e.key) {
				case 'enter': case 'tab':
					if (this.element.value != this.opted) return true;
					if (this.selected && this.visible) {
						this.choiceSelect(this.selected);
						this.fireEvent('choiceConfirm', this.selected);
						return !!(this.options.autoSubmit);
					}
					break;
				case 'up': case 'down':
					if (!this.prefetch() && this.queryValue !== null) {
						var up = (e.key == 'up');
						this.choiceOver((this.selected || this.choices)[
							(this.selected) ? ((up) ? 'getPrevious' : 'getNext') : ((up) ? 'getLast' : 'getFirst')
						](this.options.choicesMatch), true);
					}
					return false;
				case 'esc':
					this.hideChoices(true);
					break;
			}
		}
		return true;
	},

	setSelection: function(finish) {
		var input = this.selected.inputValue, value = input;
		var start = this.queryValue.length, end = input.length;
		if (input.substr(0, start).toLowerCase() != this.queryValue.toLowerCase()) start = 0;
		if (this.options.multiple) {
			var split = this.options.separatorSplit;
			value = this.element.value;
			start += this.queryIndex;
			end += this.queryIndex;
			var old = value.substr(this.queryIndex).split(split, 1)[0];
			value = value.substr(0, this.queryIndex) + input + value.substr(this.queryIndex + old.length);
			if (finish) {
				var space = /[^\s,]+/;
				var tokens = value.split(this.options.separatorSplit).filter(space.test, space);
				if (!this.options.allowDupes) tokens = [].combine(tokens);
				var sep = this.options.separator;
				value = tokens.join(sep) + sep;
				end = value.length;
			}
		}
		this.observer.setValue(value);
		this.opted = value;
		if (finish || this.selectMode == 'pick') start = end;
		this.element.selectRange(start, end);
		this.fireEvent('onSelection', [this.element, this.selected, value, input]);
	},

	showChoices: function() {
		var match = this.options.choicesMatch, first = this.choices.getFirst(match);
		this.selected = this.selectedValue = null;
		if (this.fix) {
			var pos = this.element.getCoordinates(this.relative), width = this.options.width || 'auto';
			this.choices.setStyles({
				'left': pos.left,
				'top': pos.bottom,
				'width': (width === true || width == 'inherit') ? pos.width : width
			});
		}
		if (!first) return;
		if (!this.visible) {
			this.visible = true;
			this.choices.setStyle('display', '');
			if (this.fx) this.fx.start(1);
			this.fireEvent('onShow', [this.element, this.choices]);
		}
		if (this.options.selectFirst || this.typeAhead || first.inputValue == this.queryValue) this.choiceOver(first, this.typeAhead);
		var items = this.choices.getChildren(match), max = this.options.maxChoices;
		var styles = {'overflowY': 'hidden', 'height': ''};
		this.overflown = false;
		if (items.length > max) {
			var item = items[max - 1];
			styles.overflowY = 'scroll';
			styles.height = item.getCoordinates(this.choices).bottom;
			this.overflown = true;
		};
		this.choices.setStyles(styles);
		if (this.fix){
			this.fix.show();
		}
	},

	hideChoices: function(clear) {
		if (clear) {
			var value = this.element.value;
			if (this.options.forceSelect) value = this.opted;
			if (this.options.autoTrim) {
				value = value.split(this.options.separatorSplit).filter(function(){ return arguments[0]; }).join(this.options.separator);
			}
			this.observer.setValue(value);
		}
		if (!this.visible) return;
		this.visible = false;
		this.observer.clear();
		var hide = function(){
			this.choices.setStyle('display', 'none');
			if (this.fix){
				this.fix.hide();
			}
		}.bind(this);
		if (this.fx) this.fx.start(0).chain(hide);
		else hide();
		this.fireEvent('onHide', [this.element, this.choices]);
	},

	prefetch: function() {
		var value = this.element.value, query = value;
		if (this.options.multiple) {
			var split = this.options.separatorSplit;
			var values = value.split(split);
			var index = this.element.getCaretPosition();
			var toIndex = value.substr(0, index).split(split);
			var last = toIndex.length - 1;
			index -= toIndex[last].length;
			query = values[last];
		}
		if (query.length < this.options.minLength) {
			this.hideChoices();
		} else {
			if (query === this.queryValue || (this.visible && query == this.selectedValue)) {
				if (this.visible) return false;
				this.showChoices();
			} else {
				this.queryValue = query;
				this.queryIndex = index;
				if (!this.fetchCached()) this.query();
			}
		}
		return true;
	},

	fetchCached: function() {
		if (!this.options.cache
			|| !this.cached
			|| !this.cached.length
			|| this.cached.length >= this.options.maxChoices
			|| this.queryValue) return false;
		this.update(this.filter(this.cached));
		return true;
	},

	update: function(tokens) {
		this.choices.empty();
		this.cached = tokens;
		if (!tokens || !tokens.length) {
			this.hideChoices();
		} else {
			if (this.options.maxChoices < tokens.length && !this.options.overflow) tokens.length = this.options.maxChoices;
			tokens.each(this.options.injectChoice || function(token){
				var choice = new Element('li', {'html': this.markQueryValue(token)});
				choice.inputValue = token;
				this.addChoiceEvents(choice).inject(this.choices);
			}, this);
			this.showChoices();
		}
	},

	choiceOver: function(choice, selection) {
		if (!choice || choice == this.selected) return;
		if (this.selected) this.selected.removeClass('autocompleter-selected');
		this.selected = choice.addClass('autocompleter-selected');
		this.fireEvent('onSelect', [this.element, this.selected, selection]);
		if (!selection) return;
		this.selectedValue = this.selected.inputValue;
		if (this.overflown) {
			var coords = this.selected.getCoordinates(this.choices), margin = this.options.overflowMargin,
				top = this.choices.scrollTop, height = this.choices.offsetHeight, bottom = top + height;
			if (coords.top - margin < top && top) this.choices.scrollTop = Math.max(coords.top - margin, 0);
			else if (coords.bottom + margin > bottom) this.choices.scrollTop = Math.min(coords.bottom - height + margin, bottom);
		}
		if (this.selectMode) this.setSelection();
	},

	choiceSelect: function(choice) {
		if (choice) this.choiceOver(choice);
		this.setSelection(true);
		this.queryValue = false;
		this.hideChoices();
	},

	filter: function(tokens) {
		return (tokens || this.tokens).filter(function(token) {
			return this.test(token);
		}, new RegExp(((this.options.filterSubset) ? '' : '^') + this.queryValue.escapeRegExp(), (this.options.filterCase) ? '' : 'i'));
	},

	/**
	 * markQueryValue
	 *
	 * Marks the queried word in the given string with <span class="autocompleter-queried">*</span>
	 * Call this i.e. from your custom parseChoices, same for addChoiceEvents
	 *
	 * @param		{String} Text
	 * @return		{String} Text
	 */
	markQueryValue: function(str) {
		if (!this.options.markQuery || !this.queryValue) return str;
		var regex = new RegExp('(' + ((this.options.filterSubset) ? '' : '^') + this.queryValue.escapeRegExp() + ')', (this.options.filterCase) ? '' : 'i');
		return str.replace(regex, '<span class="autocompleter-queried">$1</span>');
	},

	/**
	 * addChoiceEvents
	 *
	 * Appends the needed event handlers for a choice-entry to the given element.
	 *
	 * @param		{Element} Choice entry
	 * @return		{Element} Choice entry
	 */
	addChoiceEvents: function(el) {
		return el.addEvents({
			'mouseover': this.choiceOver.bind(this, el),
			'click': function(){
				var result = this.choiceSelect(el);
				this.fireEvent('choiceConfirm', this.selected);
				return result;
			}.bind(this)
		});
	}
});


// Begin: Source/3rdParty/Autocompleter.Local.js
/*
---
name: Autocompleter.Local

description: Allows Autocompleter to use an object in memory for autocompletion (instead of retrieving via ajax).

version: 1.1.1

license: MIT-style license
author: Harald Kirschner <mail [at] digitarald.de>
copyright: Author

requires: [Autocompleter.Base]

provides: [Autocompleter.Local]
...
 */
Autocompleter.Local = new Class({

	Extends: Autocompleter.Base,

	options: {
		minLength: 0,
		delay: 200
	},

	initialize: function(element, tokens, options) {
		this.parent(element, options);
		this.tokens = tokens;
	},

	query: function() {
		this.update(this.filter());
	}

});


// Begin: Source/3rdParty/Autocompleter.Remote.js
/*
---
name: Autocompleter.Remote

version: 1.1.1

description: Autocompleter extensions that enable requests for JSON/XHTML data for input suggestions.

license: MIT-style license
author: Harald Kirschner <mail [at] digitarald.de>
copyright: Author

requires: [Autocompleter.Base, Core/Request.HTML, Core/Request.JSON]

provides: [Autocompleter.Remote, Autocompleter.Ajax, Autocompleter.Ajax.Base, Autocompleter.Ajax.Json, Autocompleter.Ajax.Xhtml]

...
 */

Autocompleter.Ajax = {};

Autocompleter.Ajax.Base = new Class({

	Extends: Autocompleter.Base,

	options: {
		// onRequest: function(){},
		// onComplete: function(){},
		postVar: 'value',
		postData: {},
		ajaxOptions: {}
	},

	initialize: function(element, options) {
		this.parent(element, options);
		var indicator = document.id(this.options.indicator);
		if (indicator) {
			this.addEvents({
				'onRequest': indicator.show.bind(indicator),
				'onComplete': indicator.hide.bind(indicator)
			}, true);
		}
	},

	query: function(){
		var data = Object.clone(this.options.postData);
		data[this.options.postVar] = this.queryValue;
		this.fireEvent('onRequest', [this.element, this.request, data, this.queryValue]);
		this.request.send({'data': data});
	},

	/**
	 * queryResponse - abstract
	 *
	 * Inherated classes have to extend this function and use this.parent(resp)
	 *
	 * @param		{String} Response
	 */
	queryResponse: function() {
		this.fireEvent('onComplete', [this.element, this.request, this.response]);
	}

});

Autocompleter.Ajax.Json = new Class({

	Extends: Autocompleter.Ajax.Base,

	initialize: function(el, url, options) {
		this.parent(el, options);
		this.request = new Request.JSON(Object.merge({
			'url': url,
			'link': 'cancel'
		}, this.options.ajaxOptions)).addEvent('onComplete', this.queryResponse.bind(this));
	},

	queryResponse: function(response) {
		this.parent();
		this.update(response);
	}

});

Autocompleter.Ajax.Xhtml = new Class({

	Extends: Autocompleter.Ajax.Base,

	initialize: function(el, url, options) {
		this.parent(el, options);
		this.request = new Request.HTML(Object.merge({
			'url': url,
			'link': 'cancel',
			'update': this.choices
		}, this.options.ajaxOptions)).addEvent('onComplete', this.queryResponse.bind(this));
	},

	queryResponse: function(tree, elements) {
		this.parent();
		if (!elements || !elements.length) {
			this.hideChoices();
		} else {
			this.choices.getChildren(this.options.choicesMatch).each(this.options.injectChoice || function(choice) {
				var value = choice.innerHTML;
				choice.inputValue = value;
				this.addChoiceEvents(choice.set('html', this.markQueryValue(value)));
			}, this);
			this.showChoices();
		}

	}

});


// Begin: Source/Behaviors/Behavior.Autocompleter.js
/*
---
name: Behavior.Autocompleter
description: Adds support for Autocompletion on form inputs.
provides: [Behavior.Autocomplete, Behavior.Autocompleter]
requires: [Behavior/Behavior, /Autocompleter.Local, /Autocompleter.Remote]
script: Behavior.Autocomplete.js

...
*/

Behavior.addGlobalFilters({

	/*
		takes elements (inputs) with the data filter "Autocomplete" and creates a autocompletion ui for them
		that either completes against a list of terms supplied as a property of the element (dtaa-autocomplete-tokens)
		or fetches them from a server. In both cases, the tokens must be an array of values. Example:

		<input data-behavior="Autocomplete" data-autocomplete-tokens="['foo', 'bar', 'baz']"/>

		Alternately, you can specify a url to submit the current typed token to get back a list of valid values in the
		same format (i.e. a JSON response; an array of strings). Example:

		<input data-behavior="Autocomplete" data-autocomplete-url="/some/API/for/autocomplete"/>

		When the values ar fetched from the server, the server is sent the current term (what the user is typing) as
		a post variable "term" as well as the entire contents of the input as "value".

		An additional data property for autocomplete-options can be specified; this must be a JSON encoded string
		of key/value pairs that configure the Autocompleter instance (see documentation for the Autocompleter classes
		online at http://www.clientcide.com/docs/3rdParty/Autocompleter but also available as a markdown file in the
		clientcide repo fetched by hue in the thirdparty directory).

		Note that this JSON string can't include functions as callbacks; if you require amore advanced usage you should
		write your own Behavior filter or filter plugin.

	*/

	Autocomplete: {
		defaults: {
			minLength: 1,
			selectMode: 'type-ahead',
			overflow: true,
			selectFirst: true,
			multiple: true,
			separator: ' ',
			allowDupes: true,
			postVar: 'term'
		},
		setup: function(element, api){
			var options = Object.cleanValues(
				api.getAs({
					minLength: Number,
					selectMode: String,
					overflow: Boolean,
					selectFirst: Boolean,
					multiple: Boolean,
					separator: String,
					allowDupes: Boolean,
					postVar: String
				})
			);

			if (element.getData('autocomplete-url')) {
				var aaj = new Autocompleter.Ajax.Json(element, element.getData('autocomplete-url'), options);
				aaj.addEvent('request', function(el, req, data, value){
					data['value'] = el.get('value');
				});
				return aaj;
			} else {
				var tokens = api.getAs(Array, 'tokens');
				if (!tokens) {
					dbug.warn('Could not set up autocompleter; no local tokens found.');
					return;
				}
				return new Autocompleter.Local(element, tokens, options);
			}
		}
	}

});