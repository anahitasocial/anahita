<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Template_Helper
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Grid Helper
 *
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Template_Helper
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
 class ComBaseTemplateHelperGrid extends KTemplateHelperGrid
 {
 	/**
	 * Render an order field
	 *
	 * @param 	array 	An optional array with configuration options
	 * @return	string	Html
	 */
	public function order($config = array())
	{
		$config = new KConfig($config);
		
		$config->append(array(
			'row'  		=> null,
		    'total'		=> null,
		    'field'		=> 'ordering',
		    'data'		=> array('ordering' => 0)
		));

		$up   = 'media://lib_koowa/images/arrow_up.png';
		$down = 'media://lib_koowa/images/arrow_down.png';
		
		$config->data->ordering = $config->row->ordering -1;
		$updata   = str_replace('"', '&quot;', $config->data);
		
		$config->data->ordering = $config->row->ordering +1;
		$downdata = str_replace('"', '&quot;', $config->data);
		
		$html = '';
		
		if ($config->row->{$config->field} > 1) {
            $html .= '<img src="'.$up.'" border="0" alt="'.JText::_('Move up').'" data-action="edit" data-data="'.$updata.'" />';
        }

        $html .= $config->row->{$config->field};

        if($config->row->{$config->field} != $config->total) {
            $html .= '<img src="'.$down.'" border="0" alt="'.JText::_('Move down').'" data-action="edit" data-data="'.$downdata.'"/>';
	    }

		return $html;		
	} 		
	
 	/**
	 * Render a checkbox field
	 *
	 * @param 	array 	An optional array with configuration options
	 * @return	string	Html
	 */
	public function checkbox($config = array())
	{
		$config = new KConfig($config);
		$config->append(array(
			'row'  		=> null,
		));

		if($config->row->isLockable() && $config->row->locked())
		{
		    $html = '<span class="editlinktip hasTip" title="'.$config->row->lockMessage() .'">
						<img src="media://lib_koowa/images/locked.png"/>
					</span>';
		}
		else
		{
		    $column = $config->row->getEntityDescription()->getIdentityProperty()->getName();
		    $value  = $config->row->{$column};

		    $html = '<input type="checkbox" class="-koowa-grid-checkbox" name="'.$column.'[]" value="'.$value.'" />';
		}

		return $html;
	} 	
 }