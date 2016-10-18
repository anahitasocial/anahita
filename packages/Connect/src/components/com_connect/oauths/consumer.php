<?php

require_once 'core.php';

/**
 * Service Consumer.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComConnectOauthConsumer extends OAuthConsumer
{
    /**
     * Constructor.
     *
     * @param 	object 	An optional KConfig object with configuration options
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config->key, $config->secret, $config->callback_url);
    }

    /**
     * Return if the consumer key/secret is set.
     *
     * @return bool
     */
    public function isValid()
    {
        return !empty($this->key) && !empty($this->secret);
    }

    /**
     * Set the consumer callback URL.
     *
     * @param string $url
     */
    public function setCallbackURL($url)
    {
        $this->callback_url = $url;
    }
}
