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
 * @subpackage Template
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: view.php 13650 2012-04-11 08:56:41Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Abstract View Template. @see KTemplateAbstract for complete documentation
 * Changes
 * 	Caches a content after the read filtered.
 *  Caches a found path for an identifier
 *  Only look for a new path if loadIdentifier is used with a layout
 *  Load Helper will call with multiple arguments
 *  Acessor for the template data
 *
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Template
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
abstract class LibBaseTemplateAbstract extends KTemplateAbstract
{
	/**
	 * Paths
	 * 
	 * @var array
	 */
	protected $_paths = array();			
	
	/**
	 * Path stach
	 * 
	 * @var array
	 */
	protected $_path_stack = array();
	
	/**
	 * Array of helpers
	 * 
	 * @var array
	 */
	protected $_helpers = array();
	
    /**
     * ArrayAccess interface
     * 
     * @var ArrayAccess
     */
    protected $_cache;
    
    /** 
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     * 
     * @return void
     */ 
    public function __construct(KConfig $config)
    {
        parent::__construct($config);
        
        $this->_cache = $config->cache;
        
        $this->_data  = KConfig::unbox($config->data);
    }
    
    /**
    * Initializes the default configuration for the object
    *
    * Called from {@link __construct()} as a first step of object instantiation.
    *
    * @param KConfig $config An optional KConfig object with configuration options.
    *
    * @return void
    */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'cache' => $this->getService('anahita:cache', array('name'=>$this->getIdentifier())),
            'data'  => array()
        ));

        parent::_initialize($config);
        
        if ( !$config->cache ) {
            $config->cache = new ArrayObject();   
        }
    }
            
	/**
	 * Method to set a view object attached to the controller
	 *
	 * @param	mixed	An object that implements KObjectServiceable, KServiceIdentifier object 
	 * 					or valid identifier string
	 * 
	 * @return	LibBaseTemplateAbstract
	 */
	public function setView($view)
	{
		if(!($view instanceof LibBaseViewAbstract))
		{
			if(is_string($view) && strpos($view, '.') === false ) 
		    {
			    $identifier			= clone $this->getIdentifier();
			    $identifier->path	= array('view', $view);
			    $identifier->name	= 'html';
			}
			else $identifier = $this->getIdentifier($view);
		    
			if($identifier->path[0] != 'view') {
				throw new KTemplateException('Identifier: '.$identifier.' is not a view identifier');
			}

			$view = $identifier;
		}
		
		$this->_view = $view;
		
		return $this;
	}
		
	/**
	 * Get the view object attached to the template
	 *
	 * @return	LibBaseViewAbstract
	 */
	public function getView()
	{
	    if(!$this->_view instanceof LibBaseViewAbstract )
		{
		    //Make sure we have a view identifier
		    if(!($this->_view instanceof KServiceIdentifier)) {
		        $this->setView($this->_view);
            }
		    
		    $this->_view = $this->getService($this->_view);
		}
		
		return $this->_view;
	}
		
	/**
	 * Load a template by path
	 *
	 * @param   string 	The template path
	 * @param	array	An associative array of data to be extracted in local template scope
	 * @param	boolean	If TRUE process the data using a tmpl stream. Default TRUE.
	 * @return KTemplateAbstract
	 */
	public function loadFile($file, $data = array(), $process = true)
	{
		//tracks the recursive paths
		$this->_path_stack[] = $file;
		
		$data['__FILE__'] 	 = $file;
		$data['__DIR__'] 	 = dirname($file);
		 
		$result = parent::loadFile($file, $data, $process);
	
		array_pop($this->_path_stack);
	
		return $result;
	}	
	
	/**
	 * Loads the parent path of the current loaded path
	 *
	 * @param  array $data Template data
	 * 
	 * @return string
	 */
	public function loadParent($data = array())
	{
		$_path = $this->getPath();
		
		if ( empty($_path) ) 
			return '';
						
		$current   = dirname($_path);
		$filename  = basename($_path);
		$cue  	   = false;
		
		//look into the path cache to find the parent
		//if not then find the parent
		if ( !isset($this->_cache['loadParent::'.$_path]) )	
		{
			$parent	   = false;
			
			foreach($this->_paths as $path )
			{
				if ( $cue ) {
					$file = $path.'/'.$filename;
					if ( $this->findFile($file) ) {
						$parent = $file;
						break;
					}
				}
				
				if ( !$cue && $path == $current ) {
					//found the index start looking for the 
					//parent
					$cue = true;
				}
			}
			
			$this->_cache['loadParent::'.$_path] = $parent;
		} 
				
		$parent = $this->_cache['loadParent::'.$_path];
		
		if ( !$parent ) {
			throw new KTemplateException("The {$_path} template has no parent");
		}
	
		$result =  $this->loadFile($parent, $data);
			
		return $result;
	}
	
	/**
	 * Loads a template using the identifier by converting an identifier to a path. On the contrary to
	 * KTemplateAbstract if a KServiceIdentifier is passed, it will not append the path directory as the 
	 * default path of $template->_paths
	 * 
	 * @param KServiceIdentifier $template Template Identifier
	 * @param array              $data     Template data
	 *    
	 * @return KTemplateAbstract
	 */
	public function loadIdentifier($template, $data = array())
	{
		//Identify the template
	    $identifier = $this->getIdentifier($template);
	    
	    //add the path to the template paths
	    //@TODO shoudl we do that or just try load the template path
        $path       = dirname($identifier->filepath);
        
	    if ( !in_array($path, $this->_paths) ) {
			array_unshift($this->_paths, $path);
	    }
	    //load the template
	    return $this->loadTemplate($identifier->name, $data);
	}
	
	/**
	 * Loads a template by first trying to find the template file
	 *
	 * @param string $template Template name
	 * @param array  $data     Template data
	 * 
	 * @param boolean $process	If TRUE process the data using a tmpl stream. Default TRUE.
	 */
	public function loadTemplate($template, $data = array(), $process = true)
	{
		$file = $template.'.php';
				
		$path = $this->findPath($file);
		
		if ( !$path ) {
	    	throw new KTemplateException($template.' template not found for '.$this->getIdentifier());
	    }
	    
	    $path = $path.'/'.$file;
	    
	    return $this->loadFile($path, $data, $process);
	}
	
	/**
	 * Load a template helper. On Contrary to Nooku, it allows for any number of 
	 * argument than just an array
	 *
	 * @param	string	Name of the helper, dot separated including the helper function to call
	 * @param	mixed	Parameters to be passed to the helper
	 * @return 	string	Helper output
	 */
	public function renderHelper()
	{
		$args		 = func_get_args();
		$identifier  = array_shift($args);
		
		//Get the function to call based on the $identifier
		$parts    = explode('.', $identifier);
		$function = array_pop($parts);
		
		$helper = implode('.', $parts); 
        			
		$helper = $this->getHelper($helper);
		
		//Call the helper function
		if (!is_callable( array( $helper, $function ) )) {
			throw new KTemplateHelperException( get_class($helper).'::'.$function.' not supported.' );
		}
		
		return call_object_method($helper, $function, $args);		
	}

	/**
	 * Get a template helper
	 *
	 * @param	mixed	An object that implements KObjectIdentifiable, an object that
	 *                  implements KServiceIdentifierInterface or valid identifier string
	 * @param	mixed	Parameters to be passed to the helper
	 * @return 	KTemplateHelperInterface
	 */
	public function getHelper($helper)
	{
	    $name = (string) $helper;
	    if ( !isset($this->_helpers[$name]) )
	    {	        
	        if(is_string($helper) && strpos($helper, '.') === false )
	        {
	            $identifier = clone $this->getIdentifier();
	            $identifier->path = array('template','helper');
	            $identifier->name = $helper;
	            register_default(array('identifier'=>$identifier, 'prefix'=>$this));
	            $helper = $identifier;
	        }
	        $this->_helpers[$name] = parent::getHelper($helper);	        
	    }
	    return $this->_helpers[$name];	
	}		
		
	/**
	 * Caches the found paths. @see KTemplateAbstract::findPath for more detail 
	 *
	 * @param	string			The file name to look for.
	 * @return	mixed			The full path and file name for the target file, or FALSE
	 * 							if the file is not found in any of the paths
	 */
	public function findPath($filename)
	{
        $key = 'filepath::'.$filename;
        
		if ( !isset($this->_cache[$key]) )
		{
			foreach($this->_paths as $path)
			{
				$file = $path.'/'.$filename;
				if ( $this->findFile($file) ) {
					$this->_cache[$key] = $path;
					return $path;
				}
			}	
			$this->_cache[$key] = false;
		}
		
		return $this->_cache[$key];
	}

	/**
	 * Return all the template paths
	 * 
	 * @return array
	 */
	public function getPaths()
	{
		return $this->_paths;
	}
	
	/**
	 * Add a new search path. By the default the path is added to the top of the search path
	 *
	 * @param string|array The path(s) to add.
     * 
	 * @return  KTemplateAbstract
	 */
	public function addPath($paths, $append = false)
	{
		settype($paths, 'array');
		
		foreach($paths as $path) 
		{
            if ( empty($path) ) {
                continue;
            }
                
			if ( in_array($path, $this->_paths) )
				continue;
							
			if ( $append )
				$this->_paths[] = $path;
			else {
				array_unshift($this->_paths, $path);
			}
		}
	}   
	
	/**
	 * Parse the data. If a data has been parsed, it will serve it from the cache
	 *
	 * @return string	The filtered data
	 */
	public function parse()
	{
		$key  = $this->getPath();
        
        if ( !$key ) {
            $key = md5($this->_contents);
        }
        
        $key = 'parse::'.$key;
        
        if ( !isset($this->_cache[$key]) )
        {
            $this->_cache[$key] = parent::parse();
        }
        
        return $this->_cache[$key];
	}

	/**
	 * Get a filter by identifier
	 *
	 * @return KTemplateFilterInterface
	 */
	public function getFilter($filter)
	{
	    //Create the complete identifier if a partial identifier was passed
	    if(is_string($filter) && strpos($filter, '.') === false )
	    {
	        if ( !isset($this->_filters[$filter]) )
	        {
	            $identifier = clone $this->getIdentifier();
	            $identifier->path = array('template', 'filter');
	            $identifier->name = $filter;
	            register_default(array('identifier'=>$identifier, 'prefix'=>$this));
	            $filter = $identifier;
	        }
	    }
	    
	    return parent::getFilter($filter);
	}
		
	/**
	 * Handle template errors
	 *
	 * @return bool
	 */
	public function handleError($code, $message, $file = '', $line = 0, $context = array())
	{
	    if($file == 'tmpl://koowa:template.stack' || $code == 1 )
	    {
            if ( $file == 'tmpl://koowa:template.stack') {
                $file = $this->getPath();  
            }
            
	        if(ini_get('display_errors')) {
	            echo '<strong>'.$code.'</strong>: '.$message.' in <strong>'.$file.'</strong> on line <strong>'.$line.'</strong>';
	        }
	
	        if(ini_get('log_errors')) {
	            error_log(sprintf('PHP %s:  %s in %s on line %d', $code, $message, $file, $line));
	        }
	
	        return true;
	    }
	
	    return false;
	}
		
	/**
	 * Return the data used in the template 
	 *
	 * @return array
	 */
	public function getPath()
	{
		return end($this->_path_stack);
	}	
}