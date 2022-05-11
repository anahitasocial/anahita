<?php
/**
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahita.io>
 * @copyright  2008 - 2015 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */

class LibBaseDomainBehaviorCoverable extends LibBaseDomainBehaviorStorable
{
    /**
     * An arary of sizes to resize a cover to.
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
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    public function __construct(AnConfig $config)
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
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    protected function _initialize(AnConfig $config)
    {
        $config->append(array(
            'attributes' => to_hash(array(
                'coverFilename' => array('write' => 'protected'),
                'coverMimetype',
            )),
            'keep_original' => true,
            'sizes' => array(
                'large' => '1600xauto',
                'medium' => '640xauto',
             ),
        ));

        parent::_initialize($config);
    }

    /**
     * Return if the cover is set.
     *
     * @return bool
     */
    public function hasCover()
    {
        return !empty($this->coverFilename);
    }

    /**
     * Removes the cover image.
     */
    public function removeCoverImage()
    {
        $sizes = $this->_mixer->getValue('sizes');

        if (empty($sizes)) {
            $sizes = explode(' ', 'original large medium');
        } else {
            $sizes = array_keys($sizes);
        }

        foreach ($sizes as $size) {
            $file = $this->_mixer->getCoverFile($size);
            $this->_mixer->deletePath($file);
        }

        $this->set('coverFilename', null);
    }

    /**
     * Return the cover file for a size. Override the Coverable behavior due
     * to some legacy.
     *
     * @see LibBaseDomainBehaviorCoverable
     *
     * @return string
     */
    public function getCoverFile($size)
    {
        $coverFilename = $this->coverFilename;

        //get the extension
        $extension = explode('.', $coverFilename);
        $extension = array_pop($extension);

        //remove file extension
        $name = preg_replace('#\.[^.]*$#', '', $coverFilename);
        $coverFilename = $name.'_'.$size.'.'.$extension;

        return $coverFilename;
    }

    /**
     * Return the URL to the cover.
     *
     * @return string
     */
    public function getCoverURL($size = 'large')
    {
        $coverFilename = $this->_mixer->getCoverFile($size);
        $url = $this->getPathURL($coverFilename, true);

        return $url;
    }

    /**
     * Obtain the list of available sizes and dimensions for this cover.
     *
     * @return array of $size=>$dimension
     */
    public function getCoverSizes()
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
     * @param AnCommandContext $context
     */
    protected function _afterEntityInstantiate(AnCommandContext $context)
    {
        $data = $context->data;

        if ($data->cover) {
            $this->setCover($data->cover);
        }
    }

    /**
     * Set the cover.
     *
     * @param array $config The cover options [data,orientation,mimetype]
     */
    public function setCover($config)
    {
        $config = new AnConfig($config);

        $config->append(array(
            'mimetype' => 'image/jpeg',
        ));

        if ($config->url) {
            $config->append(array(
                'data' => file_get_contents($config->url),
            ));
        }

        $config->mimetype = strtolower($config->mimetype);

        // the allowed mimetypes
        $mimetypes = array(
            'image/jpeg' => 'jpg', 
            'image/png' => 'png'
        );

        // force mimetype to jpeg if invalid
        // @TODO is this wise ?? No it isn't, but until we find a more reliable method to detect mimetypes
        if (! isset($mimetypes[$config->mimetype])) {
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

        if ($this->persisted()) {
            $this->_mixer->removeCoverImage();
        }

        $images = $this->_mixer->resizeCoverImage($image);
        $this->_mixer->set('coverFilename', md5(uniqid('', true)).'.'.$mimetypes[$config->mimetype]);
        $this->_mixer->set('coverMimetype', $config->mimetype);

        $sizes = array();
        $files = array();

        foreach ($images as $key => $value) {
            $filename = $this->_mixer->getCoverFile($key);
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
    public function resizeCoverImage($image)
    {
        $images = array();
        $original_width = imagesx($image);
        $originl_height = imagesy($image);

        foreach ($this->_sizes as $name => $size) {
            if (! is_int($size)) {
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
     * @param AnCommandContext $context
     */
    protected function _afterEntityUpdate(AnCommandContext $context)
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
     * @param AnCommandContext $context Context parameter
     */
    protected function _afterEntityInsert(AnCommandContext $context)
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
     * @param  AnCommandContext $context Context parameter
     *
     * @return bool
     */
    protected function _beforeEntityDelete(AnCommandContext $context)
    {
        $this->_mixer->removeCoverImage();
    }
}
