<?php

/**
 * Coverable Behavior.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahita.io>
 * @copyright  2008 - 2015 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       https://www.Anahita.io
 */
class LibBaseControllerBehaviorCoverable extends AnControllerBehaviorAbstract
{
    /**
     * Constructor.
     *
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    public function __construct(AnConfig $config)
    {
        parent::__construct($config);

        $this->registerCallback('after.add', array($this, 'addCover'));
        $this->registerCallback('after.edit', array($this, 'editCover'));
    }

    /**
     * Add a cover.
     *
     * @param AnCommandContext $context Context parameter
     *
     * @return AnDomainEntityAbstract
     */
    public function addCover(AnCommandContext $context)
    {
        $entity = $this->getItem();
        
        if ($entity->isCoverable() && AnRequest::has('files.cover')) {
            $file = AnRequest::get('files.cover', 'raw');

            if ($file['size'] > 0 && $file['error'] == 0 && $this->bellowSizeLimit($file)) {
                $entity->setCover(array('url' => $file['tmp_name'], 'mimetype' => $file['type']));
            }
        }

        return $entity;
    }

    /**
     * edit a cover.
     *
     * @param AnCommandContext $context Context parameter
     *
     * @return AnDomainEntityAbstract
     */
    public function editCover(AnCommandContext $context)
    {
        $entity = $this->getItem();
        
        if ($entity->isCoverable() && AnRequest::has('files.cover')) {
            $file = AnRequest::get('files.cover', 'raw');

            if ($file['size'] > 0 && $file['error'] == 0 && $this->_mixer->bellowSizeLimit($file)) {
                $this->getItem()->setCover(array(
                    'url' => $file['tmp_name'],
                    'mimetype' => $file['type']
                ));
            } else {
                $entity->removeCoverImage();
            }
        }

        return $entity;
    }

    /**
     * delete a cover.
     *
     * @param AnCommandContext $context Context parameter
     *
     * @return AnDomainEntityAbstract
     */
    public function deleteCover(AnCommandContext $context)
    {
        $entity = $this->getItem();
        
        if ($entity->isCoverable() && AnRequest::has('files.cover')) {
            $entity->removeCoverImage();
        }

        return $entity;
    }
}
