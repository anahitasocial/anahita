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
    *   @param maximum session lifetime
    */
    public function purge($lifetime = LibSessionsDomainEntitySession::MAX_LIFETIME)
    {
        $past = time() - $lifetime;
        $query = $this->getQuery()->delete()->where('time < '.$past);
        $this->getStore()->execute($query);
    }
}
