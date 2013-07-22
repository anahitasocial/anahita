<?php
/**
 * @version     $Id: default.php 2721 2010-10-27 00:58:51Z johanjanssens $
 * @package     Nooku_Components
 * @subpackage  Default
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Default Paginator Helper
.*
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Nooku_Components
 * @subpackage  Default
 * @uses        KRequest
 * @uses        KConfig
 */
class ComDefaultTemplateHelperPaginator extends KTemplateHelperPaginator
{
    /**
     * Render item pagination
     * 
     * @param   array   An optional array with configuration options
     * @return  string  Html
     * @see     http://developer.yahoo.com/ypatterns/navigation/pagination/
     */
    public function pagination($config = array())
    { 
        $config = new KConfigPaginator($config);
        $config->append(array(
            'total'      => 0,
            'display'    => 4,
            'offset'     => 0,
            'limit'      => 0,
            'show_limit' => true,
		    'show_count' => true
        ));
         
        $html   = '<div class="container">';
        $html  .= '<div class="pagination">';
        if($config->show_limit) {
            $html .= '<div class="limit">'.JText::_('Display NUM').' '.$this->limit($config).'</div>';
        }
        $html .=  $this->pages($config);
        if($config->show_count) {
            $html .= '<div class="limit"> '.JText::_('Page').' '.$config->current.' '.JText::_('of').' '.$config->count.'</div>';
        }
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Render a list of pages links
     * 
     * This function is overriddes the default behavior to render the links in the khepri template
     * backend style.
     *
     * @param   array   An optional array with configuration options
     * @return  string  Html
     */
    public function pages($config = array())
    {
        $config = new KConfigPaginator($config);
		$config->append(array(
			'total'      => 0,
			'display'    => 4,
			'offset'     => 0,
			'limit'	     => 0,
			'attribs'	=> array(),
		));
	   
        $class = $config->pages->first->active ? '' : 'off';
        $html  = '<div class="button2-right '.$class.'"><div class="start">'.$this->link($config->pages->first).'</div></div>';
        
        $class = $config->pages->prev->active ? '' : 'off';
        $html  .= '<div class="button2-right '.$class.'"><div class="prev">'.$this->link($config->pages->prev).'</div></div>';
        
        $html  .= '<div class="button2-left"><div class="page">';
        foreach($config->pages->offsets as $offset) {
            $html .= $this->link($offset);
        }
        $html .= '</div></div>';
        
        $class = $config->pages->next->active ? '' : 'off';
        $html  .= '<div class="button2-left '.$class.'"><div class="next">'.$this->link($config->pages->next).'</div></div>';
        
        $class = $config->pages->last->active ? '' : 'off';
        $html  .= '<div class="button2-left '.$class.'"><div class="end">'.$this->link($config->pages->last).'</div></div>';

        return $html;
    }
}