<?php

/** 
 * LICENSE: ##LICENSE##.
 * 
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @version    SVN: $Id$
 *
 * @link       http://www.GetAnahita.com
 */

/**
 * Story Template Helper.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComStoriesTemplateHelperStory extends LibBaseTemplateHelperAbstract
{
    /**
     * Renders a set of names for actors.
     * 
     * @param ComActorsDomainEntityActor|array $actor
     *
     * @return string
     */
    public function actorName($actor, $truncate_after = 1)
    {
        $helper = KService::get('com:actors.template.helper');

        if (is_array($actor)) {
            $actors = $actor;
            $left_over = count($actors) - $truncate_after;

            if (!$truncate_after || $left_over <= 1) {
                $last_actor = $helper->name(array_pop($actors));

                if (!empty($actors)) {
                    $name = implode(', ', array_map(array($helper, 'name'), $actors));
                    $name = sprintf(AnTranslator::_('COM-STORIES-AND-ACTOR'), $name, $last_actor);
                } else {
                    $name = $last_actor;
                }

                return $name;
            }

            $ids = array_map(function ($actor) {
                        return 'ids[]='.$actor->id;
                    }, $actors);

            $ids = implode('&', $ids);

            $actors = array_splice($actors, 0, $truncate_after);
            $actors = implode(', ', array_map(array($helper, 'name'), $actors));
            $actors = sprintf(AnTranslator::_('COM-STORIES-AND-OTHERS'), $actors, route('option=com_actors&layout=modal&view=actors&'.$ids), AnTranslator::_($left_over));

            return $actors;
        }

        return $helper->name($actor);
    }

    /**
     * Return a possessive noune.
     * 
     * @param ComStoriesDomainEntityStory $story Story
     * @param ComActorsDomainEntityActor  $actor Actor
     * 
     * @return string
     */
    public function possessiveNoune($story, $actor)
    {
        if (is_array($actor) || empty($actor)) {
            $value = AnTranslator::_('LIB-AN-THEIR');
        } else {
            if ($actor->eql($story->subject)) {
                $value = AnTranslator::_(KService::get('com:actors.template.helper')->noune($actor, array('type' => 'possessive')));
            } elseif ($actor->eql(get_viewer())) {
                $value = AnTranslator::_('LIB-AN-YOUR');
            } else {
                $value = sprintf(AnTranslator::_('LIB-AN-THIRD-PERSON\'S'), $this->actorName($actor));
            }
        }

        return $value;
    }

    /**
     * Returns an HTML link to a node URL.
     * 
     * @param ComBaseDomainEntityNode $node
     * @param string                  $query
     *
     * @return string
     */
    public function link($node, $query = '')
    {
        if (is_array($node)) {
            $links = array();
            $nodes = $node;
            foreach ($nodes as $node) {
                $links[] = $this->link($node, $query);
            }

            return implode(', ', $links);
        }

        return '<a href="'.route($node->getURL()).$query.'">'.$node->getName().'</a>';
    }
}
