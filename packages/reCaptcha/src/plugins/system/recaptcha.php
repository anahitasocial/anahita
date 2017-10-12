<?php

/**
 * Google reCaptcha system plugin for Anahita. This plugin validates the reCaptcha response.
 *
 * @category   Anahita
 *
 * @author     Nick Swinford <nick@nicholasjohn16.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class plgSystemRecaptcha extends PlgAnahitaDefault
{
    private $_option;
    private $_view;
    private $_id;
    private $_layout;

    /**
    *   Class constructor
    *   @param $dispatcher optional dispatcher object
    *   @param $config optional KConfig object
    *
    *   @return void
    */
    public function __construct($dispatcher = null,  KConfig $config)
    {
        parent::__construct($dispatcher, $config);

        $this->_option = KRequest::get('get.option', 'string', '');
        $this->_view = KRequest::get('get.view', 'cmd', '');
        $this->_layout = KRequest::get('get.layout', 'cmd', '');
        $this->_id = KRequest::get('get.view', 'int', 0);
    }

    /**
     * onAfterRender handler.
     */
    public function onAfterRoute(KEvent $event)
    {
        if (KRequest::method() === 'POST' && $this->_hasRecaptcha()) {
            $recaptchaResponse = KRequest::get('post.g-recaptcha-response', 'string', null);
            if (! $this->_verifyResponse($recaptchaResponse)) {
                throw new KException("Unauthorized Request", 403);
                return;
            }
        }
    }

    /**
     * onAfterRender handler.
     */
    public function onAfterDispatch(KEvent $event)
    {

    }

    /**
     * onBeforeRender handler.
     */
    public function onBeforeRender(KEvent $event)
    {
        if($this->_option === 'com_people') {
            if ($this->_view === 'session' || $this->_view === 'person') {
                $this->_addScripts();
            }
        }

        error_log($this->_layout);

        if ($this->_option === 'com_groups' && $this->_view === 'group' && $this->_layout === 'add') {
            $this->_addScripts();
        }
    }

    /**
     * onAfterRender handler.
     */
    public function onAfterRender(KEvent $event)
    {

    }

    /**
    *   Returns true if the request is coming from a form with reCaptcha.
    *   The request is from one of the following forms:
    *   1. login form
    *   2. registration form
    *   3. add group form
    *
    *   @return boolean
    */
    private function _hasRecaptcha()
    {
        $action = KRequest::get('post.action', 'cmd', 'add');

        if ($this->_option === 'com_people') {
            if ($this->_view === 'session' && $action === 'add') {
                return true;
            }
            if ($this->_view === 'person' && $this->_id === 0) {
                return true;
            }
        }

        if ($this->_option === 'com_groups' && $this->_view === 'group' && $this->_id === 0 && $action === 'add') {
            return true;
        }

        return false;
    }

    /**
    *   Adds required javascript code to the header.
    *
    *   @return void
    */
    private function _addScripts()
    {
        $api = 'https://www.google.com/recaptcha/api.js';
        $base = KService::get('com:application')->getRouter()->getBaseUrl();
        $recaptcha = $base.'/media/plg_recaptcha/js/';
        $recaptcha .= ANDEBUG ? 'recaptcha.js' : 'min/recaptcha.min.js';

        $document = KService::get('anahita:document');
        $jsDeclaration = "
            $(document).ready(function(){
                if( $('form.recaptcha').length ) {
                    var recaptcha = $('form.recaptcha').recaptcha({
                        siteKey: \"%s\"
                    });
                }
            });
        ";
        $document->addScriptDeclaration(sprintf($jsDeclaration, $this->_params->get('site-key')));

        $document->addScript($api);
        $document->addScript($recaptcha);
    }

    /**
    *   Verifies the request reCaptcha token against the reCaptcha API
    *
    *   @return boolean
    */
    private function _verifyResponse($recaptchaResponse)
    {
        $data = http_build_query(array(
                'secret' => $this->_params->get('secret-key'),
                'response' => $recaptchaResponse
            )
        );

        $options = array('http' => array(
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => $data
            )
        );

        $context = stream_context_create($options);
        $response = file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
        $result = json_decode($response);

        return (bool) $result->success;
    }
}
