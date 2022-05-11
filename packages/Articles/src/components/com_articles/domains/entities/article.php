<?php

/**
 * Article Entity.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahita.io>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class ComArticlesDomainEntityArticle extends ComMediumDomainEntityMedium
{
    /**
     * Initializes the default configuration for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    protected function _initialize(AnConfig $config)
    {
        $config->append(array(
            'attributes' => array(
                'name' => array(
                    'required' => AnDomain::VALUE_NOT_EMPTY,
                    'length' => array(
                        'max' => 100,
                    )
                ),
                'excerpt' => array(
                    'length' => array(
                        'max' => 1000,
                    )
                ),
                'body' => array(
                    'required' => AnDomain::VALUE_NOT_EMPTY,
                    'length' => array(
                        'max' => 40000,
                    )
                ),
            ),
            'aliases' => array(
                'published' => 'enabled',
                'title' => 'name',
            ),
            'relationships' => array(
                'revisions',
            ),
        ));

        $config->append(array(
            'behaviors' => array(
                'coverable',
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
    protected function _beforeEntityUpdate(AnCommandContext $context)
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
    
    /**
     * Return the cover file for a size.
     *
     * @see LibBaseDomainBehaviorCoverable
     *
     * @return string
     */
    public function getCoverFile($size)
    {
        if (strpos($this->coverFilename, '/')) {
            $cover = str_replace('/', '/covers/'.$size, $this->coverFilename);
        } else {
            $cover = $this->component.'/covers/'.$size.$this->coverFilename;
        }

        return $cover;
    }
}
