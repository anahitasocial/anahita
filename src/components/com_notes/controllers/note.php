<?php

/**
 * Post Controller.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComNotesControllerNote extends ComMediumControllerDefault
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
            'behaviors' => array('com:shares.controller.behavior.sharable'),
        ));

        parent::_initialize($config);
    }

    /**
     * Adds a new post.
     *
     * @param KCommandContext $context Context parameter
     */
    protected function _actionAdd($context)
    {
        $data = $context->data;

        $entity = parent::_actionAdd($context);

        //if a person posting a message on his profile
        //or if a target is not actor then it can't be a private message
        if (get_viewer()->eql($this->actor) || !is_person($this->actor)) {
            unset($data->private);
        }

        //if a private message then
        //set the privacy to subject/target
        if ($data->private) {
            $entity->setAccess(array($this->actor->id, get_viewer()->id));
        }

        //create a notification for the subscribers and
        //the post owner as well
        if ($entity->owner->isSubscribable()) {
            //create a notification and pass the owner
            $notification = $this->createNotification(array(
                'name' => 'note_add',
                'object' => $entity,
                'subscribers' => array($entity->owner->subscriberIds->toArray(), $entity->owner),
            ))->setType('post', array('new_post' => true));
        }

        if (!empty($data['channels'])) {
            $this->shareObject(array(
                'object' => $entity,
                'sharers' => $data['channels']
            ));
        }

        return $entity;
    }
}
