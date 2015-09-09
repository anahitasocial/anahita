<?php

/**
 * Revision Controller.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComPagesControllerRevision extends ComMediumControllerDefault
{
    /**
     * Initializes the options for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   object  An optional KConfig object with configuration options.
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'behaviors' => array('parentable'),
        ));

        parent::_initialize($config);
    }

    /**
     * Restores a page back to a revision.
     *
     * @param KCommandContext $context Context paramter
     */
    protected function _actionRestore($context)
    {
        $revision = $this->getItem();
        $page = $revision->page;
        $page->restore($revision);

        $msg = JText::sprintf('COM-PAGES-PAGE-REVISIONS-RESTORATION-CONFIRMATION', $revision->revisionNum);
        $context->response->setRedirect($page->getURL().'&layout=edit', $msg);
    }

    /**
     * Prevents deletion of a revision.
     *
     * @return bool
     */
    public function canDelete()
    {
        return false;
    }
}
