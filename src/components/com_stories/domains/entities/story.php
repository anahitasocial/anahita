<?php

/**
 * Story Entity.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class ComStoriesDomainEntityStory extends ComBaseDomainEntityNode
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
            'attributes' => array(
                'name' => array('required' => true),
                'body' => array('format' => 'string'),
            ),
            'relationships' => array(
                    'subject' => array(
                            'required' => true,
                            'parent' => 'com:actors.domain.entity.actor',
                            'child_column' => 'story_subject_id', ),
                    'target' => array(
                            'parent' => 'com:actors.domain.entity.actor',
                            'child_column' => 'story_target_id', ),
                    'comment' => array(
                            'parent' => 'com:base.domain.entity.comment',
                            'child_column' => 'story_comment_id', ),
                    'object' => array(
                            'polymorphic' => true,
                            'type_column' => 'story_object_type',
                            'child_column' => 'story_object_id',
                            'parent' => 'com:base.domain.entity.node',
                        ),
             ),
             'behaviors' => array(
                'aggregatable',
                'authorizer',
                'modifiable',
                'ownable',
              ),
        ));

        parent::_initialize($config);
    }
}
