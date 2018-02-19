<?php

/**
 * Groups the scopes by type.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComSearchTemplateHelperScopes extends LibBaseTemplateHelperAbstract
{
    /**
     * Groups the scopes into posts, actors and others.
     *
     * @param array $scopes
     *
     * @return array
     */
    public function group($scopes, $global = true)
    {
        $groups = array(
          'posts' => array(),
          'actors' => array(),
          'other' => array()
        );

        $current = $this->_template->getView()->current_scope;

        foreach ($scopes as $scope) {
          if ($scope->type == 'post') {
              $groups['posts'][] = $scope;
          } elseif($scope->type == 'actor' && $global) {
              $groups['actors'][] = $scope;
          }
        }

        return $groups;
    }
}
