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
 * @subpackage Controller_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2011 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Commitable behavior provides API to interace with domain context and storing
 * the last save result 
 *
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Controller_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class LibBaseControllerBehaviorCommittable extends KControllerBehaviorAbstract
{
    /**
     * Error in the save 
     * 
     * @var KException
     */
    protected $_error;
    
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
            'priority'   => KCommand::PRIORITY_HIGHEST,
          ));
        
        parent::_initialize($config);
    }
        
    /**
     * Command handler
     * 
     * @param string          $name    The command name
     * @param KCommandContext $context The command context
     * 
     * @return boolean     Can return both true or false.  
     */
    public function execute($name, KCommandContext $context)
    {
        $parts = explode('.', $name);
        
        //after an action save
        if ( $parts[0] == 'after' && $parts[1] != 'cancel') 
        {
            $result =  $this->commit();
            
            if ( $result === true ) 
            {
                //succesfull commit
                if ( empty($context->status) ) {
                    $context->status = $this->getSuccessStatus($context->action);                    
                }
            }
            elseif ( $result === false ) 
            {
                if ( !$context->getError() instanceof KException )
                {
                    $context->setError(new KControllerException(
                       $this->getError() ? $this->getError() : ucfirst($context->action).' Action Failed', KHttpResponse::INTERNAL_SERVER_ERROR
                    ));                    
                }
            }
            
            //create the message
            if (  !isset($context->status_message) && KRequest::method() != 'GET' )  
            {
                if ( $context->getError() ) 
                    $type = 'error';
                else
                    $type = 'success';
                    
                $context->status_message = $this->_buildMessage($context->action, $type);
            }            
        }
    }

    /**
     * Get a response status for an action
     *
     * @param string $action The action name to get a response code for
     * 
     * @return int
     */
    public function getSuccessStatus($action)
    {
        switch($action)
        {
            case 'add'         : return KHttpResponse::CREATED; break;
            case 'delete'      : return KHttpResponse::NO_CONTENT; break;
            case 'edit'        : return KHttpResponse::RESET_CONTENT; break;
            default            : return KHttpResponse::OK; break;
        }
    }
    
    /**
     * Validate the context
     * 
     * @param KCommandContext|null $context Context parameter. Can be null
     * 
     * @return boolean
     */
    protected function _validate($context = null)
    {
        if ( !$context )
                $context = new KCommandContext();

        $space = $this->getRepository()->getSpace();
        
        if ( count($space->getCommitables()) > 0 )
        {
            //reset error if there are commitables
            $this->_error = null;
            
            if ( $space->validate($context) === false ) 
            {
                $this->_error = $context->getError();
                return false;
            }
        }
    }    
    
    /**
     * Saves the context
     * 
     * @param KCommandContext|null $context Context parameter. Can be null
     * 
     * @return boolean
     */
    public function commit($context = null)
    {
        if ( !$context )
                $context = new KCommandContext();

        $space = $this->getRepository()->getSpace();
        
        if ( count($space->getCommitables()) > 0 )
        {
            //reset error if there are commitables
            $this->_error = null;
            
            if ( $space->commit($context) === false ) 
            {
                $this->_error = $context->getError();
                return false;
            }
            return true;
        }
    }

    /**
     * Return the last save error as KException
     * 
     * @return KException
     */
    public function getError()
    {
        return $this->_error;
    }

    /**
     * Render a message for an action
     *
     * @param string $action The action name whose message is being built
     * @param string $type   The type of the message. The type can be success, error or info
     * 
     * @return string Return the built message
     */
    protected function _buildMessage($action, $type = 'success')
    {        
        switch($action) {
            case 'add'   : $default = 'ADD'  ;break;
            case 'delete': $default = 'DELETE';break;
            default :
                $default = 'SAVE';
        }
        $messages    = array();
        $messages[]  = strtoupper('COM-'.$this->_mixer->getIdentifier()->package.'-PROMPT-'.$this->_mixer->getIdentifier()->name.'-'.$action.'-'.$type);
        $messages[]  = strtoupper('LIB-AN-PROMPT-'.$this->_mixer->getIdentifier()->name.'-'.$action.'-'.$type);
        $messages[]  = strtoupper('LIB-AN-PROMPT-'.$action.'-'.$type);
        $messages[]  = 'LIB-AN-PROMPT-'.$default.strtoupper('-'.$type);
        
        $message = translate($messages, false);
        
        return $message;
    }
        
    /**
     * Return the object handle
     * 
     * @return string
     */
    public function getHandle()
    {
        return KMixinAbstract::getHandle();
    }
}