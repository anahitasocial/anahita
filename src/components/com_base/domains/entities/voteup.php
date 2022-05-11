<?php

/**
 * Represents a vote node up on voted object.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahita.io>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class ComBaseDomainEntityVoteup extends ComBaseDomainEntityEdge
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
            'aliases' => array(
                'voter' => 'nodeA',
                'votee' => 'nodeB', ), ));

        parent::_initialize($config);
    }

    /**
     * Resets the votable stats.
     *
     * AnCommandContext $context Context
     */
    protected function _afterEntityInsert(AnCommandContext $context)
    {
        $this->votee->getRepository()
                    ->getBehavior('votable')
                    ->resetStats(array($this->votee));
    }

    /**
     * Resets the votable stats.
     *
     * AnCommandContext $context Context
     */
    protected function _afterEntityDelete(AnCommandContext $context)
    {
        $this->votee->getRepository()
                    ->getBehavior('votable')
                    ->resetStats(array($this->votee));
    }
}
