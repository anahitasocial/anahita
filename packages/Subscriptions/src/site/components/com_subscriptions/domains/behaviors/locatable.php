<?php

/**
 * Locatable Behavior.
 *
 * Adds the method getURL that return a unique resource location for an entity
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComSubscriptionsDomainBehaviorLocatable extends AnDomainBehaviorAbstract
{
    /**
     * Returns the resource URL.
     *
     * @return string
     */
    public function getURL()
    {
        if (!isset($this->_url)) {
            $this->_url = 'option=com_subscriptions&view='.$this->_mixer->getIdentifier()->name;

            if ($this->_mixer->id) {
                $this->_url .= '&id='.$this->_mixer->id;
            }
        }

        return $this->_url;
    }
}
