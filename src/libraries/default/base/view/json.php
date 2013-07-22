<?php

/** 
 * LICENSE: Anahita is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 * 
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage View
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: view.php 13650 2012-04-11 08:56:41Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * JSON View Class
 *
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage View
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class LibBaseViewJson extends LibBaseViewAbstract
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
			if(isset($this->callback) && (strlen($this->callback) > 0)) {
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
	 *  @return string 	The output of the view
	 */
    public function display()
    { 
        $name  = $this->getName();
        
        $data  = array();
        
        //if data is set the just json encode those
        if ( count($this->_data) ) {
            $this->output = $this->_data;
        }
        
        else if ( KInflector::isPlural($name) ) {
            $this->output = $this->_getList();
        }
        
        else {
            $this->output = $this->_getItem();
        }
      
        if (!is_string($this->output)) {
            $this->output = json_encode($this->output);
        }

        //Handle JSONP
        if(!empty($this->_padding)) {
            $this->output = $this->_padding.'('.$this->output.');';
        }
        
    	return $this->output;
    }
    
    /**
     * Return the list
     * 
     * @return array
     */
    protected function _getList()
    {
        $data = array();
        
        if ( $items = $this->_state->getList() ) 
        {          
            $name = KInflector::singularize($this->getName());
             
            foreach($items as $item) 
            {                
               $id = $item->getIdentityProperty();
            
               $data[] = array_merge(array(
                      'href'    => (string) $this->getRoute('view='.$name.'&id='.$item->{$id}),
                    ), $item->toSerializableArray());
            }
            
            $data = array(
                $this->getName() => $data,
                'pagination'     => array(
                    'offset' => (int) $items->getOffset(),
                    'limit'  => (int) $items->getLimit(),
                    'total'  => (int) $items->getTotal(),                
                )
            );            
        }
        
        return $data;
    }
    
    /**
     * Return the list
     * 
     * @return array
     */
    protected function _getItem()
    {
        $data = array(); 
        
        if ( $item = $this->_state->getItem() ) {
            $data = $item->toSerializableArray();   
        }
        
        return $data;
    }
}