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
     * Read Service.
     *
     * @param AnCommandContext $context
     */
    protected function _actionRead(AnCommandContext $context)
    {
        $entity = parent::_actionRead($context);
        $this->getToolbar('menubar')->setTitle(sprintf(AnTranslator::_('COM-HASHTAGS-TERM'), $entity->name));
    }
}
