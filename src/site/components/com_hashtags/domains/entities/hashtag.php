<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Hashtags
 * @subpackage Domain_Entity
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2014 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.GetAnahita.com
 */

/**
 * A hashtag
 * 
 * @category   Anahita
 * @package    Com_Hashtags
 * @subpackage Domain_Entity
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.GetAnahita.com
 */
final class ComHashtagsDomainEntityHashtag extends ComBaseDomainEntityNode
{
    /*
     * hashtag regex pattern
     */
	const PATTERN_HASHTAG = '/#([A-Za-z0-9]{3,})/';
    
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
            'attributes' => array(
                'name' => array('required'=>AnDomain::VALUE_NOT_EMPTY, 'format'=>'string','read'=>'public', 'unique'=>true),
        		'body' => array('format'=>'string'),
        		'hashtagableCount' => array('default'=>0,'write'=>'private'),
        		'hashtagableIds' => array('type'=>'set', 'default'=>'set','write'=>'private')
            ),
			'behaviors'  => to_hash(array(
				'modifiable',
				'describable'
			)),
			'relationships' => array(
                'hashtagables' => array(
                    'through' => 'com:hashtags.domain.entity.association',                    
                    'child_key' => 'hashtag',
                    'target' => 'com:hashtags.domain.entity.hashtag',
                    'target_child_key' => 'hashtagable'
                )
            )
        ));
        
        parent::_initialize($config);
    }
    
	/**
     * Update stats
     * 
     * @return void
     */
    public function resetStats(array $entities)
    {
    	foreach($entities as $entity)
   		{
    		$ids = $this->getService('repos://site/hashtags.association')->getQuery()->hashtag($entity)->disableChain()->fetchValues('hashtagable.id');
   			$entity->set('hashtagableIds', AnDomainAttribute::getInstance('set')->setData($ids));
   			$entity->set('hashtagableCount', count($ids));
   			$entity->timestamp();
   		}
    }
}