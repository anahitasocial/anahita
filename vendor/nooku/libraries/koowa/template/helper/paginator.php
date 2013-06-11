<?php
/**
 * @version		$Id: paginator.php 4628 2012-05-06 19:56:43Z johanjanssens $
 * @package		Koowa_Template
 * @subpackage	Helper
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * Template Paginator Helper
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package		Koowa_Template
 * @subpackage	Helper
 */
class KTemplateHelperPaginator extends KTemplateHelperSelect
{
	/**
	 * Render item pagination
	 * 
	 * @param 	array 	An optional array with configuration options
	 * @return	string	Html
	 * @see  	http://developer.yahoo.com/ypatterns/navigation/pagination/
	 */
	public function pagination($config = array())
	{
	    $config = new KConfigPaginator($config);
		$config->append(array(
		    'total'      => 0,
            'display'    => 4,
            'offset'     => 0,
            'limit'      => 0,
		    'attribs'	 => array(),
		    'show_limit' => true,
		    'show_count' => true
		));
	
		$html = '';
		$html .= '<style src="media://lib_koowa/css/koowa.css" />';

		$html .= '<div class="-koowa-pagination">';
		if($config->show_limit) {
		    $html .= '<div class="limit">'.JText::_('Display NUM').' '.$this->limit($config).'</div>';
		}
		$html .=  $this->pages($config);
		if($config->show_count) {
		    $html .= '<div class="count"> '.JText::_('Page').' '.$config->current.' '.JText::_('of').' '.$config->count.'</div>';
		}
		$html .= '</div>';

		return $html;
	}
	
	/**
	 * Render a select box with limit values
	 *
	 * @param 	array 	An optional array with configuration options
	 * @return 	string	Html select box
	 */
	public function limit($config = array())
	{
		$config = new KConfig($config);
		$config->append(array(
			'limit'	  	=> 0,
			'attribs'	=> array(),
		));
		
		$html = '';
		
		$selected = '';
		foreach(array(10 => 10, 20 => 20, 50 => 50, 100 => 100) as $value => $text)
		{
			if($value == $config->limit) {
				$selected = $value;
			}

			$options[] = $this->option(array('text' => $text, 'value' => $value));
		}

		$html .= $this->optionlist(array('options' => $options, 'name' => 'limit', 'attribs' => $config->attribs, 'selected' => $selected));
		return $html;
	}

	/**
	 * Render a list of pages links
	 *
	 * @param   array   An optional array with configuration options
	 * @return	string	Html
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
	    
	    $html = '<ul class="pages">';

		$html .= '<li class="first">&laquo; '.$this->link($config->pages->first).'</li>';
		$html .= '<li class="previous">&lt; '.$this->link($config->pages->prev).'</li>';

		foreach($config->pages->offsets as $offset) {
			$html .= '<li>'.$this->link($offset).'</li>';
		}

		$html .= '<li class="next">'.$this->link($config->pages->next).' &gt;</li>';
		$html .= '<li class="previous">'.$this->link($config->pages->last).' &raquo;</li>';

		$html .= '</ul>';
		return $html;
	}

	/**
	 * Render a page link
	 *
	 * @param	object The page data
	 * @param	string The link title
	 * @return	string	Html
	 */
    public function link($config)
    {
        $config = new KConfig($config);
		$config->append(array(
			'title'   => '',
			'current' => false,
		    'active'  => false,
			'offset'  => 0,
			'limit'	  => 0,
		    'rel'	  => '',
			'attribs'  => array(),
		));
		
        $route = $this->getTemplate()->getView()->getRoute('limit='.$config->limit.'&offset='.$config->offset);
        $class = $config->current ? 'class="active"' : '';
        $rel   = !empty($config->rel) ? 'rel="'.$config->rel.'"' : ''; 

        if($config->active && !$config->current) {
            $html = '<a href="'.$route.'" '.$class.' '.$rel.'>'.JText::_($config->title).'</a>';
        } else {
            $html = '<span '.$class.'>'.JText::_($config->title).'</span>';
        }

        return $html;
    }
}