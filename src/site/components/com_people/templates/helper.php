<?php

/**
 * People Template Helper
 * 
 * Provides methods to for rendering avatar/name for an actor
 *
 * @category   Anahita
 * @package    Com_People
 * @subpackage Template_Helper
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.GetAnahita.com
 */
class ComPeopleTemplateHelper extends KTemplateHelperAbstract
{   
	/**
     * Return the list of enabled app links on an actor's profile
     * 
     * @param actor object ComActorsDomainEntityActor
     * 
     * @return array LibBaseTemplateObjectContainer
     */
    public function viewerMenuLinks($actor)
    {
		$context = new KCommandContext();
		$context->menuItems = new LibBaseTemplateObjectContainer();
		$context->actor = $actor;
		$context->actor->components->registerEventDispatcher($this->getService('anahita:event.dispatcher'));
		$this->getService('anahita:event.dispatcher')->dispatchEvent('onMenuDisplay', $context);
		
		return $context->menuItems;
    }
}