<?php
/**
 * @version		$Id: file.php 4628 2012-05-06 19:56:43Z johanjanssens $
 * @package     Koowa_View
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * Use to force browser to download a file from the file system
 *
 * <code>
 * // in child view class
 * public function display()
 * {
 *      $this->path = path/to/file');
 *      // OR
 *      $this->output = $file_contents;
 *
 *      $this->filename = foobar.pdf';
 *
 *      // optional:
 *      $this->mimetype    = 'application/pdf';
 *      $this->disposition =  'inline'; // defaults to 'attachment' to force download
 *
 *      return parent::display();
 * }
 * </code>
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @category    Koowa
 * @package     Koowa_View
 */
class KViewFile extends KViewAbstract
{
    /**
     * The file path
     *
     * @var string
     */
    public $path = '';

    /**
     * The file name
     *
     * @var string
     */
    public $filename = '';

    /**
     * The file disposition
     *
     * @var string
     */
    public $disposition = 'attachment';

    /**
     * Constructor
     *
     * @param   object  An optional KConfig object with configuration options
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        $this->set($config->toArray());
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   object  An optional KConfig object with configuration options
     * @return  void
     */
    protected function _initialize(KConfig $config)
    {
        $count = count($this->getIdentifier()->path);

        $config->append(array(
            'path'        => '',
            'filename'    => $this->getIdentifier()->path[$count-1].'.'.$this->getIdentifier()->name,
            'disposition' => 'attachment'
        ));

        parent::_initialize($config);
    }

    /**
     * Return the views output
     *
     * @return void
     */
    public function display()
    {
        // For a certain unmentionable browser
        if(ini_get('zlib.output_compression')) {
            ini_set('zlib.output_compression', 'Off');
        }

        // Remove php's time limit
        if(!ini_get('safe_mode') ) {
            @set_time_limit(0);
        }

        // Mimetype
        // @TODO magic mimetypes
        if($this->mimetype) {
            header('Content-type: '.$this->mimetype);
        }

        header('Content-Transfer-Encoding: binary');
        header('Accept-Ranges: bytes');

        // Prevent caching
        header("Pragma: no-store,no-cache");
        header("Cache-Control: no-cache, no-store, must-revalidate, max-age=-1");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Expires: Mon, 14 Jul 1789 12:30:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

        // Clear buffer
        while (@ob_end_clean());

        $this->filename = basename($this->filename);
        if(!empty($this->output)) // File body is passed as string
        {
            if(empty($this->filename)) {
                throw new KViewException('No filename supplied');
            }
            $this->_setDisposition();
            $filesize = strlen($this->output);
            header('Content-Length: '.$filesize);
            flush();
            echo $this->output;
        }
        elseif(!empty($this->path)) // File is read from disk
        {
            if(empty($this->filename)) {
                $this->filename = basename($this->path);
            }
            $filesize = @filesize($this->path);
            header('Content-Length: '.$filesize);
            $this->_setDisposition();
            flush();
            $this->_readChunked($this->path);
        }
        else throw new KViewException('No output or path supplied');

        die;
    }


    /**
     * Set the header disposition headers
     *
     * @return KViewFile
     */
    protected function _setDisposition()
    {
        // @TODO :Content-Disposition: inline; filename="foo"; modification-date="'.$date.'"; size=123;
        if(isset($this->disposition) && $this->disposition == 'inline') {
            header('Content-Disposition: inline; filename="'.$this->filename.'"');
        } else {
            header('Content-Description: File Transfer');
            header('Content-type: application/force-download');
            header('Content-Disposition: attachment; filename="'.$this->filename.'"');
        }
        return $this;
    }


    /**
     * Read a file in chunks and flush it to the output stream
     *
     * @param  string   Path to a file to be read
     * @return integer  Number of chunks being flushed
     */
    protected function _readChunked($path)
    {
        $chunksize  = 1*(1024*1024); // Chunk size
        $buffer     = '';
        $cnt        = 0;

        $handle = fopen($path, 'rb');
        if ($handle === false) {
            throw new KViewException('Cannot open file');
        }

        while (!feof($handle))
        {
            $buffer = fread($handle, $chunksize);
            echo $buffer;
            @ob_flush();
            flush();
            $cnt += strlen($buffer);
        }

       $status = fclose($handle);
       return $cnt;
    }
}
