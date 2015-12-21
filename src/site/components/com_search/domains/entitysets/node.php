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
     * Tracks the scopes count.
     *
     * @var array
     */
    protected $_scopes_count;

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
            $this->_scopes = clone $this->getService('com://site/components.domain.entityset.scope');
            $total = 0;

            foreach ($this->_scopes as $scope) {
                $scope->result_count = (int) $this->getScopeCount($scope);
                $total += $scope->result_count;
            }

            $this->_scopes->setTotal($total);
        }

        return $this->_scopes;
    }

    /**
     * Return the scope count for a type.
     *
     * @param string $scope The scope to
     *
     * @return array
     */
    public function getScopeCount($scope)
    {
        if (!isset($this->_scopes_count)) {

            $query = clone $this->_query;

            $query->distinct = true;

            $query->select(array('node.type', 'count(*) AS count', 'node.parent_type'))
                  ->scope(null)
                  ->limit(0, 0)
                  ->group(array('node.type', 'node.parent_type'));

            $rows = $query->fetchRows();

            foreach ($rows as $row) {

                $identifier = explode(',', $row['type']);
                $identifier = array_pop($identifier);
                $identifier = $this->getIdentifier($identifier);

                if ($identifier->name == 'comment') {
                    if (isset($row['parent_type'])) {
                        $identifier = $this->getIdentifier($row['parent_type']);
                    }
                }

                $key = $identifier->package.'.'.$identifier->name;

                if (!isset($this->_scopes_count[$key])) {
                    $this->_scopes_count[$key] = 0;
                }

                $increment = ($row['count']) ? 1 : 0;
                $this->_scopes_count[$key] = $this->_scopes_count[$key] + $increment;
            }
        }

        return isset($this->_scopes_count[$scope->getKey()]) ? $this->_scopes_count[$scope->getKey()] : 0;
    }
}
