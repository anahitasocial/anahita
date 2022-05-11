<?php

/**
 * Document Controller.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahita.io>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class ComDocumentsControllerDocument extends ComMediumControllerDefault
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
            'max_upload_limit' => get_config_value('documents.uploadlimit', 2),
        ));

        parent::_initialize($config);
    }

    /**
     * Browse Action
     *
     * @param AnCommandContext $context
     */
    protected function _actionBrowse($context)
    {
        $entities = parent::_actionBrowse($context);
        
        $entities->order('creationTime', 'DESC');

        return $entities;
    }

    /**
     * Method to upload and Add a document
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
        
        $entity = parent::_actionAdd($context);

        $entity->setFileContent($file);
        
        $this->setItem($entity);
        
        $this->getResponse()->status = AnHttpResponse::CREATED;

        if ($entity->description && preg_match('/\S/', $entity->description)) {
            $context->append(array(
                'story' => array('body' => $entity->description),
            ));
        }

        return $entity;
    }
}
