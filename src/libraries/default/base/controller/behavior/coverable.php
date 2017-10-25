<?php

/**
 * LICENSE: ##LICENSE##.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2015 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */

/**
 * Coverable Behavior.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class LibBaseControllerBehaviorCoverable extends AnControllerBehaviorAbstract
{
    /**
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        $this->registerCallback('after.add', array($this, 'addCover'));
        $this->registerCallback('after.edit', array($this, 'editCover'));
    }

    /**
     * Add a cover.
     *
     * @param KCommandContext $context Context parameter
     *
     * @return AnDomainEntityAbstract
     */
    public function addCover(KCommandContext $context)
    {
        $entity = $this->getItem();

        if ($entity->isCoverable() && KRequest::has('files.cover')) {
            $file = KRequest::get('files.cover', 'raw');

            if ($this->_mixer->bellowSizeLimit($file) && $file['error'] == 0) {
                $entity->setCover(array('url' => $file['tmp_name'], 'mimetype' => $file['type']));
            }
        }

        return $entity;
    }

    /**
     * edit a cover.
     *
     * @param KCommandContext $context Context parameter
     *
     * @return AnDomainEntityAbstract
     */
    public function editCover(KCommandContext $context)
    {
        $entity = $this->getItem();

        if ($entity->isCoverable() && KRequest::has('files.cover')) {

            $file = KRequest::get('files.cover', 'raw');

            if ($this->_mixer->bellowSizeLimit($file) && $file['error'] == 0) {

                $this->getItem()->setCover(array(
                    'url' => $file['tmp_name'],
                    'mimetype' => $file['type']
                ));

                $story = $this->createStory(array(
                   'name' => 'cover_edit',
                   'owner' => $entity,
                   'target' => $entity,
                ));

            } else {

                $entity->removeCoverImage();

                $this->getService('repos:stories.story')->destroy(array(
                    'name' => 'cover_edit',
                    'owner' => $entity,
                    'component' => 'com_'.$this->getIdentifier()->package
                ));
            }
        }

        return $entity;
    }
}
