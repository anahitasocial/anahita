<?php

/**
 * Creates a story after creating a new comment.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahita.io>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class ComMediumControllerBehaviorCommentable extends ComBaseControllerBehaviorCommentable
{
    /**
     * Constructor.
     *
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    public function __construct(AnConfig $config)
    {
        parent::__construct($config);

        if ($this->_mixer->isPublisher()) {
            $this->registerCallback('after.addcomment', array($this, 'createCommentStory'));
        }
    }

    /**
     * Create a story after a comment.
     *
     * @param AnCommandContext $context
     */
    public function createCommentStory(AnCommandContext $context)
    {
        //called by the comment controller as as callback
        $entity = $context->comment;
        $parent = $context->comment->parent;

        $owner = $entity->author;

        if ($parent->isOwnable()) {
            $owner = $parent->owner;
        }

        $data = array(
            'name' => $parent->getIdentifier()->name.'_comment',
            'component' => $parent->component,
            'comment' => $entity,
            'object' => $parent,
            'owner' => $owner,
            'target' => $parent->isOwnable()  ? $parent->owner : null,
        );

        $story = $this->_mixer->createStory($data);

        if ($this->isNotifier()) {
            //story owner
            $data['subscribers'] = array($story->owner);

            //all the not subscribers
            if ($parent->isSubscribable()) {
                $data['subscribers'][] = $parent->subscriberIds->toArray();
            }

            $notification = $this->_mixer->createNotification($data);

            $notification->setType('post');
        }

        $story->save();
    }
}
