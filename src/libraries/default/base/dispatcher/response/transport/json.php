<?php

 /**
  * JSON transport.
  *
  * @category   Anahita
  *
  * @author     Arash Sanieyan <ash@anahitapolis.com>
  * @author     Rastin Mehr <rastin@anahitapolis.com>
  * @copyright  2008 - 2011 rmdStudio Inc./Peerglobe Technology Inc
  * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
  *
  * @link       http://www.GetAnahita.com
  */
 class LibBaseDispatcherResponseTransportJson extends LibBaseDispatcherResponseTransportAbstract
 {
     /**
     * The padding for JSONP.
     *
     * @var string
     */
    protected $_padding;

    /**
     * Constructor.
     *
     * @param   object  An optional KConfig object with configuration options
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);
        $this->_padding = $config->padding;
    }

    /**
     * Initializes the config for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param     object     An optional Config object with configuration options
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
                'padding' => '',
        ));

        parent::_initialize($config);
    }

    /**
     * Sets the JSONP callback.
     *
     * @param string $callback
     *
     * @throws \InvalidArgumentException If the padding is not a valid javascript identifier
     *
     * @return DispatcherResponseTransportJson
     */
    public function setCallback($callback)
    {
        // taken from http://www.geekality.net/2011/08/03/valid-javascript-identifier/
        $pattern = '/^[$_\p{L}][$_\p{L}\p{Mn}\p{Mc}\p{Nd}\p{Pc}\x{200C}\x{200D}]*+$/u';
        $parts = explode('.', $callback);

        foreach ($parts as $part) {
            if (! preg_match($pattern, $part)) {
                throw new \InvalidArgumentException('The callback name is not valid.');
            }
        }

        $this->_padding = $callback;
    }

    /**
     * Sends content for the current web response.
     *
     * @return DispatcherResponseTransportJson
     */
    public function sendContent()
    {
        if (! empty($this->_padding)) {
            $response = $this->getResponse();
            $response->setContent(sprintf('%s(%s);', $this->_padding, $response->getContent()));
            print $response->getContent();
            die;
        }

        return parent::sendContent();
    }

    /**
     * Send HTTP response.
     *
     * If not padding is set inspect the request query for a 'callback' parameter and use this.
     *
     * @see http://tools.ietf.org/html/rfc2616
     *
     * @return DispatcherResponseTransportJson
     */
    public function send()
    {
        //@TODO when returning the location header during
        //test it fetches the new location even though the
        //code is not redirect
        $this->getResponse()->setContentType('application/json');
        $headers = $this->getResponse()->getHeaders();

        if (isset($headers['Location'])) {
            $headers['Content-Location'] = $headers['Location'];
            unset($headers['Location']);
        }

        //If not padding is set inspect the request query.
        if (empty($this->_padding)) {
            $request = $this->getResponse()->getRequest();
            if ($request->has('callback')) {
                $this->setCallback($request->get('callback'));
            }
        }

        return parent::send();
    }
 }
