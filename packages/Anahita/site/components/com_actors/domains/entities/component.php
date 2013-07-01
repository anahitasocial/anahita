<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Actors
 * @subpackage Domain_Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: view.php 13650 2012-04-11 08:56:41Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Actor component. 
 *
 * @category   Anahita
 * @package    Com_Actors
 * @subpackage Domain_Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComActorsDomainEntityComponent extends ComComponentsDomainEntityComponent
{
    /**
     * Story aggregation
     *
     * @var array
     */
    protected $_story_aggregation;
    
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
    
        $this->_story_aggregation = $config['story_aggregation'];
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
	        'story_aggregation' => array(),
			'behaviors' => array(
				//'assignable'=>array(),
				'searchable'=>array('class'=>'ComActorsDomainEntityActor','type'=>'actor')
			)
		));
	
		parent::_initialize($config);
	}
	
	/**
	 * Called on when the stories are being aggregated
	 *
	 * @param KEvent $event
	 *
	 * @return boolean
	 */
	public function onStoryAggregation(KEvent $event)
	{	    
	    if ( !empty($this->_story_aggregation) ) 
	    {
	        $event->aggregations->append(array(
	            $this->component => $this->_story_aggregation
            ));
	    }
	}
}