/*
---

description: A plugin for enabling autocomplete of a text input or textarea.

authors:
 - Fábio Miranda Costa

requires:
 - Core/Class.Extras
 - Core/Element.Event
 - Core/Element.Style
 - More/Element.Forms

license: MIT-style license

provides: [Meio.Autocomplete]

...
*/

(function(global, $){

	var browserEngine = Browser.Engine; // better compression and faster

	// Custom Events

	// thanks Jan Kassens
	$extend(Element.NativeEvents, {
		'paste': 2, 'input': 2
	});
	Element.Events.paste = {
		base : (browserEngine.presto || (browserEngine.gecko && browserEngine.version < 19)) ? 'input' : 'paste',
		condition: function(e){
			this.fireEvent('paste', e, 1);
			return false;
		}
	};
	
	// the key event that repeats
	Element.Events.keyrepeat = {
		base : (browserEngine.gecko || browserEngine.presto) ? 'keypress' : 'keydown',
		condition: $lambda(true)
	};
	
	// Autocomplete itself

	var Meio = global.Meio || {};
	var globalCache;
	
	var keysThatDontChangeValueOnKeyUp = {
		9:   1,  // tab
		16:  1,  // shift
		17:  1,  // control
		18:  1,  // alt
		224: 1,  // command (meta onkeypress)
		91:  1,  // command (meta onkeydown)
		37:  1,  // left
		38:  1,  // up
		39:  1,  // right
		40:  1   // down
	}; 
	
	var encode = function(str){
		return str.replace(/"/g, '&quot;').replace(/'/g, '&#39;');
	};
	
	Meio.Widget = new Class({
		
		initialize: function(){
			this.elements = {};
		},
		
		addElement: function(name, obj){
			this.elements[name] = obj;
		},
		
		addEventToElement: function(name, eventName, event){
			this.elements[name].addEvent(eventName, event.bindWithEvent(this));
		},
		
		addEventsToElement: function(name, events){
			for (var eventName in events){
				this.addEventToElement(name, eventName, events[eventName]);
			}
		},
		
		attach: function(){
			for (var element in this.elements){
				this.elements[element].attach();
			}
		},
		
		detach: function(){
			for (var element in this.elements){
				this.elements[element].detach();
			}
		},
		
		destroy: function(){
			for (var element in this.elements){
				this.elements[element] && this.elements[element].destroy();
			}
		}
	});
	
	Meio.Autocomplete = new Class({
		
		Extends: Meio.Widget,
		
		Implements: [Options, Events],
		
		options: {
			
			delay: 200,
			minChars: 0,
			cacheLength: 20,
			selectOnTab: true,
			maxVisibleItems: 10,
			cacheType: 'shared', // 'shared' or 'own'
			
			filter: {
				/*
					its posible to pass the filters directly or by passing a type and optionaly a path.
					
					filter: function(text, data){}
					formatMatch: function(text, data, i){}
					formatItem: function(text, data){}
					
					or
					
					type: 'startswith' or 'contains' // can be any defined on the Meio.Autocomplete.Filter object
					path: 'a.b.c' // path to the text value on each object thats contained on the data array
				*/
			},
			
			/*
			onNoItemToList: function(elements){},
			onSelect: function(elements, value){},
			onDeselect: function(elements){},
			*/
			
			fieldOptions: {}, // see Element options
			listOptions: {}, // see List options
			requestOptions: {}, // see DataRequest options
			urlOptions: {} // see URL options
			
		},
		
		initialize: function(input, data, options, listInstance){
			this.parent();
			this.setOptions(options);
			this.active = 0;
			
			this.filters = Meio.Autocomplete.Filter.get(this.options.filter);
			
			this.addElement('list', listInstance || new Meio.Element.List(this.options.listOptions));
			this.addListEvents();
			
			this.addElement('field', new Meio.Element.Field(input, this.options.fieldOptions));
			this.addFieldEvents();
			
			this.addSelectEvents();
			
			this.attach();
			this.initCache();
			this.initData(data);
		},
		
		addFieldEvents: function(){
			this.addEventsToElement('field', {
				'beforeKeyrepeat': function(e){
					this.active = 1;
					var e_key = e.key, list = this.elements.list;
					if (e_key == 'up' || e_key == 'down' || (e_key == 'enter' && list.showing)) e.preventDefault();
				},
				'delayedKeyrepeat': function(e){
					var e_key = e.key, field = this.elements.field;
					field.keyPressControl[e_key] = true;
					switch (e_key){
					case 'up': case 'down':
						this.focusItem(e_key);
						break;
					case 'enter':
						this.setInputValue();
						break;
					case 'tab':
						if (this.options.selectOnTab) this.setInputValue();
						field.keyPressControl[e_key] = false; // tab blurs the input so the keyup event wont happen at the same input you made a keydown
						break;
					case 'esc':
						this.elements.list.hide();
						break;
					default:
						this.setupList();
					}
					this.oldInputedText = field.node.get('value');
				},
				'keyup': function(e){
					var field = this.elements.field;
					if (!keysThatDontChangeValueOnKeyUp[e.code]){
						if (!field.keyPressControl[e.key]) this.setupList();
						field.keyPressControl[e.key] = false;
					}
				},
				'focus': function(){
					this.active = 1;
					var list = this.elements.list;
					list.focusedItem = null;
					list.positionNextTo(this.elements.field.node);
				},
				'click': function(){
					if (++this.active > 2 && !this.elements.list.showing){
						this.forceSetupList();
					}
				},
				'blur': function(e){
					this.active = 0;
					var list = this.elements.list;
					if (list.shouldNotBlur){
						this.elements.field.node.setCaretPosition('end');
						list.shouldNotBlur = false;
						if (list.focusedItem) list.hide();
					} else {
						list.hide();
					}
				},
				'paste': function(){
					return this.setupList();
				}
			});
		},
		
		addListEvents: function(){
			this.addEventsToElement('list', {
				'mousedown': function(e){
					if (this.active && !e.dontHide) this.setInputValue();
				}
			});
		},
		
		update: function(){
			var data = this.data, list = this.elements.list;
			var cacheKey = data.getKey(), cached = this.cache.get(cacheKey), html;
			if (cached){
				html = cached.html;
				this.itemsData = cached.data;
			} else {
				data = data.get();
				var itemsHtml = [], itemsData = [], classes = list.options.classes, text = this.inputedText;
				var filter = this.filters.filter, formatMatch = this.filters.formatMatch, formatItem = this.filters.formatItem;
				for (var row, i = 0, n = 0; row = data[i++];) if (filter.call(this, text, row)){
					itemsHtml.push(
						'<li title="', encode(formatMatch.call(this, text, row)),
						'" data-index="', n,
						'" class="', (n%2 ? classes.even : classes.odd), '">',
						formatItem.call(this, text, row, n),
						'</li>'
					);
					itemsData.push(row);
					n++;
				}
				html = itemsHtml.join('');
				this.cache.set(cacheKey, {html: html, data: itemsData});
				this.itemsData = itemsData;
			}
			list.focusedItem = null;
			this.fireEvent('deselect', [this.elements]);
			list.list.set('html', html);
			if (this.options.maxVisibleItems) list.applyMaxHeight(this.options.maxVisibleItems);
		},
		
		setupList: function(){
			this.inputedText = this.elements.field.node.get('value');
			if (this.inputedText !== this.oldInputedText){
				this.forceSetupList(this.inputedText);
			} else {
				this.elements.list.hide();
			}
			return true;
		},
		
		forceSetupList: function(inputedText){
			inputedText = inputedText || this.elements.field.node.get('value');
			if (inputedText.length >= this.options.minChars){
				clearInterval(this.prepareTimer);
				this.prepareTimer = this.data.prepare.delay(this.options.delay, this.data, this.inputedText);
			}
		},
		
		dataReady: function(){
			this.update();
			if (this.onUpdate){
				this.onUpdate();
				this.onUpdate = null;
			}
			var list = this.elements.list;
			if (list.list.get('html')){
				if (this.active) list.show();
			} else {
				this.fireEvent('noItemToList', [this.elements]);
				list.hide();
			}
		},
		
		setInputValue: function(){
			var list = this.elements.list;
			if (list.focusedItem){
				var text = list.focusedItem.get('title');
				this.elements.field.node.set('value', text);
				var index = list.focusedItem.get('data-index');
				this.fireEvent('select', [this.elements, this.itemsData[index], text, index]);
			}
			list.hide();
		},
		
		focusItem: function(direction){
			var list = this.elements.list;
			if (list.showing){
				list.focusItem(direction);
			} else {
				this.forceSetupList();
				this.onUpdate = function(){ list.focusItem(direction); };
			}
		},
		
		addSelectEvents: function(){
			this.addEvents({
				select: function(elements){
					elements.field.addClass('selected');
				},
				deselect: function(elements){
					elements.field.removeClass('selected');
				}
			});
		},
		
		initData: function(data){
			this.data = ($type(data) == 'string') ?
				new Meio.Autocomplete.Data.Request(data, this.cache, this.elements.field, this.options.requestOptions, this.options.urlOptions) :
				new Meio.Autocomplete.Data(data, this.cache);
			this.data.addEvent('ready', this.dataReady.bind(this));
		},
		
		initCache: function(){
			var cacheLength = this.options.cacheLength;
			if (this.options.cacheType == 'shared'){
				this.cache = globalCache;
				this.cache.setMaxLength(cacheLength);
			} else { // 'own'
				this.cache = new Meio.Autocomplete.Cache(cacheLength);
			}
		},
		
		refreshCache: function(cacheLength){
			this.cache.refresh();
			this.cache.setMaxLength(cacheLength || this.options.cacheLength);
		},
		
		refreshAll: function(cacheLength, urlOptions){
			// TODO, do you really need to refresh the url? see a better way of doing this
			this.refreshCache(cacheLength);
			this.data.refreshKey(urlOptions);
		}

	});
	
	// This is the same autocomplete class but it acts like a normal select element.
	// When you select an option from the autocomplete it will set the value of a given element (valueField)
	// with the return of the valueFilter.
	// if the syncAtInit option is set to true, it will synchonize the value of the autocomplete with the corresponding data
	// from the valueField's value.
	// to understand better see the user specs.
	
	Meio.Autocomplete.Select = new Class({
		
		Extends: Meio.Autocomplete,
		
		options: {
			syncName: 'id', // if falsy it wont sync at start
			valueField: null,
			valueFilter: function(data){
				return data.id;
			}
		},
		
		// overwritten
		initialize: function(input, data, options, listInstance){
			this.parent(input, data, options, listInstance);
			this.valueField = $(this.options.valueField);
			
			if (!this.valueField) return;
			
			this.syncWithValueField(data);
		},
		
		syncWithValueField: function(data){
			var value = this.getValueFromValueField();
			
			if (value && this.options.syncName){
				this.addParameter(data);
				this.addDataReadyEvent(value);
				this.data.prepare(this.elements.field.node.get('value'));
			} else {
				this.addValueFieldEvents();
			}
		},
		
		addValueFieldEvents: function(){
			this.addEvents({
				'select': function(elements, data){
					this.valueField.set('value', this.options.valueFilter.call(this, data));
				},
				'deselect': function(elements){
					this.valueField.set('value', '');
				}
			});
		},
		
		addParameter: function(data){
			this.parameter = {
				name: this.options.syncName,
				value: function(){
					return this.valueField.value;
				}.bind(this)
			};
			if (this.data.url) this.data.url.addParameter(this.parameter);
		},
		
		addDataReadyEvent: function(value){
			var self = this;
			var runOnce = function(){
				self.addValueFieldEvents();
				var values = this.get();
				for (var i = values.length; i--;){
					if (self.options.valueFilter.call(self, values[i]) == value){
						var text = self.filters.formatMatch.call(self, '', values[i], 0);
						self.elements.field.node.set('value', text);
						self.fireEvent('select', [self.elements, values[i], text, i]);
						break;
					}
				}
				if (this.url) this.url.removeParameter(self.parameter);
				this.removeEvent('ready', runOnce);
			};
			this.data.addEvent('ready', runOnce);
		},
		
		getValueFromValueField: function(){
			return this.valueField.get('value');
		}
		
	});
	
	// Transforms a select on an autocomplete field
	
	Meio.Autocomplete.Select.One = new Class({
		
		Extends: Meio.Autocomplete.Select,
		
		options: {
			filter: {
				path: 'text' // path to the text value on each object thats contained on the data array
			}
		},
		
		//overwritten
		initialize: function(select, options, listInstance){
			this.select = $(select);
			this.replaceSelect();
			this.parent(this.field, this.createDataArray(), $merge(options, {
				valueField: this.select,
				valueFilter: function(data){ return data.value; }
			}), listInstance);
		},
		
		replaceSelect: function(){
			var selectedOption = this.select.getSelected()[0];
			this.field = new Element('input', {type: 'text'});
			var optionValue = selectedOption.get('value');
			if ($chk(optionValue)) this.field.set('value', selectedOption.get('html'));
			this.select.setStyle('display', 'none');
			this.field.inject(this.select, 'after');
		},
		
		createDataArray: function(){
			var selectOptions = this.select.options, data = [];
			for(var i = 0, selectOption, optionValue; selectOption = selectOptions[i++];){
				optionValue = selectOption.value;
				if ($chk(optionValue)) data.push({value: optionValue, text: selectOption.innerHTML});
			}
			return data;
		},
		
		addValueFieldEvents: function(){
			this.addEvents({
				'select': function(elements, data, text, index){
					var option = this.valueField.getElement('option[value="' + this.options.valueFilter.call(this, data) + '"]');
					if (option) option.selected = true;
				},
				'deselect': function(elements){
					var option = this.valueField.getSelected()[0];
					if (option) option.selected = false;
				}
			});
		},
		
		getValueFromValueField: function(){
			return this.valueField.getSelected()[0].get('value');
		}
		
	});
	
	Meio.Element = new Class({
		
		Implements: [Events],
		
		initialize: function(node){
			this.setNode(node);
			this.createBoundEvents();
			this.attach();
		},
		
		setNode: function(node){
			this.node = node ? $(node) || $$(node)[0] : this.render();
		},
		
		createBoundEvents: function(){
			this.bound = {};
			this.boundEvents.each(function(evt){
				this.bound[evt] = function(e){
					this.fireEvent('before' + evt.capitalize(), e);
					this[evt] && this[evt](e);
					this.fireEvent(evt, e);
					return true;
				}.bindWithEvent(this);
			}, this);
		},
		
		attach: function(){
			for (var e in this.bound){
				this.node.addEvent(e, this.bound[e]);
			}
		},
		
		detach: function(){
			for (var e in this.bound){
				this.node.removeEvent(e, this.bound[e]);
			}
		},
		
		addClass: function(type){
			this.node.addClass(this.options.classes[type]);
		},
		
		removeClass: function(type){
			this.node.removeClass(this.options.classes[type]);
		},
		
		toElement: function(){
			this.node;
		},
		
		render: $empty
		
	});

	Meio.Element.Field = new Class({
		
		Extends: Meio.Element,
		
		Implements: [Options],
		
		options: {
			classes: {
				loading: 'ma-loading',
				selected: 'ma-selected'
			}
		},
		
		initialize: function(field, options){
			this.keyPressControl = {};
			this.boundEvents = ['paste', 'focus', 'blur', 'click', 'keyup', 'keyrepeat'];
			if (browserEngine.trident4) this.boundEvents.push('keypress'); // yeah super ugly, but what can be awesome with ie?
			this.setOptions(options);
			this.parent(field);
			
			$(global).addEvent('unload', function(){
				if (this.node) this.node.set('autocomplete', 'on'); // if autocomplete is off when you reload the page the input value gets erased
			}.bind(this));
		},
		
		setNode: function(element){
			this.parent(element);
			this.node.set('autocomplete', 'off');
		},
		
		// this let me get the value of the input on keydown and keypress
		keyrepeat: function(e){
			clearInterval(this.keyrepeatTimer);
			this.keyrepeatTimer = this._keyrepeat.delay(1, this, e);
		},
		
		_keyrepeat: function(e){
			this.fireEvent('delayedKeyrepeat', e);
		},
		
		destroy: function(){
			this.detach();
			this.node.removeAttribute('autocomplete');
		},
		
		// ie6 only, uglyness
		// this fix the form being submited on the press of the enter key
		keypress: function(e){
			if (e.key == 'enter') this.bound.keyrepeat(e);
		}
		
	});

	Meio.Element.List = new Class({
		
		Extends: Meio.Element,
		
		Implements: [Options],
		
		options: {
			width: 'field', // you can pass any other value settable by set('width') to the list container
			classes: {
				container: 'ma-container',
				hover: 'ma-hover',
				odd: 'ma-odd',
				even: 'ma-even'
			}
		},
		
		initialize: function(options){
			this.boundEvents = ['mousedown', 'mouseover'];
			this.setOptions(options);
			this.parent();
			this.focusedItem = null;
		},
		
		applyMaxHeight: function(maxVisibleItems){
			var listChildren = this.list.childNodes;
			var node = listChildren[maxVisibleItems - 1] || (listChildren.length ? listChildren[listChildren.length - 1] : null);
			if (!node) return;
			node = $(node);
			// uggly hack to fix the height of the autocomplete list
			for (var i = 2; i--;) this.node.setStyle('height', node.getCoordinates(this.list).bottom);
		},
		
		mouseover: function(e){
			var item = this.getItemFromEvent(e), hoverClass = this.options.classes.hover;
			if (!item) return true;
			if (this.focusedItem) this.focusedItem.removeClass(hoverClass);
			item.addClass(hoverClass);
			this.focusedItem = item;
			this.fireEvent('focusItem', [this.focusedItem]);
		},
		
		mousedown: function(e){
			e.preventDefault();
			this.shouldNotBlur = true;
			if (!(this.focusedItem = this.getItemFromEvent(e))){
				e.dontHide = true;
				return true;
			} 
			this.focusedItem.removeClass(this.options.classes.hover);
		},
		
		focusItem: function(direction){
			var hoverClass = this.options.classes.hover, newFocusedItem;
			if (this.focusedItem){
				if ((newFocusedItem = this.focusedItem[direction == 'up' ? 'getPrevious' : 'getNext']())){
					this.focusedItem.removeClass(hoverClass);
					newFocusedItem.addClass(hoverClass);
					this.focusedItem = newFocusedItem;
					this.scrollFocusedItem(direction);
				}
			} else {
				if ((newFocusedItem = this.list.getFirst())){
					newFocusedItem.addClass(hoverClass);
					this.focusedItem = newFocusedItem;
				}
			}
		},
		
		scrollFocusedItem: function(direction){
			var focusedItemCoordinates = this.focusedItem.getCoordinates(this.list),
				scrollTop = this.node.scrollTop;
			if (direction == 'down'){
				var delta = focusedItemCoordinates.bottom - this.node.getStyle('height').toInt();
				if ((delta - scrollTop) > 0){
					this.node.scrollTop = delta;
				}
			} else {
				var top = focusedItemCoordinates.top;
				if (scrollTop && scrollTop > top){
					this.node.scrollTop = top;
				}
			}
		},
		
		getItemFromEvent: function(e){
			var target = e.target;
			while (target && target.tagName.toLowerCase() != 'li'){
				if (target === this.node) return null;
				target = target.parentNode;
			}
			return $(target);
		},
		
		render: function(){
			var node = new Element('div', {'class': this.options.classes.container});
			if (node.bgiframe) node.bgiframe({top: 0, left: 0});
			this.list = new Element('ul').inject(node);
			$(document.body).grab(node);
			return node;
		},
		
		positionNextTo: function(fieldNode){
			var width = this.options.width, listNode = this.node;
			var elPosition = fieldNode.getCoordinates();
			listNode.setStyle('width', width == 'field' ? fieldNode.getWidth().toInt() - listNode.getStyle('border-left-width').toInt() - listNode.getStyle('border-right-width').toInt() : width);
			//@HACK " + 2" is a dirty hack by me, Stian
			listNode.setPosition({x: elPosition.left, y: elPosition.bottom + 2});
		},
		
		show: function(){
			this.node.scrollTop = 0;
			this.node.setStyle('visibility', 'visible');
			this.showing = true;
		},
		
		hide: function(){
			this.showing = false;
			this.node.setStyle('visibility', 'hidden');
		}
		
	});
	
	Meio.Autocomplete.Filter = {
		
		filters: {},
		
		get: function(options){
			var type = options.type, keys = (options.path || '').split('.');
			var filters = (type && this.filters[type]) ? this.filters[type](this, keys) : options;
			return $merge(this.defaults(keys), filters);
		},
		
		define: function(name, options){
			this.filters[name] = options;
		},
		
		defaults: function(keys){
			var self = this;
			return {
				filter: function(text, data){
					return text ? self._getValueFromKeys(data, keys).test(new RegExp(text.escapeRegExp(), 'i')) : true;
				},
				formatMatch: function(text, data){
					return self._getValueFromKeys(data, keys);
				},
				formatItem: function(text, data, i){
					return text ? self._getValueFromKeys(data, keys).replace(new RegExp('(' + text.escapeRegExp() + ')', 'gi'), '<strong>$1</strong>') : self._getValueFromKeys(data, keys);
				}
			};
		},
		
		_getValueFromKeys: function(obj, keys){
			var key, value = obj, result;
			for (var i = 0; key = keys[i++];) result = value[key];
			return result;
		}
		
	};
	
	Meio.Autocomplete.Filter.define('contains', function(self, keys){return {};});
	Meio.Autocomplete.Filter.define('startswith', function(self, keys){
		return {
			filter: function(text, data){
				return text ? self._getValueFromKeys(data, keys).test(new RegExp('^' + text.escapeRegExp(), 'i')) : true;
			}
		};
	});
	
	Meio.Autocomplete.Data = new Class({
		
		Implements: [Options, Events],
		
		initialize: function(data, cache){
			this._cache = cache;
			this.data = data;
			this.dataString = JSON.encode(this.data);
		},
		
		get: function(){
			return this.data;
		},
		
		getKey: function(){
			return this.cachedKey;
		},
		
		prepare: function(text){
			this.cachedKey = this.dataString + (text || '');
			this.fireEvent('ready');
		},
		
		cache: function(key, data){
			this._cache.set(key, data);
		},
		
		refreshKey: $empty
		
	});
	
	Meio.Autocomplete.Data.Request = new Class({
		
		Extends: Meio.Autocomplete.Data,
		
		options: {
			noCache: true,
			formatResponse: function(jsonResponse){
				return jsonResponse;
			}
		},
		
		initialize: function(url, cache, element, options, urlOptions){
			this.setOptions(options);
			this.rawUrl = url;
			this._cache = cache;
			this.element = element;
			this.urlOptions = urlOptions;
			this.refreshKey();
			this.createRequest();
		},
		
		prepare: function(text){
			this.cachedKey = this.url.evaluate(text);
			if (this._cache.has(this.cachedKey)){
				this.fireEvent('ready');
			} else {
				this.request.send({url: this.cachedKey});
			}
		},
		
		createRequest: function(){
			var self = this;
			this.request = new Request.JSON(this.options);
			this.request.addEvents({
				request: function(){
					self.element.addClass('loading');
				},
				complete: function(){
					self.element.removeClass('loading');
				},
				success: function(jsonResponse){
					self.data = self.options.formatResponse(jsonResponse);
					self.fireEvent('ready');
				}
			});
		},
		
		refreshKey: function(urlOptions){
			urlOptions = $merge(this.urlOptions, {url: this.rawUrl}, urlOptions || {});
			this.url = new Meio.Autocomplete.Data.Request.URL(urlOptions.url, urlOptions);
		}
		
	});
	
	Meio.Autocomplete.Data.Request.URL = new Class({
		
		Implements: [Options],
		
		options: {
			queryVarName: 'q',
			extraParams: null,
			max: 20
		},
		
		initialize: function(url, options){
			this.setOptions(options);
			this.rawUrl = url;
			this.url = url;
			this.url += this.url.contains('?') ? '&' : '?';
			this.dynamicExtraParams = [];
			var params = $splat(this.options.extraParams);
			for (var i = params.length; i--;){
				this.addParameter(params[i]);
			}
			if (this.options.max) this.addParameter('limit=' + this.options.max);
		},
		
		evaluate: function(text){
			text = text || '';
			var params = this.dynamicExtraParams, url = [];
			url.push(this.options.queryVarName + '=' + encodeURIComponent(text));
			for (var i = params.length; i--;){
				url.push(encodeURIComponent(params[i].name) + '=' + encodeURIComponent($lambda(params[i].value)()));
			}
			return this.url + url.join('&');
		},
		
		addParameter: function(param){
			if (param.nodeType == 1 || $type(param.value) == 'function'){
				this.dynamicExtraParams.push(param);
			} else {
				this.url += (($type(param) == 'string') ? param : encodeURIComponent(param.name) + '=' + encodeURIComponent(param.value)) + '&';
			}
		},
		
		// TODO remove non dynamic parameters
		removeParameter: function(param){
			this.dynamicExtraParams.erase(param);
		}
		
	});
	
	Meio.Autocomplete.Cache = new Class({
		
		initialize: function(maxLength){
			this.refresh();
			this.setMaxLength(maxLength);
		},
		
		set: function(key, value){
			if (!this.cache[key]){
				if (this.getLength() >= this.maxLength){
					var keyToRemove = this.pos.shift();
					this.cache[keyToRemove] = null;
					delete this.cache[keyToRemove];
				}
				this.cache[key] = value;
				this.pos.push(key);
			}
			return this;
		},
		
		get: function(key){
			return this.cache[key || ''] || null;
		},
		
		has: function(key){
			return !!this.get(key);
		},
		
		getLength: function(){
			return this.pos.length;
		},
		
		refresh: function(){
			this.cache = {};
			this.pos = [];
		},
		
		setMaxLength: function(maxLength){
			this.maxLength = Math.max(maxLength, 1);
		}
		
	});
	
	globalCache = new Meio.Autocomplete.Cache();
	
	global.Meio = Meio;
	
})(this, document.id || $);
