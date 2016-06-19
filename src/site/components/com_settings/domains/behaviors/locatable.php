<?php

/**
 * Locatable behavior
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008-2016 rmd Studio Inc.
 * @license    GNU GPLv3
 *
 * @link       http://www.GetAnahita.com
 */
class ComSettingsDomainBehaviorLocatable extends AnDomainBehaviorAbstract
{
    /**
     * Returns the resource URL.
     *
     * @return string
     */
    public function getURL()
    {
        if (!isset($this->_url)) {
            $this->_url = 'option=com_settings&view='.$this->_mixer->getIdentifier()->name;

            if ($this->_mixer->id) {
                $this->_url .= '&id='.$this->_mixer->id;
            }
        }

        return $this->_url;
    }
}
