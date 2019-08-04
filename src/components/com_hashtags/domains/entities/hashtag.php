<?php

/**
 * A hashtag.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2014 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
final class ComHashtagsDomainEntityHashtag extends ComTagsDomainEntityNode
{
    /*
     * hashtag regex pattern
     */
    const PATTERN_HASHTAG = '/(?![^<]*>)(?<=\W|^)#([^\d_\s\W][\p{L}\d\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}\x{1F680}-\x{1F6FF}]{2,})/u';

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
            'behaviors' => to_hash(array(
                'modifiable',
                'describable',
            )),
            'relationships' => array(
                'taggables' => array(
                    'through' => 'tag',
                    'child_key' => 'hashtag',
                    'target' => 'com:base.domain.entity.node',
                    'target_child_key' => 'taggable',
                ),
            ),
        ));

        parent::_initialize($config);
    }

    /**
     * Update stats.
     */
    public function resetStats(array $hashtags)
    {
        foreach ($hashtags as $hashtag) {
            $hashtag->timestamp();
        }
    }
}
