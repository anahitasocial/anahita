<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Stories
 * @subpackage Domain_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * {@inheritdoc}
 *
 * @category   Anahita
 * @package    Com_Stories
 * @subpackage Domain_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComStoriesDomainBehaviorPrivatable extends ComMediumDomainBehaviorPrivatable
{
    /**
     * {@inheritdoc}
     */
    protected function _beforeRepositoryFetch(KCommandContext $context)
    {
        $query		= $context->query;
        $repository = $query->getRepository();
        $query->privacy 	= pick($query->privacy, new KConfig());
        //weak link  the stories with object nodes
        //and use the object.access instead of the story access if there are ny
        $query->link('object', array('type'=>'weak','bind_type'=>false));
        $query->privacy->append(array(
             'use_access_column' => 'IF(@col(object.id) IS NULL,@col(story.access), @col(object.access))'                
        ));
        parent::_beforeRepositoryFetch($context);
    }
}