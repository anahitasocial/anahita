<?php

/**
 * Location Controller
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2015 rmd Studio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComLocationsControllerLocation extends ComTagsControllerDefault
{
    /**
     * Applies the browse sorting.
     *
     * @param KCommandContext $context
     */
    protected function _actionBrowse(KCommandContext $context)
    {
        $entities = parent::_actionBrowse($context);

        $edgeType = 'ComTagsDomainEntityTag,ComLocationsDomainEntityTag,com:locations.domain.entity.tag';

        $entities->where('edge.type', '=', $edgeType)->group('location.id');

        return $entities;
    }
}
