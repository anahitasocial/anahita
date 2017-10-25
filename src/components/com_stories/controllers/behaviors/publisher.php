<?php

/**
 * LICENSE: ##LICENSE##.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2011 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 *
 * @link       http://www.GetAnahita.com
 */

/**
 * Publisher Behavior. Publishes stories after an action.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComStoriesControllerBehaviorPublisher extends AnControllerBehaviorAbstract
{
    /**
     * Creates a story.
     *
     * @param array|KCommandContext $config Config. Can be a story data or KCommandContext if the method
     *                                      is used as a callback
     *
     * @return ComStoriesDomainEntityStory
     */
    public function createStory($config = array())
    {
        $config = new KConfig($config);

        $config->append(array(
            'subject' => get_viewer(),
            'owner' => get_viewer(),
            'component' => 'com_'.$this->_mixer->getIdentifier()->package,
        ));

        $story = $this->getService('repos:stories.story')->create($config->toArray());

        $story->save();

        return $story;
    }
}
