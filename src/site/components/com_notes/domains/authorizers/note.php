<?php

/**
 * Post authorizer.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComNotesDomainAuthorizerNote extends ComMediumDomainAuthorizerDefault
{
    /**
     * Notes are not ediable.
     *
     * @param KCommandContext $context Context parameter
     *
     * @return bool
     */
    protected function _authorizeEdit($context)
    {
        return true;
    }
}
