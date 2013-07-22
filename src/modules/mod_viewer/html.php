<?php

/** 
 * LICENSE: Anahita is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 * 
 * @category   Anahita
 * @package    Mod_Viewer
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Viewer Module
 *
 * @category   Anahita
 * @package    Mod_Viewer
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ModViewerHtml extends ModBaseView
{
    /**
     * Before default layout
     * 
     * @return void
     */
    protected function _layoutDefault()
    {
        $this->getService('koowa:loader')->loadIdentifier('mod://site/mainmenu.helper');
        $menus = JSite::getMenu()->getItems('menutype', $this->params->get('menutype'));
        $xml = modMainMenuHelper::getXML($this->params->get('menutype'), $this->params, 'mod_viewer_menu_decorator');
        $this->menus = $xml->children();
        
        //$this->getService('koowa:loader')->loadIdentifier('mod://site/login.helper');
        //$this->return = modLoginHelper::getReturnURL($this->params,'logout');
        //$uri = JFactory::getURI();
        $this->return   = base64_encode(JURI::base());
    }
}

/**
 * Decorates a menu item
 * 
 * @param JSimpleXMLElement $node 
 * @param array             $args
 * 
 * @return JSimpleXmlElement
 */
function mod_viewer_menu_decorator(&$node, $args)
{
    $user   = &JFactory::getUser();
    $menu   = &JSite::getMenu();
    $active = $menu->getActive();
    $path   = isset($active) ? array_reverse($active->tree) : null;
    
    if (($args['end']) && ($node->attributes('level') >= $args['end']))
    {
        $children = $node->children();
        foreach ($node->children() as $child)
        {
            if ($child->name() == 'ul') {
                $node->removeChild($child);
            }
        }
    }
    
    if ($node->name() == 'ul') 
    {
        foreach ($node->children() as $child)
        {
            if ($child->attributes('access') > $user->get('aid', 0)) {
                $node->removeChild($child);
            }
        }
    }

    if (($node->name() == 'li') && isset($node->ul)) 
    {
        //has sub menu, ignore
        $node->addAttribute('class', 'dropdown');
        $link = $node->getElementByPath('a');
        if ( $link ) 
        {
            $data = $link->data();
            $link->addAttribute('class', 'dropdown-toggle');
            $link->addAttribute('data-toggle','dropdown');          
            $link->addChild('b', array('class'=>'caret'))->setData(' ');
        }
        $ul = $node->getElementByPath('ul');
        if ( $ul )
        {
            $ul->addAttribute('class', 'dropdown-menu');
        }
    }

    if (isset($path) && (in_array($node->attributes('id'), $path) || in_array($node->attributes('rel'), $path)))
    {
        if ($node->attributes('class')) {
            $node->addAttribute('class', $node->attributes('class').' active');
        } else {
            $node->addAttribute('class', 'active');
        }
    }
    else
    {
        if (isset($args['children']) && !$args['children'])
        {
            $children = $node->children();
            foreach ($node->children() as $child)
            {
                if ($child->name() == 'ul') {
                    $node->removeChild($child);
                }
            }
        }
    }

    if (($node->name() == 'li') && ($id = $node->attributes('id'))) 
    {
        if ($node->attributes('class')) {
            $node->addAttribute('class', $node->attributes('class').' item'.$id);
        } else {
            $node->addAttribute('class', 'item'.$id);
        }
    }

    if (isset($path) && $node->attributes('id') == $path[0]) 
    {
        $node->addAttribute('id', 'current');
    } else 
    {
        $node->removeAttribute('id');
    }
    $node->removeAttribute('rel');
    $node->removeAttribute('level');
    $node->removeAttribute('access');
}