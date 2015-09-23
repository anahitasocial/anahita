<?php

/**
 * Search result in json format.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComSearchViewSearchesJson extends ComBaseViewJson
{
    /**
     * Return the list.
     *
     * @return array
     */
    protected function _getList()
    {
        $data = parent::_getList();
        $data['scopes'] = array();
        foreach ($this->_state->scopes as $scope) {
            $count = $this->_state->getList()->getScopeCount($scope);
            $data['scopes'][] = array('name' => $scope->getKey(),'count' => (int) $count);
        }

        return $data;
    }
}
