<?php

/**
 * Comment Authorizer.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComNotesDomainAuthorizerComment extends ComMediumDomainAuthorizerDefault
{
    /**
     * Notes comment can not be edited.
     *
     * @param KCommandContext $context
     *
     * @return bool
     */
    protected function _authorizeEdit($context)
    {
        return false;
    }
}
