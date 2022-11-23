<?php

/**
 * Hashtaggable Behavior.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahita.io>
 * @copyright  2008 - 2015 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class ComHashtagsControllerBehaviorHashtaggable extends AnControllerBehaviorAbstract
{
    /**
     * Constructor.
     *
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    public function __construct(AnConfig $config)
    {
        parent::__construct($config);

        $this->registerCallback('after.add', array($this, 'addHashtagsFromBody'));
        $this->registerCallback('after.edit', array($this, 'updateHashtagsFromBody'));
    }

    /**
     * Extracts hashtag terms from the entity body and add them to the item.
     */
    public function addHashtagsFromBody()
    {
        $entity = $this->getItem();
        $terms = $this->extractHashtagTerms($entity->body);

        foreach ($terms as $term) {
            $entity->addHashtag(trim($term));
        }
    }

    /**
     * Extracts hashtag terms from the entity body and updates the entity.
     *
     * @param AnCommandContext $context
     *
     * @return void
     */
    public function updateHashtagsFromBody(AnCommandContext $context)
    {
        $entity = $this->getItem();
        $terms = $this->extractHashtagTerms($entity->body);
        
        if (is_array($terms)) {
            foreach ($entity->hashtags as $hashtag) {
                if (!in_array(strtolower($hashtag->name), array_map('strtolower', $terms))) {
                    $entity->removeHashtag($hashtag->name);
                }
            }
        }

        foreach ($terms as $term) {
            $entity->addHashtag(trim($term));
        }
    }

    /**
     * extracts a list of hashtag terms from a given text.
     *
     * @return array
     */
    public function extractHashtagTerms($text)
    {
        $matches = array();

        if (preg_match_all(ComHashtagsDomainEntityHashtag::PATTERN_HASHTAG, $text, $matches)) {
            return array_unique($matches[1]);
        } else {
            return array();
        }
    }

    /**
     * Applies the hashtag filtering to the browse query.
     *
     * @param AnCommandContext $context
     */
    protected function _beforeControllerBrowse(AnCommandContext $context)
    {
        if (!$context->query) {
            $context->query = $this->_mixer->getRepository()->getQuery();
        }

        if ($this->hashtag) {
            $query = $context->query;
            $hashtags = array();
            $entityType = AnInflector::singularize($this->_mixer->getIdentifier()->name);
            $this->hashtag = (is_string($this->hashtag)) ? array($this->hashtag) : $this->hashtag;

            $edgeType = 'ComTagsDomainEntityTag,ComHashtagsDomainEntityTag,com:hashtags.domain.entity.tag';
            
            $query
            ->join('left', 'edges AS hashtag_edge', '('.$entityType.'.id = hashtag_edge.node_b_id AND hashtag_edge.type=\''.$edgeType.'\')')
            ->join('left', 'nodes AS hashtag', 'hashtag_edge.node_a_id = @col(hashtag.id)');

            foreach ($this->hashtag as $hashtag) {
                $hashtag = $this->getService('com:hashtags.filter.hashtag')->sanitize($hashtag);
                if ($hashtag != '') {
                    $hashtags[] = $hashtag;
                }
            }

            $query
            ->where('@col(hashtag.name)', 'IN', $hashtags)
            ->group($entityType.'.id');

            // error_log(str_replace('#_', 'jos', $query));
        }
    }
}
