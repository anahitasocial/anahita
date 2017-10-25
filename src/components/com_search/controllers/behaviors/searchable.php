<?php

/**
 * Searchable behavior. The searchable behavior allows for other conrollers to integrate into the
 * setup the scope and owner for the search layout.
 *
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComSearchControllerBehaviorSearchable extends AnControllerBehaviorAbstract
{
    /**
     * Sets the search owner context and scope automatically.
     */
    protected function _afterControllerGet()
    {
        if ($this->_mixer->isIdentifiable() && $this->isDispatched()) {
            $item = $this->_mixer->getItem();
            $scope = $this->_mixer->getIdentifier()->package.'.'.$this->_mixer->getIdentifier()->name;
            $scope = $this->getService('com:components.domain.entityset.scope')->find($scope);

            if ($scope) {
                $this->getService()->set('com:search.scope', $scope);
            }

            if ($item && $item->persisted() && $item->inherits('ComActorsDomainEntityActor')) {
                $this->getService()->set('com:search.owner', $item);
                $this->getService()->set('com:components.scope', null);
            } elseif ($this->getRepository()->isOwnable() && $this->actor) {
                $this->getService()->set('com:search.owner', $this->actor);
            }
        }
    }
}
