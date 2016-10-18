<?php

/**
 * Actorbar.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComArticlesControllerToolbarActorbar extends ComMediumControllerToolbarActorbar
{
    /**
     * Before controller action.
     *
     * @param KEvent $event Event object
     *
     * @return string
     */
    public function onBeforeControllerGet(KEvent $event)
    {
        parent::onBeforeControllerGet($event);

        $viewer = get_viewer();
        $actor = pick($this->getController()->actor, $viewer);
        $layout = pick($this->getController()->layout, 'default');
        $name = $this->getController()->getIdentifier()->name;

        $this->setTitle(AnTranslator::sprintf('COM-ARTICLES-ACTOR-HEADER-'.strtoupper($name).'S', $actor->name));

        //create navigations
        $this->addNavigation('articles',
                        AnTranslator::_('COM-ARTICLES-LINK-ARTICLES'),
                        array(
                            'option' => 'com_articles',
                            'view' => 'articles',
                            'oid' => $actor->id, ),
                        $name == 'article');
    }
}
