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
        $items = array();

        $scopes = clone $this->getService('com://site/components.domain.entityset.scope');

        foreach ($scopes as $scope) {
           if ($scope->type === 'actor') {
              $items[] = $scope;
           }
        }

        $this->set(array(
            'items' => $items
        ));
    }
}
