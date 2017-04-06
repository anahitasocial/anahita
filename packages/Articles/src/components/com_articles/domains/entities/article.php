<?php

/**
 * Article Entity.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComArticlesDomainEntityArticle extends ComMediumDomainEntityMedium
{
    /**
     * Initializes the default configuration for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'attributes' => array(
                'name' => array('required' => true),
                'excerpt' => array(
                    'required' => AnDomain::VALUE_NOT_EMPTY,
                    'format' => 'string',
                ),
                'body' => array('format' => 'html'),
            ),
            'relationships' => array(
                'revisions',
            ),
            'aliases' => array(
                'published' => 'enabled',
                'title' => 'name',
            ),
        ));

        $config->append(array(
            'behaviors' => array(
                'hittable',
                'pinnable',
                'modifiable' => array(
                    'modifiable_properties' => array('excerpt', 'name', 'body'),
                )
            ),
        ));

        parent::_initialize($config);
    }

    /**
     * Creates a new revision before updating.
     */
    protected function _beforeEntityUpdate(KCommandContext $context)
    {
        $modifications = $this->getModifiedData();

        if (isset($this->__restored)) {
            return;
        }

        if (
              $modifications->name ||
              $modifications->body ||
              $modifications->excerpt
        ) {
            $revision = $this->addNewRevision();

            foreach ($modifications as $property => $change) {
                $revision->$property = $change->old;
            }
        }
    }

    /**
     * Restore an article back to one of it's revision num.
     *
     * @param int $revision
     */
    public function restore($revision)
    {
        $revision = $this->revisions->find(array('revisionNum' => $revision->revisionNum));

        if ($revision) {
            $this->__restored = true;
            $this->setData(array(
                'name' => $revision->title,
                'body' => $revision->description,
                'exceprt' => $revision->excerpt,
            ), AnDomain::ACCESS_PROTECTED);
        }
    }

    /**
     * Creates new revision.
     *
     * @return ComArticlesDomainEntityRevision
     */
    public function addNewRevision()
    {
        return $this->revisions->addNew(array(
            'component' => $this->component,
            'author' => get_viewer(),
            'owner' => $this->owner,
            'title' => $this->title,
            'description' => $this->description,
            'excerpt' => $this->excerpt,
            'revisionNum' => (int) $this->revisions->fetchValue('MAX(@col(revisionNum))') + 1,
        ));
    }

    /**
     * Returns true if the article is published and visible to more than just the owner.
     *
     * @return bool
     */
    public function isPublished()
    {
        return $this->access !== LibBaseDomainBehaviorPrivatable::ADMIN;
    }
}
