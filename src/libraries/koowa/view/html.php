<?php
/**
 * @version		$Id: html.php 4628 2012-05-06 19:56:43Z johanjanssens $
 * @package     Koowa_View
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * View HTML Class
 *
 * @author		Johan Janssens <johan@nooku.org>

 * @package     Koowa_View
 */
class KViewHtml extends KViewTemplate
{
	/**
     * Initializes the config for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options
     * @return  void
     */
    protected function _initialize(KConfig $config)
    {
    	$config->append(array(
			'mimetype'	  		=> 'text/html',
    		'template_filters'	=> array('form'),
       	));

    	parent::_initialize($config);
    }

	/**
	 * Return the views output
	 *
	 * This function will auto assign the model data to the view if the auto_assign
	 * property is set to TRUE.
 	 *
	 * @return string 	The output of the view
	 */
	public function display()
	{
	    if(empty($this->output))
		{
	        $model = $this->getModel();

		    //Auto-assign the state to the view
		    $this->assign('state', $model->getState());

		    //Auto-assign the data from the model
		    if($this->_auto_assign)
		    {
			    //Get the view name
			    $name  = $this->getName();

			    //Assign the data of the model to the view
			    if(KInflector::isPlural($name))
			    {
				    $this->assign($name, 	$model->getList())
					     ->assign('total',	$model->getTotal());
			    }
			    else $this->assign($name, $model->getItem());
		    }
		}

		return parent::display();
	}
	
	/**
	 * Get a route based on a full or partial query string. 
	 * 
	 * This function force the route to be not fully qualified. 
	 *
	 * @param	string	The query string used to create the route
	 * @param 	boolean	If TRUE create a fully qualified route. Default TRUE.
	 * @return 	string 	The route
	 */
	public function getRoute( $route = '', $fqr = true)
	{
		return parent::getRoute($route, false);
	}
}