<?php

require_once 'core.php';

/**
 * Service Response.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComConnectOauthResponse extends AnConfig
{
    /**
     * Response Text.
     *
     * @var text
     */
    protected $_text;

    /**
     * Constructor.
     *
     * @param 	object 	An optional AnConfig object with configuration options
     */
    public function __construct($text, $config)
    {
        $this->_text = pick($text, '');

        parent::__construct($config);
    }

    /**
     * Return the response code.
     *
     * @return int
     */
    public function getCode()
    {
        return $this->http_code;
    }

    /**
     * Return whether the response is succesful.
     *
     * @return bool
     */
    public function successful()
    {
        return in_range($this->getCode(), 200, 299);
    }

    /**
     * Parse the resposne as query and return AnConfig.
     *
     * @return AnConfig
     */
    public function parseQuery()
    {
        $array = array();
        parse_str($this, $array);

        return new AnConfig($array);
    }

    /**
     * Parse the resposne as json and return AnConfig.
     *
     * @return AnConfig
     */
    public function parseJSON()
    {
        return new AnConfig(json_decode((string) $this, true));
    }

    /**
     * Parse the resposne as xml and return AnConfig.
     *
     * @return SimpleXMLElement
     */
    public function parseXML()
    {
        return new SimpleXMLElement((string) $this);
    }

    /**
     * Return the response text.
     *
     * @return string
     */
    public function __toString()
    {
        return pick($this->_text, ' ');
    }
}
