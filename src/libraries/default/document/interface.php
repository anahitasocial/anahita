<?php

/**
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2016 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */

interface LibDocumentInterface
{
    /**
    * Set the document type
    *
    * @access	public
    * @param	string $type
    */
    public function setType($type);

    /**
    * Returns the document type
    *
    * @access	public
    * @return	string
    */
    public function getType();

    /**
    * Get the document head data
    *
    * @access	public
    * @return	array	The document head data in array form
    */
    public function getHeadData();

    /**
    * Set the document head data
    *
    * @access	public
    * @param	array	$data	The document head data in array form
    */
    public function setHeadData($data);

    /**
    * Get the contents of the document buffer
    *
    * @access public
    * @return 	The contents of the document buffer
    */
    public function getBuffer();

    /**
    * Set the contents of the document buffer
    *
    * @access public
    * @param string 	$content	The content to be set in the buffer
    */
    public function setBuffer($contents, $type, $name = null);

    /**
    * Gets a meta tag.
    *
    * @param	string	$name			Value of name or http-equiv tag
    * @param	bool	$http_equiv	 META type "http-equiv" defaults to null
    * @return	string
    * @access	public
    */
    public function getMetaData($name, $http_equiv = false);

    /**
    * Sets or alters a meta tag.
    *
    * @param string  $name			Value of name or http-equiv tag
    * @param string  $content		Value of the content tag
    * @param bool	$http_equiv	 META type "http-equiv" defaults to null
    * @return void
    * @access public
    */
    public function setMetaData($name, $content, $http_equiv = false);

    /**
    * Adds a linked script to the page
    *
    * @param	string  $url		URL to the linked script
    * @param	string  $type		Type of script. Defaults to 'text/javascript'
    * @access   public
    */
    public function addScript($url, $type="text/javascript");

    /**
    * Adds a script to the page
    *
    * @access   public
    * @param	string  $content   Script
    * @param	string  $type	Scripting mime (defaults to 'text/javascript')
    * @return   void
    */
    public function addScriptDeclaration($content, $type = 'text/javascript');

    /**
    * Adds a linked stylesheet to the page
    *
    * @param	string  $url	URL to the linked style sheet
    * @param	string  $type   Mime encoding type
    * @param	string  $media  Media type that this stylesheet applies to
    * @access   public
    */
    public function addStyleSheet($url, $type = 'text/css', $media = null, $attribs = array());

    /**
    * Adds a stylesheet declaration to the page
    *
    * @param	string  $content   Style declarations
    * @param	string  $type		Type of stylesheet (defaults to 'text/css')
    * @access   public
    * @return   void
    */
    public function addStyleDeclaration($content, $type = 'text/css');

    /**
    * Sets the document charset
    *
    * @param   string   $type  Charset encoding string
    * @access  public
    * @return  void
    */
    public function setCharset($type = 'utf-8');

    /**
    * Returns the document charset encoding.
    *
    * @access public
    * @return string
    */
    public function getCharset();

    /**
     * Sets the global document language declaration. Default is English (en-gb).
     *
     * @access public
     * @param   string   $lang
     */
    public function setLanguage($lang = "en-gb");

    /**
     * Returns the document language.
     *
     * @return string
     * @access public
     */
    public function getLanguage();

    /**
     * Sets the global document direction declaration. Default is left-to-right (ltr).
     *
     * @access public
     * @param   string   $lang
     */
    public function setDirection($dir = "ltr");

    /**
     * Returns the document language.
     *
     * @return string
     * @access public
     */
    public function getDirection();

    /**
     * Sets the title of the document
     *
     * @param	string	$title
     * @access   public
     */
    public function setTitle($title);

    /**
     * Return the title of the document.
     *
     * @return   string
     * @access   public
     */
    public function getTitle();

    /**
     * Sets the base URI of the document
     *
     * @param	string	$base
     * @access   public
     */
    public function setBase($base);

    /**
     * Return the base URI of the document.
     *
     * @return   string
     * @access   public
     */
    public function getBase();

    /**
     * Sets the description of the document
     *
     * @param	string	$title
     * @access   public
     */
    public function setDescription($description);

    /**
     * Return the title of the page.
     *
     * @return   string
     * @access   public
     */
    public function getDescription();

    /**
    * Sets the document link
    *
    * @param   string   $url  A url
    * @access  public
    * @return  void
    */
    public function setLink($url);
    /**
    * Returns the document base url
    *
    * @access public
    * @return string
    */
    public function getLink();

    /**
    * Sets the document modified date
    *
    * @param   string
    * @access  public
    * @return  void
    */
    public function setModifiedDate($date);

    /**
    * Returns the document modified date
    *
    * @access public
    * @return string
    */
    public function getModifiedDate();

    /**
    * Sets the document MIME encoding that is sent to the browser.
    *
    * @param	string	$type
    * @access   public
    * @return   void
    */
    public function setMimeEncoding($type = 'text/html');

    /**
	 * Outputs the document
	 *
	 * @access public
	 * @return 	The rendered data
	 */
	public function render();
}
