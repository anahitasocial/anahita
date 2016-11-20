<?php

/**
 * Session repository
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2016 rmd Studio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class LibSessionsDomainRepositorySession extends AnDomainRepositoryDefault
{
    /**
    *   Removes all expired sessions
    *
    *   @param int maximum session lifetime
    *   @param bool delete guest sessions only
    */
    public function purge($lifetime = LibSessionsDomainEntitySession::MAX_LIFETIME, $guestOnly = true)
    {
        $past = time() - $lifetime;

        $query = $this->getQuery()->delete()->where('time < '.$past);

        if ($guestOnly) {
            $query->where('guest', '=', 1);
        }

        return (boolean) $this->getStore()->execute($query);
    }
}
