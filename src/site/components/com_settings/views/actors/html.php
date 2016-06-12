<?php

/**
 * Default Actors View.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComSettingsViewActorsHtml extends ComBaseViewHtml
{
    /**
     * Default Layout.
     */
    protected function _layoutDefault()
    {
        $components = $this->getService('repos:components.component')
                           ->getQuery()
                           ->enabled(true)
                           ->order('name')
                           ->fetchSet();

        $items = array();
        foreach($components as $component) {
          $identifiers = $component->getEntityIdentifiers('ComActorsDomainEntityActor');
          foreach ($identifiers as $identifier) {
              $identifier->application = null;
              $items[] = $identifier;
          }
        }

        $apps = array();
        foreach($components as $component) {
           if ($component->isAssignable()) {
              $apps[] = $component;
           }
        }

        $this->set(array(
            'items' => $items,
            'apps' => $apps
        ));
    }
}
