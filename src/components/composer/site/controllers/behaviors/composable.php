<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Composer
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Composable Behavior
 *
 * @category   Anahita
 * @package    Com_Composer
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComComposerControllerBehaviorComposable extends KControllerBehaviorAbstract
{    
    /**
     * Convienet method to render a story
     *
     * @return string
     */
    protected function _renderComposedStory($story)
    {
        $controller = $this->getService('com://site/stories.controller.story')
                            ->layout('list')
                            ->setItem($story);
        
        //manually set the toolbar
        $controller->toolbar = $controller->getToolbar('story');
        
        return  $controller->getView()->display();
    }
}