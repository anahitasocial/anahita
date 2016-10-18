<?php

/**
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */

/**
 * Tagable Query.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComTagsDomainQueryNode extends AnDomainQueryDefault
{
    /**
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

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
     * Build the search query.
     */
    protected function _beforeQuerySelect()
    {
        if ($this->scope) {
            $this->scopes = $this->getService('com://site/components.domain.entityset.scope');
            if ($this->current_scope = $this->scopes->find($this->scope)) {
                $this->where('node.type', 'LIKE', '%'.$this->current_scope->identifier);
            }
        }
    }

    /**
     * Order by top ranked nodes.
     *
     * @return ComTagsDomainQueryNode
     */
    public function sortTop()
    {
        $this->order('(COALESCE(node.comment_count,0) + COALESCE(node.vote_up_count,0) + COALESCE(node.subscriber_count,0) + COALESCE(node.follower_count,0))', 'DESC')->groupby('tagable.id');

        return $this;
    }

    /**
     * Order by most recently created nodes.
     *
     * @return ComTagsDomainQueryNode
     */
    public function sortRecent()
    {
        $this->order('node.created_on', 'DESC');

        return $this;
    }
}
