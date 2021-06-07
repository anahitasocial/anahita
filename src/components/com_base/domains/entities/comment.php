<?php

/**
 * Comment Entity.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComBaseDomainEntityComment extends ComBaseDomainEntityNode
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
        $config->append(array(
            'inheritance' => array(
                'abstract' => $this->getIdentifier()->package == 'base', 
            ),
            'attributes' => array(
                'body' => array(
                    'required' => AnDomain::VALUE_NOT_EMPTY,
                    'length' => array(
                        'max' => 5000,
                    ), 
                ),
            ),
            'behaviors' => array(
                'parentable' => array(
                    'parent' => 'com:base.domain.entity.node', 
                ),
                'com:hashtags.domain.behavior.hashtaggable',
                'com:people.domain.behavior.mentionable',
                'modifiable',
                'authorizer',
                'locatable',
                'votable', ),
        ));

        parent::_initialize($config);
    }

    /**
     * Returns the URL for a comment.
     *
     * @return string
     */
    public function getURL()
    {
        return $this->parent->getURL().'&cid='.$this->id;
    }

    /**
     * Validating Entity.
     *
     * AnCommandContext $context Context
     */
    protected function _onEntityValidate(AnCommandContext $context)
    {
        $this->parent->getRepository()
                     ->getBehavior('commentable')
                     ->sanitizeComments(array($this));
    }

    /**
     * Resets the comment stats.
     *
     * AnCommandContext $context Context
     */
    protected function _afterEntityInsert(AnCommandContext $context)
    {
        $this->parent->getRepository()
                     ->getBehavior('commentable')
                     ->resetStats(array($this->parent));
        $this->parent->execute('after.comment', array('comment' => $this));
    }

    /**
     * Resets the comment stats.
     *
     * AnCommandContext $context Context
     */
    protected function _afterEntityDelete(AnCommandContext $context)
    {
        $this->parent->getRepository()
                     ->getBehavior('commentable')
                     ->resetStats(array($this->parent));
    }
}
