<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Medium
 * @subpackage Controller_Behavior
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
 * @package    Com_Medium
 * @subpackage Controller_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComMediumControllerBehaviorComposable extends ComComposerControllerBehaviorComposable
{
    /**
     * Renders a story after an entity is created through composer
     *
     * @param KCommandContext $context Context parameter
     * 
     * @return void
     */
    protected function _afterControllerAdd(KCommandContext $context)
    {
        $data = $context->data;
        
        if ( $data->composed && 
                $data->story ) 
        {
            if ( $context->response->isRedirect() ) 
            {
                $context->response->setStatus(KHttpResponse::OK);
                $context->response->removeHeader('Location');
            }
            $content = $this->_renderComposedStory($data->story);            
            $context->response->setContent($content);
        }
    }
}