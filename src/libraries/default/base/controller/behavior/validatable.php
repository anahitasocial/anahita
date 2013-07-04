<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Controller_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2011 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Validatable Behavior. Validates data using the repository
 *
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Controller_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class LibBaseControllerBehaviorValidatable extends KControllerBehaviorAbstract
{	
    /**
     * Validator
     * 
     * @var string
     */
    protected $_validator;    
    
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
            
        ));
    
        parent::_initialize($config);
    }
    
    /**
     * Return the validator
     *
     * @return LibBaseControllerValidatorAbstract
     */
    public function getValidator()
    {
       if ( !$this->_validator instanceof LibBaseControllerValidatorAbstract )
       {
           //Make sure we have a view identifier
           if(!($this->_validator instanceof KServiceIdentifier)) {
               $this->setValidator($this->_validator);
           }
           
           $config = array(
                'controller' => $this->getMixer()
           );
           
           $this->_validator = $this->getService($this->_validator, $config);           
       }
       
       return $this->_validator;
    }
    
    /**
     * Sets the validator
     * 
     * @param string $validator
     * 
     * @return void
     */
    public function setValidator($validator)
    {
        if(!($validator instanceof LibBaseControllerValidatorAbstract))
        {
            if(is_string($validator) && strpos($validator, '.') === false )
            {
                $identifier         = clone $this->getIdentifier();
                $identifier->path   = array('controller', 'validator');
                $identifier->name   = $validator;
                register_default(array('identifier'=>$identifier, 'prefix'=>$this));
            }
            else $identifier = $this->getIdentifier($validator);
        
            if($identifier->path[1] != 'validator') {
                throw new KControllerBehaviorException('Identifier: '.$identifier.' is not a validator identifier');
            }
        
            $validator = $identifier;
        }
        
        $this->_validator = $validator;        
    }
    
    /**
     * Set the validator before the validate action is called
     *
     * @return void
     */
    protected function _beforeControllerValidate()
    {
        if ( !isset($this->_validator) )
            $this->setValidator($this->_mixer->getIdentifier()->name);
    }
    
	/**
	 * Validate Action. Calls the method validate<$key> on the validator object.
	 *
	 * @param KCommandContext $context Context parameter
	 * 
	 * @return void
	 */
	protected function _actionValidate($context)
	{
		$data 	   = $context->data;		
		$key	   = $data->key;
		$value	   = $data->value;		
		$method    = 'validate'.ucfirst($key);
		$result    = true;		
		$result    = $this->getValidator()->$method($value);
        $output    = $this->getValidator()->getMessage();
		if ( $result === false ) 
		{
			$context->response->status     = KHttpResponse::PRECONDITION_FAILED;
			if ( is_string($output)  ) 
			    $output = array('errorMsg'=>$output);
			$context->response->validation = json_encode($output);			
		} else {
		    $context->response->validation = json_encode($output);;
		}
	}	
}