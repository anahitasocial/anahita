<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Plugins
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

jimport('joomla.plugin.plugin');

/**
 * Debug Plugin
 * 
 * @category   Anahita
 * @package    Plugins
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class  PlgSystemDebug extends JPlugin
{    
    /**
     * Logger
     * 
     * @return JLog
     */
    protected $_logger;
    
    /**
     * An array of strings to be logged
     * 
     * @var array
     */
    protected $_logs;
    
    /**
     * Constructor
     * 
     * @param mixed $subject Dispatcher
     * @param array $config  Array of configuration
     * 
     * @return void
     */
    public function __construct($subject, $config = array())
    {
		parent::__construct($subject, $config);
        
        if ( !JDEBUG )
            return;
            
        $this->_logs = new ArrayObject();
        
        $subscriber = KService::get('plg:system.debug.logger', array('logs'=>$this->_logs));
        
        KService::setConfig('anahita:domain.store.database', array(
            'event_subscribers' => array($subscriber)
        )); 
        
        jimport('joomla.error.log');
        
        $this->_logger = JLog::getInstance('debug.php');
	} 
    
	/**
	* Rendres the DEBUG in the file
	*
	*/
    public function __destruct()
	{
        if ( !JDEBUG )
            return;
                    
        $newline = '/ (WHEN|FROM|LEFT|INNER|OUTER|WHERE|SET|VALUES|ORDER|GROUP|HAVING|LIMIT|ON|AND) /i';
        
        global $_PROFILER, $mainframe;
        
        $this->_logs[] = implode("\n", $_PROFILER->getBuffer());
                
        $lang = &JFactory::getLanguage();
        $extensions = $lang->getPaths();
        $langs = array();
        foreach ( $extensions as $extension => $files)
        {
            foreach ( $files as $file => $status )
            {
                 $langs[] = "$file $status";
            }
        }
        
        $this->_logs[] = implode("\n", $langs);        
                    
        foreach($this->_logs as $key => $log)
        {
            $log = preg_replace($newline, "\n\\0", $log);
            $this->_logs[$key] = $log;   
        }
        $start  = str_pad("",100,'*');
        $logs   = $this->_logs->getArrayCopy();
        $logs   = "\n\n\n\n\n".$start."\n".$start."\n".$start."\n\n\n\n\n".implode("\n".str_pad("",40,'-')."\n",$logs);
        $logger =JLog::getInstance('debug.php');
        $logger->addEntry(array('comment'=>$logs));
	}
}

/**
 * Debug Plugin
 * 
 * @category   Anahita
 * @package    Plugins
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class  PlgSystemDebugLogger extends KEventSubscriberAbstract
{
    /**
     * Array object
     * 
     * @var ArrayObject
     */
    protected $_logs;
    
    /**
     * Stacks
     * 
     * @return KObjectStack
     */
    protected $_profilers;
    
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
        
        $this->_logs = $config->logs;
        
        $this->_profilers = $this->getService('koowa:object.stack');        
    }

    /**
     * Event Listener
     *
     * @param KEvent $event
     */
    public function onBeforeDomainStoreExecute(KEvent $event)
    {
        $this->_profilers->push(new JProfiler());
    }
    
    /**
     * Event Listener
     *
     * @param KEvent $event
     */
    public function onBeforeDomainStoreInsert(KEvent $event)
    {
        $this->_logs[] = "Inserting ".$event->repository->getIdentifier();
    }
    
    /**
     * Event Listener
     *
     * @param KEvent $event
     */
    public function onBeforeDomainStoreUpdate(KEvent $event)
    {
        $this->_logs[] = "Updating ".$event->repository->getIdentifier();
    }
    
    /**
     * Event Listener
     *
     * @param KEvent $event
     */
    public function onBeforeDomainStoreDelete(KEvent $event)
    {
        $this->_logs[] = "Deleting ".$event->repository->getIdentifier();
    }
                
    /**
     * Event Listener
     *
     * @param KEvent $event
     */
    public function onBeforeDomainStoreFetch(KEvent $event)
    {
        $this->_logs[] = "Selecting ".$event->repository->getIdentifier();        
        $this->_profilers->push(new JProfiler());
    }
    
    /**
     * Event Listener
     *
     * @param KEvent $event
     */
    public function onAfterDomainStoreFetch(KEvent $event)
    {       
        $query   = (string) $event->query;
        $query  .= "\n".$this->_profilers->pop()->mark('Time');
        $this->_logs[] = $query;
    }
    
    /**
     * Event Listener
     *
     * @param KEvent $event
     */
    public function onAfterDomainStoreExecute(KEvent $event)
    {
        $query   = (string) $event->query;       
        $query  .= "\n".$this->_profilers->pop()->mark('Time');
        $this->_logs[] = $query;
    }
}