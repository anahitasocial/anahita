<?php

 /**
  * Hashtaggable Behavior.
  *
  * @category   Anahita
  *
  * @author     Rastin Mehr <rastin@anahita.io>
  * @copyright  2008 - 2014 rmdStudio Inc.
  * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
  *
  * @link       http://www.Anahita.io
  */
 class ComHashtagsDomainBehaviorHashtaggable extends AnDomainBehaviorAbstract
 {
     /**
     * Initializes the default configuration for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    protected function _initialize(AnConfig $config)
    {
        $identifier = $config->mixer->getIdentifier()->identifier;
        $config->append(array(
            'relationships' => array(
                'hashtags' => array(
                    'through' => 'com:hashtags.domain.entity.tag',
                    'target' => $config->target ? $config->target : 'com:base.domain.entity.node',
                    'child_key' => 'taggable',
                    'target_child_key' => 'hashtag',
                    'inverse' => true,
                ),
            ),
        ));

        parent::_initialize($config);
    }

    /**
     * Adds a hashtag to a hashtaggable mixer entity.
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
        
        $hashtag = $this->getService('repos:hashtags.hashtag')
                        ->findOrAddNew(array('name' => $term));

        if ($hashtag) {
            if (! $this->hashtags->find($hashtag)) {
                $this->hashtags->insert($hashtag);
                return $this;
            }
        }

        return;
    }

    /**
     * Removes a hashtag from a hashtaggable mixer entity.
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

        $hashtag = $this->getService('repos:hashtags.hashtag')
                        ->find(array('name' => $term));

        if ($hashtag) {
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
