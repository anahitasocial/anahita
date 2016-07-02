<?php

/**
 * Article Authorizer.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComArticlesDomainAuthorizerArticle extends ComMediumDomainAuthorizerDefault
{
    /**
     * Check if a node authroize being updated.
     *
     * @param KCommandContext $context Context parameter
     *
     * @return bool
     */
    protected function _authorizeEdit($context)
    {
        $ret = parent::_authorizeEdit($context);

        if (
              $ret === false &&
              $this->_entity->isOwnable() &&
              $this->_entity->owner->allows($this->_viewer, 'com_articles:article:edit')
        ) {
            return true;
        }

        return $ret;
    }
}
