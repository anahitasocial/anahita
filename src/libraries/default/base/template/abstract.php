<?php

/** 
 * LICENSE: ##LICENSE##
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
	 * Array of search path
	 * 
	 * @var array
	 */
	protected $_search_paths = array();
	
	/**
	 * The template load stack
	 * 
	 * @var array
	 */
	protected $_load_stack = array();
	
	/**
	 * Array of helpers
	 * 
	 * @var array
	 */
	protected $_helpers = array();
	
	/**
	 * Contains the paths for the template
	 * 
	 * @var array
	 */
	protected $_paths = array();
	
	/**
	 * stores the parsed data for each path
	 * 
	 * @var array
	 */
	protected $_parsed_data;
    
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
        
        $this->_data  = KConfig::unbox($config->data);
        
        $this->_parsed_data = new ArrayObject();
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
            'data'  => array()
        ));

        parent::_initialize($config);
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
		$this->_load_stack[]		 = $file;
		
		$data['__FILE__'] 	 = $file;
		$data['__DIR__'] 	 = dirname($file);
		 
		$result = parent::loadFile($file, $data, $process);
	
		array_pop($this->_load_stack);
		//the path 
		$this->_path = end($this->_load_stack);
	
		return $result;
	}		
	
	/**
	 * Loads a template using the identifier by converting an identifier to a path. On the contrary to
	 * KTemplateAbstract if a KServiceIdentifier is passed, it will not append the path directory as the 
	 * default path of $template->_search_paths
	 * 
	 * @param KServiceIdentifier $template Template Identifier
	 * @param array              $data     Template data
	 * @param boolean $process	If TRUE process the data using a tmpl stream. Default TRUE.
	 *    
	 * @return KTemplateAbstract
	 */
	public function loadIdentifier($template, $data = array(), $process = true)
	{
		//Identify the template
	    $identifier = $this->getIdentifier($template);
	    
	    //add the path to the template paths
	    //@TODO shoudl we do that or just try load the template path
        $path       = dirname($identifier->filepath);
        
	    if ( !in_array($path, $this->_search_paths) ) {
			array_unshift($this->_search_paths, $path);
	    }
	    //load the template
	    return $this->loadTemplate($identifier->name, $data, $process);
	}
	
	/**
	 * Loads a template by first trying to find the template file
	 *
	 * @param string $template Template name
	 * @param array  $data     Template data
	 * @param boolean $process	If TRUE process the data using a tmpl stream. Default TRUE.
	 * 
	 * @return string
	 */
	public function loadTemplate($template, $data = array(), $process = true)
	{				
		$path = $this->findTemplate($template);
		 
		if ( !$path ) {
		    //@TODO a hack to prevent caching the 
		    //paths that are not found
		    unset($this->_paths[$template.'.php']);
	    	throw new KTemplateException($template.' template not found for '.$this->getIdentifier());
	    }
	    	    
	    return $this->loadFile($path, $data, $process);
	}
	
	/**
	 * Load a template helper. On Contrary to Nooku, it allows for any number of 
	 * argument than just an array
	 *
	 * @param	string	Name of the helper, dot separated including the helper function to call
	 * @param	mixed	Parameters to be passed to the helper
	 * 
	 * @return 	string	Helper output
	 */
	public function renderHelper($identifier, $config = array())
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
	public function getHelper($helper, $config = array())
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
	        $this->_helpers[$name] = parent::getHelper($helper, $config);	        
	    }
	    return $this->_helpers[$name];	
	}		

	/**
	 * Same as findPath except it automatically adds the .php extension
	 * 
	 * @param string $template The template path
	 * 
	 * @return string 
	 */
	public function findTemplate($template)
	{
		return $this->findPath($template.'.php');
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
		if ( !isset($this->_paths[$filename]) )
		{
			foreach($this->_search_paths as $path)
			{
				$file = $path.'/'.$filename;
				if ( $this->findFile($file) ) {
					$this->_paths[$filename] = $file;
					return $file;
				}
			}
			$this->_paths[$filename] = false;
		}
		
		return $this->_paths[$filename];
	}	

	/**
	 * Return all the template paths
	 * 
	 * @return array
	 */
	public function getSearchPaths()
	{
		return $this->_search_paths;
	}
	
	/**
	 * Add a new search path. By the default the path is added to the top of the search path
	 *
	 * @param string|array The path(s) to add.
     * 
	 * @return  KTemplateAbstract
	 */
	public function addSearchPath($paths, $append = false)
	{
		settype($paths, 'array');
		
		foreach($paths as $path) 
		{
            if ( empty($path) ) {
                continue;
            }
                
			if ( in_array($path, $this->_search_paths) )
				continue;
							
			if ( $append )
				$this->_search_paths[] = $path;
			else {
				array_unshift($this->_search_paths, $path);
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
        $path = $this->_contents;
        
        if ( !isset($this->_parsed_data[$path]) ) {
        	$this->_parsed_data[$path] = parent::parse();
        }
        
        return $this->_parsed_data[$path];
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
		return end($this->_load_stack);
	}	
}