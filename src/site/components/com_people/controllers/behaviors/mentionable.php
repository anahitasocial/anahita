<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_People
 * @subpackage Controller_Behavior
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2014 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.GetAnahita.com
 */

/**
 * Mentionable Behavior
 *
 * @category   Anahita
 * @package    Com_Hashtags
 * @subpackage Controller_Behavior
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.GetAnahita.com
 */
class ComPeopleControllerBehaviorMentionable extends KControllerBehaviorAbstract 
{	
	/*
	 * contains the list of newly added mentions so they can be notified
	 * 
	 */
	protected $_newly_mentioned = array();
	
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
        
        $this->registerCallback('after.add', array($this, 'addMentionsFromBody'));
        $this->registerCallback('after.edit', array($this, 'updateMentionsFromBody'));
        
        $this->registerCallback(array('after.add', 'after.edit'), array($this, 'notifyMentioned'));
    }
	
	/**
	 * Extracts mention usernames from the entity body and add them to the item. 
	 *
	 * @return void
	 */
	public function addMentionsFromBody()
	{
		$entity = $this->getItem();
		$usernames = $this->extractMentions($entity->body);
		
    	foreach($usernames as $username)
        	$entity->addMention(trim($username));
        	
        $this->_newly_mentioned = $usernames;
	}
	
	/**
	 * Extracts mention usernames from the entity body and updates the entity 
	 *
	 * @param  KCommandContext $context
	 * @return boolean
	 */
	public function updateMentionsFromBody(KCommandContext $context)
	{
		$entity = $this->getItem();		
		$usernames = $this->extractMentions($entity->body);
		$existing_mentions = $entity->mentions;

		foreach($existing_mentions as $mention)
			if(!in_array($mention->username, $usernames))
       			$entity->removeMention($mention->username);	

       	$existing_mentions = KConfig::unbox($existing_mentions->username);
       	
       	foreach($usernames as $index=>$username)
       		if(in_array($username, $existing_mentions))	
       			unset($usernames[$index]);				
       			
       	foreach($usernames as $username)
        	$entity->addMention(trim($username));

       	$this->_newly_mentioned = $usernames;
	}
	
	/**
	 * extracts a list of mention usernames from a given text
	 * 
	 * @return array
	 */
	public function extractMentions($text)
	{
        $matches = array();
        
        if(preg_match_all(ComPeopleDomainEntityPerson::PATTERN_MENTION, $text, $matches))
        	return array_unique($matches[2]);
        else
        	return array();
	}
	
	/**
	 * Applies the hashtag filtering to the browse query
	 * 
	 * @param KCommandContext $context
	 */
	protected function _beforeControllerBrowse(KCommandContext $context)
	{				
		if(!$context->query)
            $context->query = $this->_mixer->getRepository()->getQuery(); 
    
		if($this->mention)
		{
			$query = $context->query;
			$usernames = array();
			$entityType = KInflector::singularize($this->_mixer->getIdentifier()->name);
			$this->mention = (is_string($this->mention)) ? array($this->mention) : $this->mention;
			
			$edgeType = 'ComTagsDomainEntityTag,ComPeopleDomainEntityMention,com:people.domain.entity.mention';
			
			$query
			->join('left', 'anahita_edges AS mention_edge', '('.$entityType.'.id = mention_edge.node_b_id AND mention_edge.type=\''.$edgeType.'\')')
			->join('left', 'anahita_nodes AS mention', 'mention_edge.node_a_id = mention.id');	
			
			foreach($this->mention as $mention)
			{
				$username = $this->getService('com://site/people.filter.username')->sanitize($mention);
				
				if($username != '')
					$usernames[] = $username;
			}
			
			$query
			->where('mention.person_username', 'IN', $usernames)
			->group($entityType.'.id');
			
			//print str_replace('#_', 'jos', $query);
		}
	}
	
	/**
	 * Notify the people who have been mentioned
	 * 
	 * @param KCommandContext $context
	 * 
	 * @return void
	 */
	public function notifyMentioned(KCommandContext $context)
	{
		$entity = $this->getItem();
		$subscribers = array();
		
		foreach($this->_newly_mentioned as $username)
		{
			$person = $this->getService('repos://site/people.person')->find(array('username'=>$username));
			
			if($person && $person->authorize('mention'))
				$subscribers[] = $person->id;
		}
		
		if(count($subscribers) == 0)
			return;
		
		if($entity instanceof ComBaseDomainEntityComment)
		{
			$parentIdentifier = $entity->parent->getIdentifier()->name;
			$parentController = $this->getService('com://site/'.KInflector::pluralize($parentIdentifier).'.controller.'.$parentIdentifier);
			
			if($parentController->isNotifier() && $entity->parent->isSubscribable())
			{
				$data = array(
					'name' => 'actor_mention_comment',
					'object' => $entity,
					'comment' => $entity,
					'component' => $entity->parent->component,
					'subscribers' => $subscribers
				);
				
				$parentController->createNotification($data);
			}
		}
		else
		{
			$data = array(
				'name' => 'actor_mention',
				'object' => $entity,
    			'component' => $entity->component,
				'subscribers' => $subscribers
			);
				
			$notification = $this->_mixer->createNotification($data);
		}
	}
}