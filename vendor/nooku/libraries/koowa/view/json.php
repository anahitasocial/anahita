<?php
/**
 * @version     $Id: json.php 4628 2012-05-06 19:56:43Z johanjanssens $
 * @package     Koowa_View
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * View JSON Class
 *
 * The JSON view implements supports for JSONP through the models callback
 * state. If a callback is present the output will be padded.
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Koowa_View
 */
class KViewJson extends KViewAbstract
{
	 /**
	 * The padding for JSONP
	 *
	 * @var string
	 */
	protected $_padding;

	 /**
	 * Constructor
	 *
	 * @param   object  An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);

		//Padding can explicitly be turned off by setting to FALSE
		if(empty($config->padding) && $config->padding !== false)
		{
			$state = $this->getModel()->getState();

			if(isset($state->callback) && (strlen($state->callback) > 0)) {
				$config->padding = $state->callback;
			}
		}

		$this->_padding = $config->padding;
	}

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
			'padding'	  => '',
			'version'	  => '1.0'
		))->append(array(
			'mimetype'	  => 'application/json; version='.$config->version,
		));

		parent::_initialize($config);
	}

	/**
	 * Return the views output
	 *
	 * If the view 'output' variable is empty the output will be generated based on the
	 * model data, if it set it will be returned instead.
	 *
	 * If the model contains a callback state, the callback value will be used to apply
	 * padding to the JSON output.
	  *
	 *  @return string 	The output of the view
	 */
	public function display()
	{
		if(empty($this->output)) {
			$this->output = KInflector::isPlural($this->getName()) ? $this->_getList() : $this->_getItem();
		}

		if (!is_string($this->output)) {
			$this->output = json_encode($this->output);
		}

		//Handle JSONP
		if(!empty($this->_padding)) {
			$this->output = $this->_padding.'('.$this->output.');';
		}

		return parent::display();
	}
	
	/**
	 * Get the list data
	 *
	 * @return array 	The array with data to be encoded to json
	 */
	protected function _getList()
	{
		//Get the model
	    $model = $this->getModel();

	    //Get the route
		$route = $this->getRoute();
		
		//Get the model state
		$state = $model->getState();
		
		//Get the paginator
		$paginator = new KConfigPaginator(array(
          	'offset' => (int) $model->offset,
           	'limit'  => (int) $model->limit,
		    'total'  => (int) $model->getTotal(),
        ));    
		
	    $vars = array();
	    foreach($state->toArray(false) as $var) 
	    {
	        if(!$var->unique) {
	            $vars[] = $var->name;
	        }  
	    }
	     
		$data = array(
			'version'  => '1.0',
			'href'     => (string) $route->setQuery($state->toArray()),
			'url'      => array(
				'type'     => 'application/json',
				'template' => (string) $route->get(KHttpUrl::BASE).'?{&'.implode(',', $vars).'}',
			),
			'offset'   => (int) $paginator->offset,
			'limit'    => (int) $paginator->limit,
			'total'	   => 0,
			'items'    => array(),
			'queries'  => array()
		);

		if($list = $model->getList())
		{
		    $vars = array();
	        foreach($state->toArray(false) as $var) 
	        {
	            if($var->unique) 
	            {
	                $vars[] = $var->name;
	                $vars   = array_merge($vars, $var->required);
	            }      
	        }
	        
	        //Singularize the view name
	        $name = KInflector::singularize($this->getName());
		    
		    $items = array();
			foreach($list as $item) 
			{
			    $id = $item->getIdentityColumn();
			  
			    $items[] = array(
		    		'href'    => (string) $this->getRoute('view='.$name.'&id='.$item->{$id}),
	        		'url'     => array(
						'type'     => 'application/json',
						'template' => (string) $this->getRoute('view='.$name).'?{&'.implode(',', $vars).'}',
	                ),
			    	'data' => $item->toArray()
			    );
			}
			
			$queries = array();
            foreach(array('first', 'prev', 'next', 'last') as $offset) 
            {
                $page = $paginator->pages->{$offset}; 
                if($page->active) 
                {  
                    $queries[] = array(
		   				'rel' => $page->rel, 
		   				'href' => (string) $this->getRoute('limit='.$page->limit.'&offset='.$page->offset)
                    );
                }
            }
            
            $data = array_merge($data, array(
				'total'    => $paginator->total,
				'items'    => $items,
		        'queries'  => $queries
			 ));
		}

		return $data;
	}

	/**
	 * Get the item data
	 *
	 *  @return array 	The array with data to be encoded to json
	 */
	protected function _getItem()
	{
		//Get the model
	    $model = $this->getModel();

	    //Get the route
		$route = $this->getRoute();
		
		//Get the model state
		$state = $model->getState();
		
	    $vars = array();
	    foreach($state->toArray(false) as $var) 
	    {
	        if($var->unique) 
	        {
	            $vars[] = $var->name;
	            $vars   = array_merge($vars, $var->required);
	        }  
	    }
		
		$data = array(
			'version' => '1.0',
		    'href'    => (string) $route->setQuery($state->getData(true)),
	        'url'     => array(
				'type'     => 'application/json',
				'template' => (string) $route->get(KHttpUrl::BASE).'?{&'.implode(',', $vars).'}',
	        ),
	        'item'	  => array()
		);

		if($item = $model->getItem())
		{
		    $data = array_merge($data, array(
				'item' => $item->toArray()
			 ));
		};

		return $data;
	}
}