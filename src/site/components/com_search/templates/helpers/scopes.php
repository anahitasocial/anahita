<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Search
 * @subpackage Template_Helper
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: view.php 13650 2012-04-11 08:56:41Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Groups the scopes by type
 *
 * @category   Anahita
 * @package    Com_Search
 * @subpackage Template_Helper
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComSearchTemplateHelperScopes extends KTemplateHelperAbstract
{ 
	/**
	 * Groups the scopes into posts, actors and others
	 * 
	 * @param array $scopes
	 * 
	 * @return array
	 */
	public function group($scopes)
	{
		$groups  = array('posts'=>array(),'actors'=>array(),'other'=>array());
		$current = $this->_template->getView()->current_scope;
		foreach($scopes as $scope) 
		{
			if ( $scope->result_count > 0 || $current === $scope)
			{
				if  ( $scope->type == 'post' ) {
					$groups['posts'][]  = $scope;
				} else {
					$groups['actors'][] = $scope;
				}				
			}			
		}
		return $groups;
	}
}