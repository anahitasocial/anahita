<?php
/**
 * @version		$Id: grid.php 4628 2012-05-06 19:56:43Z johanjanssens $
 * @package		Koowa_Template
 * @subpackage	Helper
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * Template Grid Helper
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package		Koowa_Template
 * @subpackage	Helper
 * @see 		http://ajaxpatterns.org/Data_Grid
 */
class KTemplateHelperGrid extends KTemplateHelperAbstract
{
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
			'row'    => null,
	    ))->append(array( 
        	'column' => $config->row->getIdentityColumn() 
        )); 
		
		if($config->row->isLockable() && $config->row->locked())
		{
		    $html = '<span class="editlinktip hasTip" title="'.$config->row->lockMessage() .'">
						<img src="media://lib_koowa/images/locked.png"/>
					</span>';
		}
		else
		{
		    $column = $config->column;
		    $value  = $config->row->{$column};

		    $html = '<input type="checkbox" class="-koowa-grid-checkbox" name="'.$column.'[]" value="'.$value.'" />';
		}

		return $html;
	}

	/**
	 * Render an search header
	 *
	 * @param 	array 	An optional array with configuration options
	 * @return	string	Html
	 */
	public function search($config = array())
	{
	    $config = new KConfig($config);
		$config->append(array(
			'search' => null
		));

	    $html = '<input name="search" id="search" value="'.$this->getTemplate()->getView()->escape($config->search).'" />';
        $html .= '<button>'.JText::_('Go').'</button>';
		$html .= '<button onclick="document.getElementById(\'search\').value=\'\';this.form.submit();">'.JText::_('Reset').'</button>';

	    return $html;
	}

	/**
	 * Render a checkall header
	 *
	 * @param 	array 	An optional array with configuration options
	 * @return	string	Html
	 */
	public function checkall($config = array())
	{
		$config = new KConfig($config);

		$html = '<input type="checkbox" class="-koowa-grid-checkall" />';
		return $html;
	}

	/**
	 * Render a sorting header
	 *
	 * @param 	array 	An optional array with configuration options
	 * @return	string	Html
	 */
	public function sort( $config = array())
	{
		$config = new KConfig($config);
		$config->append(array(
			'title'   	=> '',
			'column'  	=> '',
			'direction' => 'asc',
			'sort'		=> ''
		));


		//Set the title
		if(empty($config->title)) {
			$config->title = ucfirst($config->column);
		}

		//Set the direction
		$direction	= strtolower($config->direction);
		$direction 	= in_array($direction, array('asc', 'desc')) ? $direction : 'asc';

		//Set the class
		$class = '';
		if($config->column == $config->sort)
		{
			$direction = $direction == 'desc' ? 'asc' : 'desc'; // toggle
			$class = 'class="-koowa-'.$direction.'"';
		}

		$route = $this->getTemplate()->getView()->getRoute('sort='.$config->column.'&direction='.$direction);

		$html  = '<a href="'.$route.'" title="'.JText::_('Click to sort by this column').'"  '.$class.'>';
		$html .= JText::_($config->title);
		$html .= '</a>';

		return $html;
	}

	/**
	 * Render an enable field
	 *
	 * @param 	array 	An optional array with configuration options
	 * @return	string	Html
	 */
	public function enable($config = array())
	{
		$config = new KConfig($config);
		$config->append(array(
			'row'  		=> null,
		    'field'		=> 'enabled'
		))->append(array(
		    'data'		=> array($config->field => $config->row->{$config->field})
		));

		$img    = $config->row->{$config->field} ? 'enabled.png' : 'disabled.png';
		$alt 	= $config->row->{$config->field} ? JText::_( 'Enabled' ) : JText::_( 'Disabled' );
		$text 	= $config->row->{$config->field} ? JText::_( 'Disable Item' ) : JText::_( 'Enable Item' );

	    $config->data->{$config->field} = $config->row->{$config->field} ? 0 : 1;
	    $data = str_replace('"', '&quot;', $config->data);

		$html = '<img src="media://lib_koowa/images/'. $img .'" border="0" alt="'. $alt .'" data-action="edit" data-data="'.$data.'" title='.$text.' />';

		return $html;
	}

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
		    'data'		=> array('order' => 0)
		));

		$up   = 'media://lib_koowa/images/arrow_up.png';
		$down = 'media://lib_koowa/images/arrow_down.png';

		$config->data->order = -1;
		$updata   = str_replace('"', '&quot;', $config->data);

		$config->data->order = +1;
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
	 * Render an access field
	 *
	 * @param 	array 	An optional array with configuration options
	 * @return	string	Html
	 */
	public function access($config = array())
	{
		$config = new KConfig($config);
		$config->append(array(
			'row'  		=> null,
		    'field'		=> 'access'
		))->append(array(
		    'data'		=> array($config->field => $config->row->{$config->field})
		));

		switch($config->row->{$config->field})
		{
			case 0 :
			{
				$color   = 'green';
				$group   = JText::_('Public');
				$access  = 1;
			} break;

			case 1 :
			{
				$color   = 'red';
				$group   = JText::_('Registered');
				$access  = 2;
			} break;

			case 2 :
			{
				$color   = 'black';
				$group   = JText::_('Special');
				$access  = 0;
			} break;

		}

		$config->data->{$config->field} = $access;
	    $data = str_replace('"', '&quot;', $config->data);

		$html = '<span style="color:'.$color.'" data-action="edit" data-data="'.$data.'">'.$group.'</span>';

		return $html;
	}
}