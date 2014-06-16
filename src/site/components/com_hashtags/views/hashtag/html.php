<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Hashtags
 * @subpackage View
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2014 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.GetAnahita.com
 */

/**
 * Default Actor View (Profile View)
 *
 * @category   Anahita
 * @package    Com_Hashtags
 * @subpackage View
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.GetAnahita.com
 */
class ComHashtagsViewHashtagHtml extends ComBaseViewHtml
{
	/**
	 * Prepare default layout
	 * 
	 * @return void
	 */
	protected function _layoutDefault()
	{	
		$this->set('gadgets', new LibBaseTemplateObjectContainer());
		$scopes = $this->getService('com://site/hashtags.domain.entityset.scope');

		print $scopes->getTotal();
		
		$context = new KCommandContext();
		$context->gadgets = $this->gadgets;
		
		foreach($scopes as $scope)
		{		
			$scopeKey = explode('.', $scope->getKey());
			$view = KInflector::pluralize($scopeKey[1]);
			$option = 'com_'.$scopeKey[0];
			
			$context->gadgets->insert($scope->getKey(), array(             
                'title' => translate('COM-'.str_ireplace('.', '-SEARCH-SCOPE-', strtoupper($scope->getKey()))),
				'url' => 'option='.$option.'&view='.$view.'&layout=gadget&ht[]='.$this->item->alias,
                'title_url'	=> 'option='.$option.'&view='.$view.'&ht[]='.$this->item->alias
        	));
		}
	}
}