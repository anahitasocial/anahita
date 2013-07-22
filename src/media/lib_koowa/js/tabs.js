/**
 * @version     $Id: default.php 2721 2010-10-27 00:58:51Z johanjanssens $
 * @category    Nooku
 * @package     Nooku_Media
 * @subpackage  Javascript
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

if(!Koowa) var Koowa = {};

/**
 * Koowa global namespace
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @category    Nooku
 * @package     Nooku_Media
 * @subpackage  Javascript
 */
(function(){
var $ = document.id;

Koowa.Tabs = new Class({

    Implements: [Options, Events],
    
    getOptions: function()
    {
        return {

            display: 0,

            onActive: function(title, description){
                description.setStyle('display', 'block');
                title.addClass('open').removeClass('closed');
            },

            onBackground: function(title, description){
                description.setStyle('display', 'none');
                title.addClass('closed').removeClass('open');
            }
        };
    },

    initialize: function(dlist, options)
    {
        this.dlist = $(dlist);
        this.setOptions(this.getOptions(), options);
        this.titles = this.dlist.getChildren('dt');
        this.descriptions = this.dlist.getChildren('dd');
        this.content = new Element('div').injectAfter(this.dlist).addClass('current');
          
        if(this.options.height) {
            this.content.setStyle('height', this.options.height);
        }

        for (var i = 0, l = this.titles.length; i < l; i++)
        {
            var title = this.titles[i];
            var description = this.descriptions[i];
            title.setStyle('cursor', 'pointer');
            title.addEvent('click', this.display.bind(this, i));
            description.injectInside(this.content);
        }
        
        if(Cookie.read('ktabs.' + dlist)) {
        	this.options.display = Cookie.read('ktabs.' + dlist);
        }
    
        if ($chk(this.options.display)) {
            this.display(this.options.display);
        }

        if (this.options.initialize) {
            this.options.initialize.call(this);
        }
    },

    hideAllBut: function(but)
    {
        for (var i = 0, l = this.titles.length; i < l; i++){
            if (i != but) this.fireEvent('onBackground', [this.titles[i], this.descriptions[i]])
        }
    },

    display: function(i)
    {
    	Cookie.write('ktabs.' + this.dlist.getProperty('id'), i);
        this.hideAllBut(i);
        this.fireEvent('onActive', [this.titles[i], this.descriptions[i]])
    }
});
})();