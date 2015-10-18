<?php

/**
 * Hashtag Controller.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComHashtagsControllerHashtag extends ComTagsControllerDefault
{
    /**
     * Applies the browse sorting.
     *
     * @param KCommandContext $context
     */
    protected function _actionBrowse(KCommandContext $context)
    {
        $entities = parent::_actionBrowse($context);

        $edgeType = 'ComTagsDomainEntityTag,ComHashtagsDomainEntityTag,com:hashtags.domain.entity.tag';

        $entities->where('edge.type', '=', $edgeType)->group('hashtag.id');

        return $entities;
    }
}
