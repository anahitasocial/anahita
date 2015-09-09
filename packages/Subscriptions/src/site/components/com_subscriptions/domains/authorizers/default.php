<?php

/**
 * Default Medium Authorizer.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComSubscriptionsDomainAuthorizerDefault extends LibBaseDomainAuthorizerDefault
{
    /**
     * Check if a medium authorizes acccess.
     *
     * @param KCommandContext $context Context parameter
     *
     * @return bool
     */
    protected function _authorizeAccess($context)
    {
        return $this->_viewer->admin();
    }

    /**
     * Check if a node authroize being updated.
     *
     * @param KCommandContext $context Context parameter
     *
     * @return bool
     */
    protected function _authorizeAdministration($context)
    {
        return $this->_viewer->admin();
    }

    /**
     * Check if a node authroize being updated.
     *
     * @param KCommandContext $context Context parameter
     *
     * @return bool
     */
    protected function _authorizeEdit($context)
    {
        if ($this->_viewer->guest()) {
            return false;
        }

        if ($this->_viewer->admin()) {
            return true;
        }

        return false;
    }

    /**
     * Check if a node authroize being updated.
     *
     * @param KCommandContext $context Context parameter
     *
     * @return bool
     */
    protected function _authorizeDelete($context)
    {
        return $this->_authorizeAdministration($context);
    }
}
