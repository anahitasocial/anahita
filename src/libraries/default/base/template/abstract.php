<?php
/**
 * @category   Anahita
 *
 * @author	   Johan Janssens <johan@nooku.org>
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @copyright  Copyright (C) 2018 rmd Studio Inc.
 * @copyright  Copyright (C) 2010 PeerGlobe Technology Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
abstract class LibBaseTemplateAbstract extends KObject
{
    /**
     * The template data
     *
     * @var array
     */
    protected $_data = array();
    
    /**
     * Array of search path.
     *
     * @var array
     */
    protected $_search_paths = array();

    /**
     * The template load stack.
     *
     * @var array
     */
    protected $_load_stack = array();

    /**
     * Array of helpers.
     *
     * @var array
     */
    protected $_helpers = array();

    /**
     * The template path
     *
     * @var string
     */
    protected $_path;

    /**
     * Contains the paths for the template.
     *
     * @var array
     */
    protected $_paths = array();

    /**
     * stores the parsed data for each path.
     *
     * @var array
     */
    protected $_parsed_data;
    
    /**
     * The template contents
     *
     * @var string
     */
    protected $_contents = '';
    
    /**
     * The set of template filters for templates
     *
     * @var array
     */
    protected $_filters = array();
    
    /**
     * View object or identifier (com://APP/COMPONENT.view.NAME.FORMAT)
     *
     * @var	string|object
     */
    protected $_view;
    
    /**
     * The template stack object
     *
     * @var	LibBaseTemplateStack
     */
    protected $_stack;
    
    /**
     * Template errors
     *
     * @var array
     */
    private static $_errors = array(
        1 => 'Fatal Error',
        2 => 'Warning',
        4 => 'Parse Error',
        8 => 'Notice',
        64 => 'Compile Error',
        256 => 'User Error',
        512 => 'User Warning',
        2048 => 'Strict',
        4096 => 'Recoverable Error'
    );

    /**
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);
        
        // Set the view indentifier
        $this->_view = $config->view;
        
        // Set the template stack object
        $this->_stack = $config->stack;
            
        //Register the template stream wrapper
        LibBaseTemplateStream::register();
        
        //Set shutdown function to handle sandbox errors
        register_shutdown_function(array($this, '__destroy'));
        
         // Mixin a command chain
        $this->mixin(new KMixinCommand($config->append(array('mixer' => $this))));

        $this->_data = KConfig::unbox($config->data);

        $this->_parsed_data = new ArrayObject();
    }
    
    /**
     * Destructor
     *
     * Hanlde sandbox shutdown. Clean all output buffers and display the latest error
     * if an error is found.
     *
     * @return bool
     */
    public function __destroy()
    {
        if (!$this->getStack()->isEmpty()) {
            if ($error = error_get_last()) {
                if ($error['type'] === E_ERROR || $error['type'] === E_PARSE || $error['type'] === E_COMPILE_ERROR) {
                    while (@ob_get_clean());
                    $this->handleError($error['type'], $error['message'], $error['file'], $error['line']);
                }
            }
        }
    }

    /**
     * Initializes the default configuration for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'data' => array(),
            'stack' => $this->getService('com:base.template.stack'),
            'view' => null,
            'command_chain' => $this->getService('koowa:command.chain'),
            'dispatch_events' => false,
            'enable_callbacks' => false,
        ));

        parent::_initialize($config);
    }

    /**
     * Method to set a view object attached to the controller.
     *
     * @param	mixed	An object that implements KObjectServiceable, KServiceIdentifier object
     * 					or valid identifier string
     *
     * @return LibBaseTemplateAbstract
     */
    public function setView($view)
    {
        if (!($view instanceof LibBaseViewAbstract)) {
            if (is_string($view) && strpos($view, '.') === false) {
                $identifier = clone $this->getIdentifier();
                $identifier->path = array('view', $view);
                $identifier->name = 'html';
            } else {
                $identifier = $this->getIdentifier($view);
            }

            if ($identifier->path[0] != 'view') {
                throw new LibBaseTemplateException('Identifier: '.$identifier.' is not a view identifier');
            }

            $view = $identifier;
        }

        $this->_view = $view;

        return $this;
    }

    /**
     * Get the view object attached to the template.
     *
     * @return LibBaseViewAbstract
     */
    public function getView()
    {
        if (!$this->_view instanceof LibBaseViewAbstract) {
            //Make sure we have a view identifier
            if (!($this->_view instanceof KServiceIdentifier)) {
                $this->setView($this->_view);
            }

            $this->_view = $this->getService($this->_view);
        }

        return $this->_view;
    }

    /**
     * Load a template by path.
     *
     * @param   string 	The template path
     * @param	array	An associative array of data to be extracted in local template scope
     * @param	bool	If TRUE process the data using a tmpl stream. Default TRUE.
     *
     * @return LibBaseTemplateAbstract
     */
    public function loadFile($file, $data = array(), $process = true)
    {
        //tracks the recursive paths
        $this->_load_stack[] = $file;

        $data['__FILE__'] = $file;
        $data['__DIR__'] = dirname($file);

        // get the file contents
        $contents = file_get_contents($file);
        
        // load the contents
        $this->loadString($contents, $data, $process);

        array_pop($this->_load_stack);
        //the path
        $this->_path = end($this->_load_stack);

        return $this;
    }
    
    /**
     * Load a template from a string
     *
     * @param   string 	The template contents
     * @param	array	An associative array of data to be extracted in local template scope
     * @param	boolean	If TRUE process the data using a tmpl stream. Default TRUE.
     * @return LibBaseTemplateAbstract
     */
    public function loadString($string, $data = array(), $process = true)
    {
        $this->_contents = $string;
    
        // Merge the data
        $this->_data = array_merge((array) $this->_data, $data);
        
        // Process the data
        if ($process == true) {
            $this->__sandbox();
        }
    
        return $this;
    }
    
    /**
     * Render the template
     *
     * This function passes the template throught write filter chain and returns the
     * result.
     *
     * @return string	The rendered data
     */
    public function render()
    {
        $context = $this->getCommandContext();
        $context->data = $this->_contents;
                
        $result = $this->getCommandChain()->run(LibBaseTemplateFilter::MODE_WRITE, $context);
        
        return $context->data;
    }
    
    /**
     * Process the template using a simple sandbox
     *
     * This function passes the template through the read filter chain before letting
     * the PHP parser executed it. The result is buffered.
     *
     * @param  boolean 	If TRUE apply write filters. Default FALSE.
     * @return LibBaseTemplateAbstract
     */
    protected function __sandbox()
    {
        set_error_handler(array($this, 'handleError'), E_WARNING | E_NOTICE);
        $this->getStack()->push(clone $this);

        //Extract the data in local scope
           extract($this->_data, EXTR_SKIP);
           
           // Capturing output into a buffer
        ob_start();
        include 'tmpl://'.$this->getStack()->getIdentifier();
        $this->_contents = ob_get_clean();

        $this->getStack()->pop();
        restore_error_handler();
        
        return $this;
    }

    /**
     * Loads a template using the identifier by converting an identifier to a path. On the contrary to
     * LibBaseTemplateAbstract if a KServiceIdentifier is passed, it will not append the path directory as the
     * default path of $template->_search_paths.
     *
     * @param  KServiceIdentifier $template Template Identifier
     * @param  array              $data     Template data
     * @param  bool               $process  If TRUE process the data using a tmpl stream. Default TRUE.
     *
     * @return LibBaseTemplateAbstract
     */
    public function loadIdentifier($template, $data = array(), $process = true)
    {
        //Identify the template
        $identifier = $this->getIdentifier($template);

        //add the path to the template paths
        //@TODO should we do that or just try load the template path
        $path = dirname($identifier->filepath);

        if (!in_array($path, $this->_search_paths)) {
            array_unshift($this->_search_paths, $path);
        }

        //load the template
        return $this->loadTemplate($identifier->name, $data, $process);
    }

    /**
     * Loads a template by first trying to find the template file.
     *
     * @param string $template Template name
     * @param array  $data     Template data
     * @param bool   $process  If TRUE process the data using a tmpl stream. Default TRUE.
     *
     * @return string
     */
    public function loadTemplate($template, $data = array(), $process = true)
    {
        $path = $this->findTemplate($template);

        if (! $path) {
            unset($this->_paths[$template.'.php']);
            throw new LibBaseTemplateException($template.' template not found for '.$this->getIdentifier());
            return false;
        }

        return $this->loadFile($path, $data, $process);
    }

    /**
     * Load a template helper. On Contrary to Nooku, it allows for any number of
     * argument than just an array.
     *
     * @param	string	Name of the helper, dot separated including the helper function to call
     * @param	mixed	Parameters to be passed to the helper
     *
     * @return string Helper output
     */
    public function renderHelper($identifier, $config = array())
    {
        $args = func_get_args();
        $identifier = array_shift($args);

        //Get the function to call based on the $identifier
        $parts = explode('.', $identifier);
        $function = array_pop($parts);

        $helper = implode('.', $parts);

        $helper = $this->getHelper($helper);

        //Call the helper function
        if (!is_callable(array($helper, $function))) {
            throw new LibBaseTemplateHelperException(get_class($helper).'::'.$function.' not supported.');
        }

        return call_object_method($helper, $function, $args);
    }

    /**
     * Get a template helper.
     *
     * @param	mixed	An object that implements KObjectIdentifiable, an object that
     *                  implements KServiceIdentifierInterface or valid identifier string
     * @param	mixed	Parameters to be passed to the helper
     *
     * @return LibBaseTemplateHelperInterface
     */
    public function getHelper($helper, $config = array())
    {
        $name = (string) $helper;

        if (!isset($this->_helpers[$name])) {
            if (is_string($helper) && strpos($helper, '.') === false) {
                $identifier = clone $this->getIdentifier();
                $identifier->path = array('template','helper');
                $identifier->name = $helper;
            } else {
                $identifier = $this->getIdentifier($helper);
            }
         
            register_default(array(
                'identifier' => $identifier,
                'prefix' => $this
            ));
         
            //Create the template helper
            $helper = $this->getService($identifier, array_merge($config, array('template' => $this)));
            
            //Check the helper interface
            if (!($helper instanceof LibBaseTemplateHelperInterface)) {
                throw new LibBaseTemplateHelperException("Template helper $identifier does not implement LibBaseTemplateHelperInterface");
            }

            $this->_helpers[$name] = $helper;
        }

        return $this->_helpers[$name];
    }

    /**
     * Same as findPath except it automatically adds the .php extension.
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
     * Searches for the file
     *
     * @param	string	The file path to look for.
     * @return	mixed	The full path and file name for the target file, or FALSE
     * 					if the file is not found
     */
    public function findFile($file)
    {
        $result = false;
        $path   = dirname($file);
        
        // is the path based on a stream?
        if (strpos($path, '://') === false) {
            // not a stream, so do a realpath() to avoid directory
            // traversal attempts on the local file system.
            $path = realpath($path); // needed for substr() later
            $file = realpath($file);
        }

        // The substr() check added to make sure that the realpath()
        // results in a directory registered so that non-registered directores
        // are not accessible via directory traversal attempts.
        if (file_exists($file) && substr($file, 0, strlen($path)) == $path) {
            $result = $file;
        }

        // could not find the file in the set of paths
        return $result;
    }

    /**
     * @param	string			The file name to look for.
     *
     * @return mixed The full path and file name for the target file, or FALSE
     *               if the file is not found in any of the paths
     */
    public function findPath($filename)
    {
        if (!isset($this->_paths[$filename])) {
            foreach ($this->_search_paths as $path) {
                $file = $path.'/'.$filename;
                if ($this->findFile($file)) {
                    $this->_paths[$filename] = $file;
                    return $file;
                }
            }

            $this->_paths[$filename] = false;
        }

        return $this->_paths[$filename];
    }

    /**
     * Return all the template paths.
     *
     * @return array
     */
    public function getSearchPaths()
    {
        return $this->_search_paths;
    }

    /**
     * Add a new search path. By the default the path is added to the top of the search path.
     *
     * @param string|array The path(s) to add.
     *
     * @return LibBaseTemplateAbstract
     */
    public function addSearchPath($paths, $append = false)
    {
        settype($paths, 'array');

        foreach ($paths as $path) {
            if (empty($path)) {
                continue;
            }

            if (in_array($path, $this->_search_paths)) {
                continue;
            }

            if ($append) {
                $this->_search_paths[] = $path;
            } else {
                array_unshift($this->_search_paths, $path);
            }
        }
    }

    /**
     * Parse the data. If a data has been parsed, it will serve it from the cache.
     *
     * @return string The filtered data
     */
    public function parse()
    {
        $path = $this->_contents;

        if (!isset($this->_parsed_data[$path])) {
            $context = $this->getCommandContext();
            $context->data = $path;
            $this->getCommandChain()->run(LibBaseTemplateFilter::MODE_READ, $context);
            $this->_parsed_data[$path] = $context->data;
        }

        return $this->_parsed_data[$path];
    }
    
    /**
     * Check if a filter exists
     *
     * @param 	string	The name of the filter
     * @return  boolean	TRUE if the filter exists, FALSE otherwise
     */
    public function hasFilter($filter)
    {
        return isset($this->_filters[$filter]);
    }
    
    /**
     * Adds one or more filters for template transformation
     *
     * @param array 	Array of one or more behaviors to add.
     * @return LibBaseTemplate
     */
    public function addFilter($filters)
    {
        $filters = (array) KConfig::unbox($filters);

        foreach ($filters as $filter) {
            if (!($filter instanceof LibBaseTemplateFilterInterface)) {
                $filter = $this->getFilter($filter);
            }
            
            //Enqueue the filter in the command chain
            $this->getCommandChain()->enqueue($filter);
            
            //Store the filter
            $this->_filters[$filter->getIdentifier()->name] = $filter;
        }
        
        return $this;
    }

    /**
     * Get a filter by identifier.
     *
     * @return LibBaseTemplateFilterInterface
     */
    public function getFilter($filter)
    {
        //Create the complete identifier if a partial identifier was passed
        if (is_string($filter) && strpos($filter, '.') === false) {
            $identifier = clone $this->getIdentifier();
            $identifier->path = array('template', 'filter');
            $identifier->name = $filter;
        } else {
            $identifier = KService::getIdentifier($filter);
        }

        if (!isset($this->_filters[$filter])) {
            register_default(array(
                'identifier' => $identifier,
                'prefix' => $this
            ));
            
            $filter = KService::get($identifier);

            if (!($filter instanceof LibBaseTemplateFilterInterface)) {
                throw new LibBaseTemplateException("Template filter $identifier does not implement LibBaseTemplateFilterInterface");
            }
        } else {
            $filter = $this->_filters[$identifier->name];
        }

        return $filter;
    }

    /**
     * Handle template errors.
     *
     * @return bool
     */
    public function handleError($code, $message, $file = '', $line = 0, $context = array())
    {
        if ($file == 'tmpl://com:base.template.stack' || $code == 1) {
            if ($file == 'tmpl://com:base.template.stack') {
                $file = $this->getPath();
            }

            if (ini_get('display_errors')) {
                echo '<strong>'.$code.'</strong>: '.$message.' in <strong>'.$file.'</strong> on line <strong>'.$line.'</strong>';
            }

            if (ini_get('log_errors')) {
                error_log(sprintf('PHP %s:  %s in %s on line %d', $code, $message, $file, $line));
            }

            return true;
        }

        return false;
    }

    /**
     * Return the data used in the template.
     *
     * @return array
     */
    public function getPath()
    {
        return end($this->_load_stack);
    }
    
    /**
     * Get the template data
     *
     * @return	mixed
     */
    public function getData()
    {
        return $this->_data;
    }
    
    /**
     * Get the template object stack
     *
     * @return 	LibBaseTemplateStack
     */
    public function getStack()
    {
        return $this->_stack;
    }
    
    /**
     * Renders the template and returns the result
     *
     * @return 	string
     */
    public function __toString()
    {
        try {
            $result = $this->_contents;
        } catch (Exception $e) {
            $result = $e->getMessage();
        }
            
        return $result;
    }
}
