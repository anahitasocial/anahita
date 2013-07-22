<?php
/**
 * @version     $Id: image.php 4628 2012-05-06 19:56:43Z johanjanssens $
 * @package     Nooku_Components
 * @subpackage  Default
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Editor Helper
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Nooku_Components
 * @subpackage  Default
 * @uses        KConfig
 */
class ComDefaultTemplateHelperImage extends KTemplateHelperListbox
{
 	/**
     * Generates an HTML image position listbox
     *
     * @param 	array   An optional array with configuration options
     * @return 	string  Html
     */
   	public function position($config = array())
   	{
       	$config = new KConfig($config);
      	$config->append(array(
          	'name'          => 'image_position',
           	'attribs'       => array(),
            'deselect'      => false
      	))->append(array(
           	'selected'  => $config->{$config->name}
       	));

		$options  = array();

     	if($config->deselect) {
         	$options[] =  $this->option(array('text' => '- '.JText::_( 'Select' ).' -'));
       	}

      	$options[] = $this->option(array('text' => JText::_( 'Left' ), 'value' => 'left' ));
      	$options[] = $this->option(array('text' => JText::_( 'Center' ), 'value' => 'center' ));
       	$options[] = $this->option(array('text' => JText::_( 'Right' ), 'value' => 'right' ));

       	//Add the options to the config object
       	$config->options = $options;

      	return $this->optionlist($config);
	}

	/**
	 * Generated a HTML images listbox
	 * .
	 * $config options
	 *
	 * name			string		column name of helper
	 * directory	string		image directory (relative to docroot)
	 * filetypes	array 		allowd file type extensions
	 * deselect		boolean		show -select- option with 0 value
	 * preview		boolean		show preview directly below listbox
	 * selected		string		currently selected value
	 * attribs		array		associative array of listbox attributes
	 *
	 * @param   array   An optional array with configuration options
     * @return  string  Html
	 */
	public function listbox($config = array())
	{
  		$config = new KConfig($config);
  		$config->append(array(
   			'name'		=> 'image_name',
   			'directory'	=> JPATH_IMAGES.'/stories',
  			'filetypes'	=> array('swf', 'gif', 'jpg', 'png'),
   			'deselect'	=> true,
  		    'preview'   => true
  		))->append(array(
                        'selected'  => $config->{$config->name}
		))->append(array(
			'attribs' => array(
			'id' => $config->name,
			'class' => 'inputbox'
			)));

	    $root = JURI::root(true).str_replace(JPATH_ROOT, '', $config->directory);

		$html = "
		<script>
		window.addEvent('domready', function(){
			$('".$config->name."').addEvent('change', function(){
				var value = this.value ? ('".$root."/' + this.value) : 'media://system/images/blank.png';
				$('".$config->name."-preview').src = value;
			});
		});
		</script>";

		if($config->deselect) {
			$options[] = $this->option(array('text' => '- '.JText::_( 'Select' ).' -', 'value' => ''));
  		}

		$files = array();
  		foreach(new DirectoryIterator($config->directory) as $file) {
   			if(in_array(pathinfo($file, PATHINFO_EXTENSION), $config->filetypes->toArray() )) {
    				$files[] = (string) $file;
   			}
  		}
		sort($files);
		foreach( $files as $file) {
			$options[] = $this->option(array('text' => (string) $file, 'value' => (string) $file));
 		}


  		$list = $this->optionlist(array(
   			'options' => $options,
   			'name'  => $config->name,
   			'attribs' => $config->attribs,
   			'selected' => $config->selected
  		));

  		$html .= $config->preview ? $list.'<br />'.$this->preview($config) : $list;

  		return $html;
 	}

 	/**
 	 * Generates an HTML image preview listbox
 	 *
 	 * $config options:
 	 *
	 * name			string		column name of helper
	 * directory	string		image directory (relative to docroot)
 	 * width		int			image width
 	 * height		int			image height
 	 * border		int			border width
 	 * style		string		style string
 	 * selected		string		currently selected vallue
 	 *
 	 * @param   array   An optional array with configuration options
     * @return  string  Html
 	 */
 	public function preview($config = array())
 	{
 	    $config = new KConfig($config);
 	    $config->append(array(
   			'name'		=> 'image_name',
   			'directory'	=> JPATH_IMAGES.'/stories',
 	    	'width'		=> 80,
   			'height'	=> 80,
   			'border'	=> 2,
   			'style'		=> 'margin: 10px 0;'
  		))->append(array(
            'selected'  => $config->{$config->name}
 	    ));

 	    $image = JURI::root(true).str_replace(JPATH_ROOT, '', $config->directory).'/'.$config->selected;

 	    $path = $config->selected ? $image : 'media://system/images/blank.png';
  		$html = '<img '.KHelperArray::toString(array(
  			'src'		=> $path,
  			'id'		=> $config->name.'-preview',
  			'class'		=> 'preview',
  			'width'		=> $config->width,
  			'height'	=> $config->height,
  			'border'	=> $config->border,
  			'alt'		=> JText::_('Preview'),
  			'style'		=> $config->style
  		)).' />';

 	    return $html;
 	}
}