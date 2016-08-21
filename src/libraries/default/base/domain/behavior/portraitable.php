<?php

/**
 * LICENSE: ##LICENSE##.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @version    SVN: $Id$
 *
 * @link       http://www.GetAnahita.com
 */

/**
 * Portraitable Behavior.
 *
 * An image representation of a node
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class LibBaseDomainBehaviorPortraitable extends LibBaseDomainBehaviorStorable
{
    /**
     * An arary of sizes to resize a portrait to.
     *
     * @var array
     */
    protected $_sizes;

    /**
     * A boolearn flag to whether to keep the orignal file or not. By default set to true.
     *
     * @var bool
     */
    protected $_keep_original;

    /**
     * Pending files to be stored for an entity.
     *
     * @var AnObjectArray
     */
    protected $_pending_files;

    /**
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        $this->_sizes = $config['sizes'];
        $this->_keep_original = $config['keep_original'];
        $this->_pending_files = $this->getService('anahita:object.array');
    }

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
            'attributes' => to_hash(array(
                'filename' => array('write' => 'protected'),
                'mimetype',
            )),
            'keep_original' => true,
            'sizes' => array(
                'large' => '1024xauto',
                'medium' => '640xauto',
                'small' => '240xauto',
                'square' => 100,
             ),
        ));

        parent::_initialize($config);
    }

    /**
     * Return if the portrait is set.
     *
     * @return bool
     */
    public function portraitSet()
    {
        return !empty($this->filename);
    }

    /**
     * Removes the portrait image.
     */
    public function removePortraitImage()
    {
        $sizes = $this->_mixer->getValue('sizes');

        if (empty($sizes)) {
            $sizes = explode(' ', 'original large medium small thumbnail square');
        } else {
            $sizes = array_keys($sizes);
        }

        foreach ($sizes as $size) {
            $file = $this->_mixer->getPortraitFile($size);
            $this->_mixer->deletePath($file);
        }

        $this->set('filename', null);
    }

    /**
     * Return the portrait file for a size. Override the Portriable behavior due
     * to some legacy.
     *
     * @see LibBaseDomainBehaviorPortraitable
     *
     * @return string
     */
    public function getPortraitFile($size)
    {
        $filename = $this->filename;

        //get file extension
        $extension = explode('.', $filename);
        $extension = array_pop($extension);

        //remove extension
        $name = preg_replace('#\.[^.]*$#', '', $filename);

        $filename = $name.'_'.$size.'.'.$extension;

        return $filename;
    }

    /**
     * Return the URL to the portrait.
     *
     * @return string
     */
    public function getPortraitURL($size = 'square')
    {
        $filename = $this->_mixer->getPortraitFile($size);
        $url = $this->getPathURL($filename, true);

        return $url;
    }

    /**
     * Obtain the list of available sizes and dimensions for this photo.
     *
     * @return array of $size=>$dimension
     */
    public function getPortraitSizes()
    {
        $sizes = $this->getValue('sizes');

        if (empty($sizes)) {
            $sizes = $this->_sizes;
        }

        return $sizes;
    }

    /**
     * After an entity is instantaited.
     *
     * @param KCommandContext $context
     */
    protected function _afterEntityInstantiate(KCommandContext $context)
    {
        $data = $context->data;

        if ($data->portrait) {
            $this->setPortrait($data->portrait);
        }
    }

    /**
     * Set the portrait.
     *
     * @param array $config The portrait options [data,orientation,mimetype]
     */
    public function setPortrait($config)
    {
        $config = new KConfig($config);

        $config->append(array(
                'rotation' => 0,
                'mimetype' => 'image/jpeg',
        ));

        if ($config->url) {
            $config->append(array(
                'data' => file_get_contents($config->url),
            ));
        }

        $config->mimetype = strtolower($config->mimetype);

        //the allowed mimetypes
        $mimetypes = array('image/jpeg' => 'jpg','image/png' => 'png','image/gif' => 'gif');

        //force mimetype to jpeg if invalid
        //@TODO is this wise ?? No it isn't, but until we find a more reliable method to detect mimetypes
        if (!isset($mimetypes[$config->mimetype])) {
            $config->mimetype = 'image/jpeg';
        }

        if ($config->data) {
            $data = $config->data;

            if (empty($data)) {
                return false;
            }

            $config->append(array(
                'image' => imagecreatefromstring($data),
            ));
        }

        $image = $config->image;

        if (empty($image)) {
            return false;
        }

        $rotation = $config->rotation;

        switch ($rotation) {
            case 3:
                $rotation = 180;
                break;

            case 6:
                $rotation = -90;
                break;

            case 8:
                $rotation = 90;
                break;

            default :
                $rotation = 0;
        }

        if ($rotation != 0) {
            $image = imagerotate($image, $rotation, 0);
        }

        if ($this->persisted()) {
            $this->_mixer->removePortraitImage();
        }

        $images = $this->_mixer->resizePortraitImage($image);
        $this->_mixer->set('filename', md5(uniqid('', true)).'.'.$mimetypes[$config->mimetype]);
        $this->_mixer->set('mimetype', $config->mimetype);

        $sizes = array();
        $files = array();

        foreach ($images as $key => $value) {
            $filename = $this->_mixer->getPortraitFile($key);
            $sizes[$key] = $value['size'];
            $files[$filename] = AnHelperImage::output($value['data'], $config->mimetype);
        }

        imagedestroy($image);
        $this->_mixer->setValue('sizes', $sizes);
        $this->_pending_files[$this->_mixer] = $files;

        return true;
    }

    /**
     * Return a resizes verion of the image.
     *
     * @param resource $image
     *
     * @return array
     */
    public function resizePortraitImage($image)
    {
        $images = array();
        $original_width = imagesx($image);
        $originl_height = imagesy($image);

        foreach ($this->_sizes as $name => $size) {
            if (!is_int($size)) {
                list($width, $height) = AnHelperImage::parseSize($size);
                if ($original_width < $width) {
                    $size = array($original_width, $originl_height);
                } else {
                    $size = array($width, 'auto');
                }
            }

            $data = AnHelperImage::resize($image, $size);
            $width = imagesx($data);
            $height = imagesy($data);
            $images[$name] = array('size' => $width.'x'.$height,'data' => $data);
        }

        if ($this->_keep_original) {
            $images['original'] = array('size' => $original_width.'x'.$originl_height, 'data' => $image);
        }

        return $images;
    }

    /**
     * Called after the entity is updated.
     *
     * @param KCommandContext $context
     */
    protected function _afterEntityUpdate(KCommandContext $context)
    {
        if (isset($this->_pending_files[$this->_mixer])) {
            $files = $this->_pending_files[$this->_mixer];

            foreach ($files as $filename => $data) {
                $this->_mixer->writeData($filename, $data);
            }

            unset($this->_pending_files[$this->_mixer]);
        }
    }

    /**
     * Called after inserting the entity.
     *
     * @param KCommandContext $context Context parameter
     */
    protected function _afterEntityInsert(KCommandContext $context)
    {
        if (isset($this->_pending_files[$this->_mixer])) {
            $files = $this->_pending_files[$this->_mixer];

            foreach ($files as $filename => $data) {
                $this->_mixer->writeData($filename, $data);
            }

            unset($this->_pending_files[$this->_mixer]);
        }
    }

    /**
     * Delete a photo image from the storage.
     *
     * @param  KCommandContext $context Context parameter
     *
     * @return bool
     */
    protected function _beforeEntityDelete(KCommandContext $context)
    {
        $this->_mixer->removePortraitImage();
    }
}
