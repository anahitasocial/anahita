<?php

/**
 * Search Query.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComSearchDomainQueryNode extends AnDomainQueryDefault
{
    /**
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        $this->distinct = true;

        //lets add all the common fields
        $this->select(array(
            'node.created_by',
            'node.owner_id',
            'node.owner_type',
            'node.name',
            'node.alias',
            'node.body',
            'node.created_on',
            'node.modified_on',
            'node.modified_by',
            'node.blocker_ids',
            'node.blocked_ids',
            'node.access',
            'node.follower_count',
            'node.leader_count',
            'node.parent_id',
            'node.parent_type',
            'node.filename'
        ));
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
            'repository' => 'repos:search.node'
        ));

        parent::_initialize($config);
    }

    /**
     * Build the search query.
     */
    protected function _beforeQuerySelect()
    {
        $keywords = $this->search_term;

        if ($keywords) {
            $operation = null;

            if (strpos($keywords, ' OR ')) {
                $operation = 'OR';
                $keywords = explode(' OR ', $keywords);
            } else {
                $operation = 'AND';

                if (strpos($keywords, ' AND ')) {
                    $keywords = explode(' AND ', $keywords);
                } else {
                    $keywords = explode(' ', $keywords);
                }
            }

            foreach ($keywords as $index => $value) {
                if (strlen($value) < 3) {
                    unset($keywords[$index]);
                }
            }

            $clause = $this->clause('AND');

            foreach ($keywords as $keyword) {
                $clause->where('CONCAT(IF(node.name IS NULL,"",node.name), IF(node.body IS NULL,"",node.body)) LIKE @quote(%'.$keyword.'%)', $operation);
            }
        }

        $scopes = $this->getService('com:components.domain.entityset.scope');

        if ($this->scope instanceof ComComponentsDomainEntityScope) {
            $scopes = array($this->scope);
        }

        $comments = array();
        $types = array();

        foreach ($scopes as $scope) {
            $types[] = $scope->node_type;

            if ($scope->commentable) {
                $comments[] = (string) $scope->identifier;
            }
        }

        $comment_query = '';

        if (count($comments) && $this->_state->search_comments) {
            $comment_query = 'OR (@col(node.type) LIKE :comment_type AND node.parent_type IN (:parent_types) )';
        }

        $owner_query = '';

        if ($this->owner_context) {
            $owner_query = 'node.owner_id = '.$this->owner_context->id.' AND ';

            if (!empty($comment_query)) {
                $this->distinct = true;
                $this->join('LEFT', 'nodes AS comment_parent', 'node.type LIKE :comment_type AND comment_parent.id = node.parent_id');
                $comment_query = preg_replace_callback('/\)$/', function($matches) { return ' AND comment_parent.owner_id = '.$this->owner_context->id.')'; }, $comment_query);
            }
        }

        $this->where('( '.$owner_query.' @col(node.type) IN (:types) '.$comment_query.')')
             ->bind('types', $types)
             ->bind('comment_type', 'ComBaseDomainEntityComment%')
             ->bind('parent_types', $comments);
    }

    /**
     * Order by relevance.
     *
     * @return ComSearchDomainQuerySearch
     */
    public function orderByRelevance()
    {
        $this->order('(COALESCE(node.comment_count,0) + COALESCE(node.vote_up_count,0) + COALESCE(node.subscriber_count,0) + COALESCE(node.follower_count,0))', 'DESC');

        return $this;
    }

    /**
     * Returns a entity set whose data has not been fetched yet.
     *
     * @return AnDomainEntitysetDefault
     */
    public function toEntitySet()
    {
        return KService::get('com:search.domain.entityset.node', array(
                  'query' => clone $this,
                  'repository' => $this->getRepository()
                ));
    }
}
