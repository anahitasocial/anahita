<?php

/**
 * Revision Controller.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahita.io>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComArticlesControllerRevision extends ComMediumControllerDefault
{
    /**
     * Initializes the options for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   object  An optional AnConfig object with configuration options.
     */
    protected function _initialize(AnConfig $config)
    {
        $config->append(array(
            'behaviors' => array('parentable'),
        ));

        parent::_initialize($config);
    }

    /**
     * Restores an article back to a revision.
     *
     * @param AnCommandContext $context Context paramter
     */
    protected function _actionRestore($context)
    {
        $revision = $this->getItem();
        $article = $revision->article;
        $article->restore($revision);
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
