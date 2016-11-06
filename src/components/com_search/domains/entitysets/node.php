<?php

/**
 * Request edge represents a follow request between two actors.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComSearchDomainEntitysetNode extends AnDomainEntitysetDefault
{
    /**
     * Scopes.
     *
     * @var array
     */
    protected $_scopes;

    /**
     * Return an array of scopes with a count per each scope.
     *
     * @return array
     */
    public function getScopes()
    {
        if (!isset($this->_scopes)) {
            $this->_scopes = clone $this->getService('com:components.domain.entityset.scope');
        }

        return $this->_scopes;
    }
}
