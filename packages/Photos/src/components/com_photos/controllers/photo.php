<?php

/**
 * Photo Controller.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahita.io>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class ComPhotosControllerPhoto extends ComMediumControllerDefault
{
    /**
     * The max upload limit.
     *
     * @var int
     */
    protected $_max_upload_limit;

    /**
     * Constructor.
     *
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    public function __construct(AnConfig $config)
    {
        parent::__construct($config);
        $this->_max_upload_limit = $config->max_upload_limit;
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
            'max_upload_limit' => get_config_value('photos.uploadlimit', 2),
        ));

        parent::_initialize($config);
    }

    /**
     * Browse Photos.
     *
     * @param AnCommandContext $context
     */
    protected function _actionBrowse($context)
    {
        $this->getService('repos:photos.set');
        $photos = parent::_actionBrowse($context);
        $photos->order('creationTime', 'DESC');

        if ($this->exclude_set != '') {
            $set = $this->actor->sets->fetch(array('id' => $this->exclude_set));

            if (!empty($set)) {
                $photo_ids = array();

                foreach ($set->photos as $photo) {
                    $photo_ids[] = $photo->id;
                }

                if (count($photo_ids)) {
                    $photos->where('photo.id', '<>', $photo_ids);
                }
            }
        }

        return $photos;
    }

    /**
     * Method to upload and Add a photo.
     *
     * @param AnCommandContext $context
     */
    protected function _actionAdd($context)
    {
        $data = $context->data;
        $file = AnRequest::get('files.file', 'raw');
        $content = @file_get_contents($file['tmp_name']);
        $filesize = strlen($content);
        $uploadlimit = $this->_max_upload_limit * 1024 * 1024;

        $exif = (function_exists('exif_read_data')) ? @exif_read_data($file['tmp_name']) : array();

        if ($filesize == 0) {
            throw new LibBaseControllerExceptionBadRequest('File is missing');

            return;
        }

        if ($filesize > $uploadlimit) {
            throw new LibBaseControllerExceptionBadRequest('Exceed maximum size');

            return;
        }

        $orientation = 0;

        if (!empty($exif) && isset($exif['Orientation'])) {
            $orientation = $exif['Orientation'];
        }

        $data['portrait'] = array(
            'data' => $content,
            'rotation' => $orientation,
            'mimetype' => isset($file['type']) ? $file['type'] : null,
        );

        $photo = $this->actor->photos->addNew($data);
        $photo->setExifData($exif);
        $photo->save();
        $this->setItem($photo);
        $this->getResponse()->status = AnHttpResponse::CREATED;

        if ($photo->body && preg_match('/\S/', $photo->body)) {
            $context->append(array(
                'story' => array('body' => $photo->body),
            ));
        }

        return $photo;
    }
}
