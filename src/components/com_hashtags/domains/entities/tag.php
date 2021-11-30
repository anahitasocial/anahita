<?php

/**
 * Hashtag association.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2014 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
final class ComHashtagsDomainEntityTag extends ComTagsDomainEntityTag
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
                'hashtag' => 'nodeA',
            ),
            'relationships' => array(
                'hashtag' => array('parent' => 'com:hashtags.domain.entity.hashtag'),
            ),
        ));

        parent::_initialize($config);
    }

    /**
     * After entity insert reset stats.
     *
     * AnCommandContext $context Context
     */
    protected function _afterEntityInsert(AnCommandContext $context)
    {
        $this->resetStats();
    }

    /**
     * After entity delete reset stats.
     *
     * AnCommandContext $context Context
     */
    protected function _afterEntityDelete(AnCommandContext $context)
    {
        $this->resetStats();
    }

    /**
     * Resets the hashtag.
     *
     * AnCommandContext $context Context
     */
    private function resetStats()
    {
        $this->hashtag->resetStats(array($this->hashtag));

        if (count($this->hashtag->taggables) === 0) {
            $this->hashtag->delete();
        }
    }
}
