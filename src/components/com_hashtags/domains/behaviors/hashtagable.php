<?php

 /**
  * LICENSE: ##LICENSE##.
  *
  * @category   Anahita
  *
  * @author     Rastin Mehr <rastin@anahitapolis.com>
  * @copyright  2008 - 2014 rmdStudio Inc.
  * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
  *
  * @link       http://www.GetAnahita.com
  */

 /**
  * Hashtagable Behavior.
  *
  * @category   Anahita
  *
  * @author     Rastin Mehr <rastin@anahitapolis.com>
  * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
  *
  * @link       http://www.GetAnahita.com
  */
 class ComHashtagsDomainBehaviorHashtagable extends AnDomainBehaviorAbstract
 {
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
            'relationships' => array(
                'hashtags' => array(
                    'through' => 'com:hashtags.domain.entity.tag',
                    'target' => 'com:base.domain.entity.node',
                    'child_key' => 'tagable',
                    'target_child_key' => 'hashtag',
                    'inverse' => true,
                ),
            ),
        ));

        parent::_initialize($config);
    }

    /**
     * Adds a hashtag to a hashtagable mixer entity.
     *
     * @param a word
     */
    public function addHashtag($term)
    {
        $term = trim($term);

        //@todo implement is_hashtag filter method
        if (!is_string($term)) {
            return;
        }

        if ($hashtag = $this->getService('repos:hashtags.hashtag')->findOrAddNew(array('name' => $term))) {
            $this->hashtags->insert($hashtag);

            return $this;
        }

        return;
    }

    /**
     * Removes a hashtag from a hashtagable mixer entity.
     *
     * @param a word
     */
    public function removeHashtag($term)
    {
        $term = trim($term);

        //@todo implement is_hashtag filter method
        if (!is_string($term)) {
            return;
        }

        if ($hashtag = $this->getService('repos:hashtags.hashtag')->find(array('name' => $term))) {
            $this->hashtags->extract($hashtag);

            return $this;
        }

        return;
    }

    /**
     * Change the query to include name.
     *
     * Since the target is a simple node. The name field is not included. By ovewriting the
     * tags method we can change the query to include name in the $taggable->tags query
     *
     * @return AnDomainEntitySet
     */
    public function getHashtags()
    {
        $this->get('hashtags')->getQuery()->select('name');

        return $this->get('hashtags');
    }
 }
