<?php

/**
 * Google reCaptcha system plugin for Anahita. This plugin validates the reCaptcha response.
 *
 * @category   Anahita
 *
 * @author     Nick Swinford <nick@nicholasjohn16.com>
 * @author     Rastin Mehr <rastin@anahita.io>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class plgSystemRecaptcha extends PlgAnahitaDefault
{
    private $_option;
    private $_view;
    private $_layout;
    private $_id;
    private $_viewer;

    /**
    *   Class constructor
    *   @param $dispatcher optional dispatcher object
    *   @param $config optional AnConfig object
    *
    *   @return void
    */
    public function __construct(AnConfig $config)
    {
        parent::__construct($config);

        $this->_option = AnRequest::get('get.option', 'string', '');
        $this->_view = AnRequest::get('get.view', 'cmd', '');
        $this->_layout = AnRequest::get('get.layout', 'cmd', '');
        $this->_id = AnRequest::get('get.id', 'int', 0);
        $this->_viewer = AnService::get('com:people.viewer');
    }

    /**
     * onAfterRender handler.
     */
    public function onAfterRoute(AnEvent $event)
    {
        if (AnRequest::method() === 'POST' && $this->_hasRecaptcha()) {
            $recaptchaResponse = AnRequest::get('post.g-recaptcha-response', 'string', null);
            if (! $this->_verifyResponse($recaptchaResponse)) {
                throw new AnException("Unauthorized Request", 403);
                return;
            }
        }
    }

    /**
     * onAfterRender handler.
     */
    public function onAfterDispatch(AnEvent $event)
    {

    }

    /**
     * onBeforeRender handler.
     */
    public function onBeforeRender(AnEvent $event)
    {
        if($this->_option == 'com_people' && $this->_viewer->guest()) {
            if (in_array($this->_view, array('session', 'person'))) {
                $this->_addScripts();
            }
        }

        if (
            $this->_option === 'com_groups' &&
            $this->_view === 'group' &&
            $this->_id === 0 &&
            in_array($this->_layout, array('add', 'form'))
        ) {
            $this->_addScripts();
        }
    }

    /**
     * onAfterRender handler.
     */
    public function onAfterRender(AnEvent $event)
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
        $action = AnRequest::get('post.action', 'cmd', 'add');

        if ($action == 'add' && $this->_id === 0) {
            if ($this->_option === 'com_people' && $this->_viewer->guest()) {
                if ($this->_view === 'session') {
                    return true;
                }
                if ($this->_view === 'person') {
                    return true;
                }
            }

            if ($this->_option === 'com_groups' && $this->_view === 'group') {
                return true;
            }
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
        $base = AnService::get('com:application')->getRouter()->getBaseUrl();
        $recaptcha = $base.'/media/plg_recaptcha/js/';
        $recaptcha .= ANDEBUG ? 'recaptcha.js' : 'min/recaptcha.min.js';

        $document = AnService::get('anahita:document');
        $jsDeclaration = "
            var recaptcha = null;
            $(document).ready(function(){
                if( $('form.recaptcha').length ) {
                    recaptcha = $('form.recaptcha').recaptcha({
                        siteKey: \"%s\"
                    });
                }
            });
            function recaptchaCallback(token) {
                recaptcha.recaptcha('recaptchaCallback', token)
            }
        ";

        $document->addScriptDeclaration(sprintf($jsDeclaration, $this->_params->get('site-key')));
        $document->addScript($recaptcha);
        $document->addScript($api, 'text/javascript', array('async', 'defer'));
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
