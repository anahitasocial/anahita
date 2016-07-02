<?php

/**
 * Default Location Authorizer.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComLocationsDomainAuthorizerDefault extends LibBaseDomainAuthorizerDefault
{
    /**
     * Check if a node authroize being updated.
     *
     * @param KCommandContext $context Context parameter
     *
     * @return bool
     */
    protected function _authorizeEdit($context)
    {
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
        if ($this->_viewer->admin()) {
            return true;
        }

        return false;
    }
}
