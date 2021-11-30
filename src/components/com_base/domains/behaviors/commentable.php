<?php

/**
 * Commentable Behavior.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class ComBaseDomainBehaviorCommentable extends AnDomainBehaviorAbstract
{
    /**
     * Comment sanitizer.
     *
     * @var array
     */
    protected $_comment_sanitizer;

    /**
     * Constructor.
     *
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    public function __construct(AnConfig $config)
    {
        parent::__construct($config);
        $this->_comment_sanitizer = $config->comment;
    }

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
            'attributes' => array(
                'openToComment' => array(
                    'column' => 'comment_status',
                    'default' => true,
                    'write' => 'protected', ),
                'numOfComments' => array(
                    'column' => 'comment_count',
                    'default' => 0,
                    'write' => 'private', ),
                'lastCommentTime' => array(
                    'column' => 'last_comment_on',
                    'write' => 'private', ),
            ),
            'relationships' => array(
                'lastComment' => array(
                    'parent' => 'comment',
                    'write' => 'private', ),
                'lastCommenter' => array(
                    'parent' => 'com:people.domain.entity.person',
                    'child_column' => 'last_comment_by',
                    'write' => 'private', ),
                'comments' => array(
                    'child' => 'comment',
                    'child_key' => 'parent',
                    'parent_delete' => 'ignore', ),
            ),
            'comment' => array(),
        ));

        parent::_initialize($config);
    }

    /**
     * Initialize a new node.
     */
    protected function _afterEntityInstantiate(AnConfig $config)
    {
        $entity = $config->entity;

        //set the last commentor to the author
        if ($this->getService()->has('com:people.viewer')) {
            $config->append(array(
                'data' => array(
                    'lastCommenter' => $this->getService('com:people.viewer'),
                ),
            ));
        }
    }

    /**
     * Return a comment offset with id $cid from a list of comments of the commentable.
     *
     * @param int $cid The comment id
     *
     * @return int The offset of the comment
     */
    public function getCommentOffset($cid)
    {
        $this->getRepository()->getStore()->execute('set @i = 0');
        $query = clone $this->comments->getQuery();

        return $query->where('@col(id) < '.(int) $cid)->fetchValue('MAX(@i := @i + 1)');
    }

    /**
     * Adds a comment.
     *
     * @param string|ComBaseDomainEntityComment $comment The comment to add
     *
     * @return ComBaseDomainEntityComment
     */
    public function addComment($comment)
    {
        if (is_string($comment)) {
            $comment = AnHelperString::trim($comment);

            $comment = array(
                'author' => get_viewer() ,
                'body' => $comment,
                'component' => $this->component,
            );
        }

        $comment = $this->_mixer->comments->addNew($comment);

        if ($this->_mixer->isSubscribable() && !$this->_mixer->subscribed($comment->author)) {
            $this->_mixer->addSubscriber($comment->author);
        }

        return $comment;
    }

    /**
     * Sanitize an array of comments.
     *
     * @param array $comments The comments to sanitize
     */
    public function sanitizeComments($comments)
    {
        $sanitizers = $this->_comment_sanitizer;

        if (isset($sanitizers['format'])) {
            $sanitizers['format'] = $this->_repository
                                         ->getValidator()
                                         ->getFilter($sanitizers['format']);
        }

        foreach ($comments as $comment) {
            $comment->sanitizeData('body', $sanitizers);
        }
    }

    /**
     * Reset comment stats.
     *
     * @param array $entities Entities to reset the stats
     */
    public function resetStats(array $entities)
    {
        foreach ($entities as $entity) {
            $entity->set('numOfComments', $entity->comments->getTotal());
            $last_comment = $entity->comments
                                   ->reset()
                                   ->order('creationTime', 'DESC')
                                   ->fetch();
            $entity->set('lastComment', $last_comment);

            if ($last_comment) {
                $entity->set('lastCommenter', $last_comment->author);
                $entity->set('lastCommentTime', $last_comment->creationTime);
            } else {
                $entity->set('lastCommenter', null);
                $entity->set('lastCommentTime', null);
            }
        }
    }
}
