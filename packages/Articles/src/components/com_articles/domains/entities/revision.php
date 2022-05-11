<?php

/**
 * Revision Entity.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahita.io>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class ComArticlesDomainEntityRevision extends ComMediumDomainEntityMedium
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
            'behaviors' => array(
                'parentable' => array('parent' => 'article'),
            ),
            'attributes' => array(
                'name' => array(
                    'required' => AnDomain::VALUE_NOT_EMPTY,
                    'format' => 'string',
                    'length' => array(
                        'max' => 100,
                    )
                ),
                'excerpt' => array(
                    'format' => 'string',
                    'length' => array(
                        'max' => 1000,
                    )
                ),
                'body' => array(
                    'required' => AnDomain::VALUE_NOT_EMPTY,
                    'format' => 'html',
                    'length' => array(
                        'max' => 40000,
                    )
                ),
                'revisionNum' => 'ordering',
            ),
            'aliases' => array(
                'title' => 'name',
            ),
        ));

        parent::_initialize($config);

        AnHelperArray::unsetValues($config->behaviors, array('commentable', 'subscribable', 'hashtaggable'));
    }

//end class
}
